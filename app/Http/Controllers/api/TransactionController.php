<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customers;
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
    public function index()
    {
        $data = Order::with(
            'detail.variant.product',
            'customer.member',
            'userId',
            'payment'
        )->get();

        return view('transaction.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customer = Customers::with('member')->get();
        $product = Product::with('variant')->get();
        $category = Category::get();

        return view('transaction.form', compact('customer', 'product', 'category'));
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
                // return redirect()
                //     ->back()
                //     ->withErrors($validate)
                //     ->withInput()
                //     ->with('error', 'Please check the form and try again.');

                // return response()->json(['data', $validate->errors()], 500);
            }

            DB::beginTransaction();

            // Get customer and membership info for discount validation
            $customer = Customers::with('member')->find($request->customer_id);
            $membershipDiscount = $customer->is_member ? ($customer->member->discount ?? 0) : 0;

            // Validate if discount applied matches membership discount
            $calculatedDiscount = $request->totalAmount * ($membershipDiscount / 100);
            if (abs($request->discount - $calculatedDiscount) > 0.01) { // Allow small floating point difference
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

            // Get current user (assuming you have authentication)
            $userId = auth()->guard('web')->id() ?? 1; // Fallback to 1 if no auth

            // Create Order
            $order = Order::create([
                'user_id' => $userId,
                'customer_id' => $request->customer_id,
                'order_date' => now(),
                'total_amount' => $request->totalAfterDiscount,
                'discount' => $request->discount,
                'status' => 'completed',
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

                // Update stock
                $variant->decrement('stock_qty', $item['quantity']);
            }

            // Create Payment Record
            $payment = Payment::create([
                'order_id' => $order->order_id,
                'payment_method' => $request->payment_method,
                'amount' => $request->payment,
                'change' => $request->change,
                'currency' => 'IDR',
                'status' => $request->payment_method === 'midtrans' ? 'pending' : 'completed',
                'payment_date' => now(),
                'created_by' => $userId
            ]);

            // If payment method is Midtrans, handle payment gateway integration
            if ($request->payment_method === 'midtrans') {
                $snapToken = $this->generateMidtransSnapToken($order, $payment);

                $payment->update([
                    'snap_token' => $snapToken,
                    'token_expires_at' => now()->addHours(2), // Token expires in 2 hours
                    'status' => 'pending'
                ]);

                // You might want to redirect to Midtrans payment page here
                // or handle it differently based on your frontend setup
            }

            DB::commit();

            return redirect()
                ->route('transaction.index')
                ->with('success', 'Transaction completed successfully! Order ID: ' . $order->order_id);

        } catch (Exception $e) {
            DB::rollBack();

            // return redirect()
            //     ->back()
            //     ->withInput()
            //     ->with('error', 'Transaction failed: ' . $e->getMessage());

            return response()->json(['errors' => $e->getMessage()],500);
        }
    }

    /**
     * Generate Midtrans Snap Token
     */
    private function generateMidtransSnapToken(Order $order, Payment $payment)
    {
        // Configure Midtrans
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Prepare transaction details
        $transactionDetails = [
            'order_id' => $order->order_id . '-' . time(),
            'gross_amount' => (int) $payment->amount,
        ];

        // Prepare customer details
        $customer = $order->customer;
        $customerDetails = [
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name ?? '',
            'email' => $customer->email,
            'phone' => $customer->phone,
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
                'price' => (int) -$order->discount, // Negative price for discount
                'quantity' => 1,
                'name' => 'Member Discount',
            ];
        }

        // Prepare transaction data
        $transactionData = [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($transactionData);
            return $snapToken;
        } catch (Exception $e) {
            throw new Exception('Failed to generate Midtrans token: ' . $e->getMessage());
        }
    }

    /**
     * Midtrans Payment Notification Handler (Webhook)
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

            // Extract original order ID (remove timestamp suffix)
            $originalOrderId = explode('-', $orderId)[0];

            $order = Order::find($originalOrderId);
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $payment = Payment::where('order_id', $order->order_id)->first();

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $payment->status = 'challenge';
                } else if ($fraudStatus == 'accept') {
                    $payment->status = 'completed';
                    $order->status = 'completed';
                }
            } else if ($transactionStatus == 'settlement') {
                $payment->status = 'completed';
                $order->status = 'completed';
            } else if ($transactionStatus == 'pending') {
                $payment->status = 'pending';
            } else if (
                $transactionStatus == 'deny' ||
                $transactionStatus == 'expire' ||
                $transactionStatus == 'cancel'
            ) {
                $payment->status = 'failed';
                $order->status = 'cancelled';

                // Restore stock if payment failed
                foreach ($order->detail as $detail) {
                    $variant = ProductVariant::find($detail->variant_id);
                    if ($variant) {
                        $variant->increment('stock_qty', $detail->quantity);
                    }
                }
            }

            $payment->save();
            $order->save();

            return response()->json(['message' => 'Notification processed successfully']);

        } catch (Exception $e) {
            return response()->json(['message' => 'Error processing notification: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with('detail.variant.product', 'customer.member', 'payment')
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