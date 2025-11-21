<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customers;
use App\Models\Membership;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer.member', 'userId', 'payment']);

        // Apply filters
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        if ($request->has('payment_method') && $request->payment_method) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('order_date', 'desc')->get();

        return view('transaction.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customer = Customers::with('member')->get();
        $product = Product::with('variant')->get();
        $membership = Membership::get();
        $category = Category::get();

        return view('transaction.form', compact('customer', 'product', 'category', 'membership'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,customer_id',
                'payment_method' => 'required|string|in:cash,midtrans',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,product_id',
                'items.*.variant_id' => 'required|exists:product_variants,variant_id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'totalAmount' => 'required|numeric|min:0',
                'discount' => 'required|numeric|min:0',
                'totalAfterDiscount' => 'required|numeric|min:0',
                'payment' => 'required|numeric|min:0',
                'change' => 'required|numeric|min:0'
            ], [
                'items.required' => 'Please add at least one product to the transaction.',
                'items.*.product_id.required' => 'Product is required for all items.',
                'items.*.variant_id.required' => 'Variant is required for all items.',
                'items.*.quantity.required' => 'Quantity is required for all items.',
                'items.*.quantity.min' => 'Quantity must be at least 1.',
                'payment.min' => 'Payment amount cannot be negative.'
            ]);

            if ($validate->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validate)
                    ->withInput()
                    ->with('error', 'Please check the form and try again.');
            }

            DB::beginTransaction();

            // Get customer and membership info for discount validation
            $customer = Customers::with('member')->find($request->customer_id);
            $membershipDiscount = $customer->is_member ? ($customer->member->discount ?? 0) : 0;

            // Validate if discount applied matches membership discount
            $calculatedDiscount = $request->totalAmount * ($membershipDiscount / 100);
            if (abs($request->discount - $calculatedDiscount) > 0.01) {
                throw new Exception("Discount calculation mismatch. Expected: {$calculatedDiscount}, Received: {$request->discount}");
            }

            // Check stock availability for all items
            foreach ($request->items as $item) {
                $variant = ProductVariant::find($item['variant_id']);
                if (!$variant) {
                    throw new Exception("Product variant not found.");
                }

                if ($variant->stock_qty < $item['quantity']) {
                    throw new Exception("Insufficient stock for {$variant->variant_name}. Available: {$variant->stock_qty}, Requested: {$item['quantity']}");
                }
            }

            // Get current user
            $userId = auth()->guard('web')->id() ?? 1;

            // Create Order
            $order = Order::create([
                'user_id' => $userId,
                'customer_id' => $request->customer_id,
                'order_date' => now(),
                'total_amount' => $request->totalAmount,
                'discount' => $request->discount,
                'status' => $request->payment_method === 'midtrans' ? 'pending' : 'completed',
                'created_by' => $userId,
                'created_at' => now()
            ]);

            // Create Order Details and Update Stock
            foreach ($request->items as $item) {
                $variant = ProductVariant::find($item['variant_id']);
                $subtotal = $item['price'] * $item['quantity'];

                // Create Order Detail
                OrderDetail::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                    'total_price' => $subtotal
                ]);

                // Update stock only for cash payments immediately
                // For midtrans, stock will be updated after payment confirmation
                if ($request->payment_method === 'cash') {
                    $variant->decrement('stock_qty', $item['quantity']);
                }
            }

            // Create Payment Record
            $paymentData = [
                'order_id' => $order->order_id,
                'payment_method' => $request->payment_method,
                'amount' => $request->payment_method === 'midtrans' ? $request->totalAfterDiscount : $request->payment,
                'change' => $request->change,
                'currency' => 'IDR',
                'status' => $request->payment_method === 'midtrans' ? 'pending' : 'completed',
                'payment_date' => $request->payment_method === 'cash' ? now() : null,
                'created_by' => $userId
            ];

            // If payment method is Midtrans, generate snap token
            if ($request->payment_method === 'midtrans') {
                $snapToken = $this->generateMidtransSnapToken($order, $request->totalAfterDiscount);

                $paymentData['snap_token'] = $snapToken;
                $paymentData['token_expires_at'] = now()->addHours(2);
            }

            $payment = Payment::create($paymentData);

            DB::commit();

            // Redirect based on payment method
            if ($request->payment_method === 'midtrans') {
                return redirect()->route('transaction.payment', ['order' => $order->order_id])
                    ->with('success', 'Order created successfully. Please complete the payment.');
            }

            return redirect()
                ->route('transaction.index')
                ->with('success', 'Transaction completed successfully! Order ID: ' . $order->order_id);

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate Midtrans Snap Token
     */
    private function generateMidtransSnapToken(Order $order, $totalAmount)
    {
        // Configure Midtrans
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Prepare transaction details
        $transactionDetails = [
            'order_id' => 'ORDER-' . $order->order_id . '-' . time(),
            'gross_amount' => (int) $totalAmount,
        ];

        // Prepare customer details
        $customer = $order->customer;
        $customerDetails = [
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name ?? '',
            'email' => $customer->email,
            'phone' => $customer->phone,
            'billing_address' => [
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name ?? '',
                'address' => $customer->alamat,
                'phone' => $customer->phone,
            ],
            'shipping_address' => [
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name ?? '',
                'address' => $customer->alamat,
                'phone' => $customer->phone,
            ]
        ];

        // Prepare item details
        $itemDetails = [];
        foreach ($order->detail as $detail) {
            $itemDetails[] = [
                'id' => $detail->variant_id,
                'price' => (int) $detail->price_at_purchase,
                'quantity' => (int) $detail->quantity,
                'name' => $detail->product->product_name . ' - ' . $detail->variant->variant_name,
            ];
        }

        // Add discount item if applicable
        if ($order->discount > 0) {
            $itemDetails[] = [
                'id' => 'DISCOUNT',
                'price' => (int) -$order->discount,
                'quantity' => 1,
                'name' => 'Member Discount',
            ];
        }

        // Prepare transaction data
        $transactionData = [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
            'callbacks' => [
                'finish' => route('transaction.payment.callback'),
            ]
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($transactionData);
            return $snapToken;
        } catch (Exception $e) {
            throw new Exception('Failed to generate Midtrans token: ' . $e->getMessage());
        }
    }

    /**
     * Show payment page for Midtrans
     */
    public function showPayment($orderId)
    {
        $order = Order::with(['customer', 'detail.variant.product', 'payment'])
            ->findOrFail($orderId);

        if ($order->payment->status === 'completed') {
            return redirect()->route('transaction.show', $orderId)
                ->with('info', 'Payment already completed.');
        }

        if ($order->payment->status !== 'pending') {
            return redirect()->route('transaction.show', $orderId)
                ->with('error', 'Invalid payment status.');
        }

        return view('transaction.payment', compact('order'));
    }

    /**
     * Handle Midtrans payment callback
     */
    public function paymentCallback(Request $request)
    {
        try {
            $orderId = $request->order_id;
            $statusCode = $request->status_code;
            $grossAmount = $request->gross_amount;
            $transactionStatus = $request->transaction_status;
            $fraudStatus = $request->fraud_status;

            // Extract original order ID
            $originalOrderId = explode('-', $orderId)[1]; // ORDER-{order_id}-timestamp

            $order = Order::with('payment', 'detail.variant')->find($originalOrderId);
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $payment = $order->payment;

            DB::beginTransaction();

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $payment->status = 'challenge';
                    $order->status = 'pending';
                } else if ($fraudStatus == 'accept') {
                    $payment->status = 'completed';
                    $order->status = 'completed';
                    $payment->payment_date = now();

                    // Update stock for completed payment
                    foreach ($order->detail as $detail) {
                        $variant = $detail->variant;
                        if ($variant) {
                            $variant->decrement('stock_qty', $detail->quantity);
                        }
                    }
                }
            } else if ($transactionStatus == 'settlement') {
                $payment->status = 'completed';
                $order->status = 'completed';
                $payment->payment_date = now();

                // Update stock for completed payment
                foreach ($order->detail as $detail) {
                    $variant = $detail->variant;
                    if ($variant) {
                        $variant->decrement('stock_qty', $detail->quantity);
                    }
                }
            } else if ($transactionStatus == 'pending') {
                $payment->status = 'pending';
                $order->status = 'pending';
            } else if (
                $transactionStatus == 'deny' ||
                $transactionStatus == 'expire' ||
                $transactionStatus == 'cancel'
            ) {
                $payment->status = 'failed';
                $order->status = 'cancelled';
            }

            $payment->save();
            $order->save();

            DB::commit();

            // Redirect to order detail page
            return redirect()->route('transaction.show', $order->order_id)
                ->with('success', 'Payment status updated: ' . $transactionStatus);

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('transaction.index')
                ->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle Midtrans notification (webhook)
     */
    public function midtransNotification(Request $request)
    {
        try {
            $notification = new \Midtrans\Notification();

            $orderId = $notification->order_id;
            $statusCode = $notification->status_code;
            $grossAmount = $notification->gross_amount;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;

            // Extract original order ID
            $originalOrderId = explode('-', $orderId)[1];

            $order = Order::with('payment', 'detail.variant')->find($originalOrderId);
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $payment = $order->payment;

            DB::beginTransaction();

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $payment->status = 'challenge';
                    $order->status = 'pending';
                } else if ($fraudStatus == 'accept') {
                    $payment->status = 'completed';
                    $order->status = 'completed';
                    $payment->payment_date = now();

                    // Update stock
                    foreach ($order->detail as $detail) {
                        $variant = $detail->variant;
                        if ($variant) {
                            $variant->decrement('stock_qty', $detail->quantity);
                        }
                    }
                }
            } else if ($transactionStatus == 'settlement') {
                $payment->status = 'completed';
                $order->status = 'completed';
                $payment->payment_date = now();

                // Update stock
                foreach ($order->detail as $detail) {
                    $variant = $detail->variant;
                    if ($variant) {
                        $variant->decrement('stock_qty', $detail->quantity);
                    }
                }
            } else if ($transactionStatus == 'pending') {
                $payment->status = 'pending';
                $order->status = 'pending';
            } else if (
                $transactionStatus == 'deny' ||
                $transactionStatus == 'expire' ||
                $transactionStatus == 'cancel'
            ) {
                $payment->status = 'failed';
                $order->status = 'cancelled';
            }

            $payment->save();
            $order->save();

            DB::commit();

            return response()->json(['message' => 'Notification processed successfully']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error processing notification: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($orderId)
    {
        $order = Order::with('payment')->findOrFail($orderId);

        return response()->json([
            'status' => $order->payment->status,
            'snap_token' => $order->payment->snap_token
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with([
            'customer.member',
            'userCreator',
            'detail.variant.product.category',
            'payment'
        ])
            ->findOrFail($id);

        return view('transaction.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * API Methods for Form
     */
    public function getCustomers()
    {
        return Customers::with('member')->get();
    }

    public function getProductsByCategory($categoryId)
    {
        return Product::where('category_id', $categoryId)->get();
    }

    public function getVariantsByProduct($productId)
    {
        return ProductVariant::where('product_id', $productId)->get();
    }
}