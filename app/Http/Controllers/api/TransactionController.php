<?php

namespace App\Http\Controllers\Api;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                'change' => 'required|numeric|min:0',
            ], [
                'items.required' => 'Please add at least one product to the transaction.',
                'items.*.product_id.required' => 'Product is required for all items.',
                'items.*.variant_id.required' => 'Variant is required for all items.',
                'items.*.quantity.required' => 'Quantity is required for all items.',
                'items.*.quantity.min' => 'Quantity must be at least 1.',
                'payment.min' => 'Payment amount cannot be negative.',
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
                if (! $variant) {
                    throw new Exception('Product variant not found.');
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
                'created_at' => now(),
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
                    'total_price' => $subtotal,
                ]);

                // Update stock only for cash payments immediately
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
                'created_by' => $userId,
            ];

            // If payment method is Midtrans, generate snap token
            if ($request->payment_method === 'midtrans') {
                $midtransData = $this->generateMidtransSnapToken($order, $request->totalAfterDiscount);

                // Add token data to payment data
                $paymentData['snap_token'] = $midtransData['snap_token'];
                $paymentData['token_expires_at'] = now()->addHours(2);
                $paymentData['previous_tokens'] = $midtransData['midtrans_order_id']; // Store Midtrans order ID
            }

            $payment = Payment::create($paymentData);

            DB::commit();

            // Redirect based on payment method
            if ($request->payment_method === 'midtrans') {
                return redirect()->route('transaction.payment', ['order' => $order->order_id])
                    ->with('success', 'Order created successfully. Please complete the payment.');
            }

            return redirect()
                ->route('transaction.show', $order->order_id)
                ->with('success', 'Transaction completed successfully! Order ID: '.$order->order_id);

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Transaction failed: '.$e->getMessage());
        }
    }

    /**
     * Generate Midtrans Snap Token - Return array with token and order ID
     */
    private function generateMidtransSnapToken(Order $order, $totalAmount)
    {
        // Configure Midtrans
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Create unique order ID for Midtrans
        $midtransOrderId = 'ORDER-'.$order->order_id.'-'.time();

        $transactionDetails = [
            'order_id' => $midtransOrderId,
            'gross_amount' => (int) $totalAmount,
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
                'name' => $detail->product->product_name.' - '.$detail->variant->variant_name,
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
                'finish' => route('transaction.payment.finish', $order->order_id),
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($transactionData);

            Log::info('Midtrans token generated', [
                'order_id' => $order->order_id,
                'midtrans_order_id' => $midtransOrderId,
                'amount' => $totalAmount,
            ]);

            return [
                'snap_token' => $snapToken,
                'midtrans_order_id' => $midtransOrderId,
            ];
        } catch (Exception $e) {
            Log::error('Failed to generate Midtrans token: '.$e->getMessage());
            throw new Exception('Failed to generate Midtrans token: '.$e->getMessage());
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
     * Handle payment finish callback from Midtrans
     */
    public function paymentFinish($orderId)
    {
        try {
            $order = Order::with('payment', 'detail.variant')->findOrFail($orderId);

            // Check if payment is already completed
            if ($order->payment->status === 'completed') {
                return redirect()->route('transaction.show', $orderId)
                    ->with('success', 'Payment already completed.');
            }

            // For safety, check payment status with Midtrans API
            $this->checkMidtransPaymentStatus($order);

            // Reload order data
            $order->refresh();

            if ($order->payment->status === 'completed') {
                return redirect()->route('transaction.show', $orderId)
                    ->with('success', 'Payment completed successfully!');
            } else {
                return redirect()->route('transaction.show', $orderId)
                    ->with('warning', 'Payment is still pending. Please wait for confirmation or contact support.');
            }

        } catch (Exception $e) {
            Log::error('Error in paymentFinish: '.$e->getMessage());

            return redirect()->route('transaction.show', $orderId)
                ->with('error', 'Error checking payment status: '.$e->getMessage());
        }
    }

    /**
     * Check Midtrans payment status via API
     */
    private function checkMidtransPaymentStatus(Order $order)
    {
        try {
            // Get Midtrans order ID from previous_tokens field
            $midtransOrderId = $order->payment->previous_tokens;

            Log::info('Checking Midtrans status:', [
                'order_id' => $order->order_id,
                'midtrans_order_id' => $midtransOrderId,
            ]);

            if (! $midtransOrderId) {
                Log::warning('No Midtrans order ID found for order: '.$order->order_id);

                return;
            }

            // Configure Midtrans
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // Check status via Midtrans API
            Log::info('Calling Midtrans Transaction::status for: '.$midtransOrderId);
            $status = \Midtrans\Transaction::status($midtransOrderId);

            // Log::info('Midtrans API Response:', [
            //     'order_id' => $order->order_id,
            //     'midtrans_order_id' => $midtransOrderId,
            //     'transaction_status' => $status->transaction_status,
            //     'fraud_status' => $status->fraud_status ?? null,
            //     'status_code' => $status->status_code ?? null,
            // ]);

            $this->updatePaymentStatusFromMidtrans($order, $status);

        } catch (Exception $e) {
            Log::error('Error checking Midtrans status for order '.$order->order_id.': '.$e->getMessage());
            throw new Exception('Midtrans API error: '.$e->getMessage());
        }
    }

    /**
     * Update payment status based on Midtrans response
     */
    private function updatePaymentStatusFromMidtrans(Order $order, $status)
    {
        DB::beginTransaction();

        try {
            $payment = $order->payment;
            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status ?? null;

            Log::info('Updating payment status from Midtrans:', [
                'order_id' => $order->order_id,
                'current_status' => $payment->status,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
            ]);

            // If already completed, do nothing
            if ($payment->status === 'completed') {
                Log::info('Payment already completed, skipping update');
                DB::commit();

                return;
            }

            $stockUpdated = false;
            $previousStatus = $payment->status;

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $payment->status = 'challenge';
                    $order->status = 'pending';
                    Log::info('Status updated to challenge');
                } elseif ($fraudStatus == 'accept') {
                    $payment->status = 'completed';
                    $order->status = 'completed';
                    $payment->payment_date = now();
                    $stockUpdated = true;
                    Log::info('Status updated to completed (capture)');
                }
            } elseif ($transactionStatus == 'settlement') {
                $payment->status = 'completed';
                $order->status = 'completed';
                $payment->payment_date = now();
                $stockUpdated = true;
                Log::info('Status updated to completed (settlement)');
            } elseif ($transactionStatus == 'pending') {
                $payment->status = 'pending';
                $order->status = 'pending';
                Log::info('Status remains pending');
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $payment->status = 'failed';
                $order->status = 'cancelled';
                Log::info('Status updated to failed: '.$transactionStatus);
            }

            // Update stock only if payment completed and not previously updated
            if ($stockUpdated && $previousStatus !== 'completed') {
                Log::info('Updating stock for completed payment');
                foreach ($order->detail as $detail) {
                    $variant = ProductVariant::find($detail->variant_id);
                    if ($variant) {
                        if ($variant->stock_qty >= $detail->quantity) {
                            $oldStock = $variant->stock_qty;
                            $variant->decrement('stock_qty', $detail->quantity);
                            Log::info("Stock updated - Variant: {$variant->variant_id}, Old: {$oldStock}, New: {$variant->stock_qty}");
                        } else {
                            Log::error("Insufficient stock for variant: {$variant->variant_id}");
                        }
                    }
                }
            }

            $payment->save();
            $order->save();

            DB::commit();

            Log::info('Payment status update completed', [
                'order_id' => $order->order_id,
                'previous_status' => $previousStatus,
                'new_status' => $payment->status,
                'stock_updated' => $stockUpdated,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating payment status: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle Midtrans payment callback (webhook) - Support GET and POST
     */
    public function paymentCallback(Request $request)
    {
        Log::info('Midtrans Callback Received:', $request->all());

        try {
            // Handle both GET and POST parameters
            $orderId = $request->get('order_id') ?? $request->input('order_id');
            $transactionStatus = $request->get('transaction_status') ?? $request->input('transaction_status');
            $fraudStatus = $request->get('fraud_status') ?? $request->input('fraud_status');
            $statusCode = $request->get('status_code') ?? $request->input('status_code');

            Log::info('Parsed Callback Data:', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'status_code' => $statusCode,
                'method' => $request->method(),
            ]);

            if (! $orderId || ! $transactionStatus) {
                Log::error('Missing required callback parameters');

                return response()->json(['message' => 'Missing parameters'], 400);
            }

            // Find order by Midtrans order ID stored in previous_tokens
            $payment = Payment::where('previous_tokens', $orderId)->first();

            if (! $payment) {
                Log::error('Payment not found for Midtrans order ID: '.$orderId);

                // Try alternative: extract from order ID format ORDER-8-1763815426
                if (strpos($orderId, 'ORDER-') === 0) {
                    $parts = explode('-', $orderId);
                    if (count($parts) >= 2 && is_numeric($parts[1])) {
                        $originalOrderId = $parts[1];
                        $payment = Payment::where('order_id', $originalOrderId)->first();
                        Log::info('Trying alternative lookup with order ID: '.$originalOrderId);
                    }
                }

                if (! $payment) {
                    return response()->json(['message' => 'Payment not found'], 404);
                }
            }

            $order = $payment->order;
            if (! $order) {
                Log::error('Order not found for payment: '.$payment->payment_id);

                return response()->json(['message' => 'Order not found'], 404);
            }

            DB::beginTransaction();

            // Check if already processed
            if ($payment->status === 'completed') {
                DB::commit();
                Log::info('Payment already completed for order: '.$order->order_id);

                return response()->json(['message' => 'Payment already processed']);
            }

            Log::info('Processing payment callback', [
                'order_id' => $order->order_id,
                'previous_status' => $payment->status,
                'new_transaction_status' => $transactionStatus,
            ]);

            $stockUpdated = false;

            // PROCESS PAYMENT STATUS
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $payment->status = 'challenge';
                    $order->status = 'pending';
                    Log::info('Payment challenged for fraud review');
                } elseif ($fraudStatus == 'accept') {
                    $payment->status = 'completed';
                    $order->status = 'completed';
                    $payment->payment_date = now();
                    $stockUpdated = true;
                    Log::info('Payment captured and accepted');
                }
            } elseif ($transactionStatus == 'settlement') {
                $payment->status = 'completed';
                $order->status = 'completed';
                $payment->payment_date = now();
                $stockUpdated = true;
                Log::info('Payment settled');
            } elseif ($transactionStatus == 'pending') {
                $payment->status = 'pending';
                $order->status = 'pending';
                Log::info('Payment pending');
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $payment->status = 'failed';
                $order->status = 'cancelled';
                Log::info('Payment failed or cancelled: '.$transactionStatus);
            }

            // Update stock only if payment completed
            if ($stockUpdated) {
                Log::info('Updating stock for order: '.$order->order_id);
                foreach ($order->detail as $detail) {
                    $variant = ProductVariant::find($detail->variant_id);
                    if ($variant) {
                        if ($variant->stock_qty >= $detail->quantity) {
                            $oldStock = $variant->stock_qty;
                            $variant->decrement('stock_qty', $detail->quantity);
                            Log::info("Stock updated - Variant: {$variant->variant_id}, Old: {$oldStock}, New: {$variant->stock_qty}, Reduced: {$detail->quantity}");
                        } else {
                            Log::error("Insufficient stock for variant: {$variant->variant_id}, Available: {$variant->stock_qty}, Required: {$detail->quantity}");
                        }
                    }
                }
            }

            $payment->save();
            $order->save();

            DB::commit();

            Log::info('Payment callback processed successfully', [
                'order_id' => $order->order_id,
                'payment_status' => $payment->status,
                'order_status' => $order->status,
                'stock_updated' => $stockUpdated,
            ]);

            // Return success response
            if ($request->isMethod('GET')) {
                // If it's a GET request (from browser redirect), redirect to order page
                return redirect()->route('transaction.show', $order->order_id)
                    ->with('success', 'Payment status updated: '.$transactionStatus);
            } else {
                // If it's a POST request (from webhook), return JSON
                return response()->json([
                    'message' => 'Callback processed successfully',
                    'order_id' => $order->order_id,
                    'status' => $payment->status,
                ]);
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error processing payment callback: '.$e->getMessage());

            if ($request->isMethod('GET')) {
                return redirect()->route('transaction.index')
                    ->with('error', 'Error processing payment: '.$e->getMessage());
            } else {
                return response()->json(['message' => 'Error processing callback'], 500);
            }
        }
    }

    /**
     * Check payment status manually
     */
    public function checkPaymentStatus($orderId)
    {
        try {
            Log::info('Manual status check requested for order: '.$orderId);

            $order = Order::with('payment')->findOrFail($orderId);

            Log::info('Current payment status:', [
                'order_id' => $order->order_id,
                'current_status' => $order->payment->status,
                'payment_method' => $order->payment->payment_method,
            ]);

            // If not pending, return current status
            if ($order->payment->status !== 'pending') {
                Log::info('Payment already processed, returning current status');

                return response()->json([
                    'status' => $order->payment->status,
                    'message' => 'Payment status: '.$order->payment->status,
                ]);
            }

            // Only check with Midtrans API for midtrans payments
            if ($order->payment->payment_method === 'midtrans') {
                Log::info('Checking Midtrans API for order: '.$orderId);
                $this->checkMidtransPaymentStatus($order);

                // Reload data
                $order->refresh();

                Log::info('After Midtrans API check:', [
                    'order_id' => $order->order_id,
                    'new_status' => $order->payment->status,
                ]);
            }

            return response()->json([
                'status' => $order->payment->status,
                'message' => $order->payment->status === 'pending'
                    ? 'Payment is still pending. Please wait for confirmation.'
                    : 'Payment status updated: '.$order->payment->status,
            ]);

        } catch (Exception $e) {
            Log::error('Error checking payment status for order '.$orderId.': '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error checking payment status: '.$e->getMessage(),
            ], 500);
        }
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
            'payment',
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
