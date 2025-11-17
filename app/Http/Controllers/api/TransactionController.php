<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customers;
use App\Models\Membership;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Midtrans\Transaction;
use Carbon\Carbon;

class TransactionController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Order::with(
            'detail.product',
            'customer.member',
            'payment',
            'userId'
        )->get();

        return view('transaction.index', compact('data'));
    }

    public function create()
    {
        $customer = Customers::get();
        $product = Product::with('variant', 'category', 'brand')->get();
        $variant = ProductVariant::get();
        $categories = Category::get();
        $membership = Membership::get();

        return view('transaction.form', compact(
            'customer',
            'product',
            'variant',
            'categories',
            'membership'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Decode product input
            $item = $request->product;
            if (is_string($item)) {
                $item = json_decode($item, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json(['error' => 'Invalid product JSON'], 422);
                }
            }

            // Validation
            $validate = Validator::make(
                array_merge($request->all(), ['product' => $item]),
                [
                    'customer_id' => 'nullable|exists:customers,customer_id',
                    'payment_method' => 'required|string|max:50|in:Cash,Credit Card,Transfer,Midtrans',
                    'currency' => 'required|string|max:3|in:USD,EUR,IDR',
                    'product' => 'required|array|min:1',
                    'product.*.product_id' => 'required|exists:products,product_id',
                    'product.*.variant_id' => 'required|exists:product_variants,variant_id',
                    'product.*.qty' => 'required|integer|min:1',
                    'discount' => 'nullable|numeric|min:0',
                    'payment' => 'required|numeric|min:0',
                ]
            );

            if ($validate->fails()) {
                return back()
                    ->withErrors($validate->errors())
                    ->withInput();
            }

            // Authentication check
            $userId = Auth::guard('web')->id();
            if (!$userId) {
                return back()
                    ->with('error', 'Unauthorized. Please login again.')
                    ->withInput();
            }

            // Customer discount logic
            $discountPercent = 0;
            if ($request->filled('customer_id')) {
                $customer = Customers::with('member')->find($request->customer_id);
                if ($customer && $customer->is_member && $customer->member) {
                    $discountPercent = $customer->member->discount ?? 0;
                }
            }

            // Calculate totals
            $totalPrice = 0;
            $products = [];

            foreach ($item as $productData) {
                $variant = ProductVariant::with('product.category', 'product.brand')->find($productData['variant_id']);

                if (!$variant) {
                    throw new \Exception("Variant not found: {$productData['variant_id']}");
                }

                // Ensure stock is enough
                if ($variant->stock_qty < $productData['qty']) {
                    return back()
                        ->with('error', "Insufficient stock for variant {$variant->variant_name}")
                        ->withInput();
                }

                // Determine price
                $price = $variant->price ?? $variant->product->price ?? 0;
                $itemTotal = $price * $productData['qty'];
                $totalPrice += $itemTotal;

                $products[] = [
                    'product_id' => $variant->product_id,
                    'product_name' => $variant->product->product_name,
                    'variant_id' => $variant->variant_id,
                    'variant_name' => $variant->variant_name,
                    'quantity' => $productData['qty'],
                    'price' => $price,
                    'total' => $itemTotal,
                    'brand_name' => $variant->product->brand->name ?? 'N/A',
                    'category_name' => $variant->product->category->name ?? 'N/A',
                ];
            }

            // Apply discount
            $discountValue = ($totalPrice * $discountPercent / 100);
            $totalAfterDiscount = max(0, $totalPrice - $discountValue);

            // For Midtrans payments
            if ($request->payment_method === 'Midtrans') {
                $result = $this->handleMidtransPayment($request, $totalAfterDiscount, $products, $userId, $discountValue);
                DB::commit();
                return $result;
            }

            // For other payment methods
            $result = $this->handleRegularPayment($request, $totalAfterDiscount, $products, $userId, $discountValue);
            DB::commit();
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction creation error: ' . $e->getMessage());

            return back()
                ->with('error', 'An error occurred during transaction creation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Handle regular payment (Cash, Credit Card, Transfer)
     */
    protected function handleRegularPayment($request, $totalAfterDiscount, $products, $userId, $discountValue)
    {
        // Payment and change calculation
        $paymentAmount = $request->payment;
        $change = $paymentAmount - $totalAfterDiscount;

        if ($change < 0) {
            return back()
                ->with('error', 'Payment amount is insufficient')
                ->withInput();
        }

        // Create transaction
        $transaction = Order::create([
            'user_id' => $userId,
            'customer_id' => $request->customer_id,
            'total_amount' => $totalAfterDiscount,
            'discount' => $discountValue,
            'change' => $change,
            'order_date' => now(),
            'status' => 'completed',
            'created_by' => $userId,
        ]);

        // Create transaction details & update stock
        foreach ($products as $p) {
            OrderDetail::create([
                'order_id' => $transaction->order_id,
                'product_id' => $p['product_id'],
                'variant_id' => $p['variant_id'],
                'quantity' => $p['quantity'],
                'price_at_purchase' => $p['price'],
                'total_price' => $p['total'],
            ]);

            // Decrease stock
            ProductVariant::where('variant_id', $p['variant_id'])
                ->decrement('stock_qty', $p['quantity']);
        }

        // Record payment
        Payment::create([
            'order_id' => $transaction->order_id,
            'payment_method' => $request->payment_method,
            'amount' => $paymentAmount,
            'currency' => $request->currency,
            'status' => 'complete',
            'change' => $change,
            'payment_date' => now(),
            'created_by' => $userId,
        ]);

        return redirect()
            ->route('transaction.show', $transaction->order_id)
            ->with('success', 'Transaction successfully created!');
    }

    /**
     * Handle Midtrans payment with direct Snap redirect
     */
    protected function handleMidtransPayment($request, $totalAfterDiscount, $products, $userId, $discountValue)
    {
        try {
            // Create pending transaction first
            $transaction = Order::create([
                'user_id' => $userId,
                'customer_id' => $request->customer_id,
                'total_amount' => $totalAfterDiscount,
                'discount' => $discountValue,
                'change' => 0,
                'order_date' => now(),
                'status' => 'pending',
                'created_by' => $userId,
            ]);

            // Create transaction details (don't update stock yet)
            foreach ($products as $p) {
                OrderDetail::create([
                    'order_id' => $transaction->order_id,
                    'product_id' => $p['product_id'],
                    'variant_id' => $p['variant_id'],
                    'quantity' => $p['quantity'],
                    'price_at_purchase' => $p['price'],
                    'total_price' => $p['total'],
                ]);
            }

            // Prepare Midtrans transaction data
            $customer = Customers::find($request->customer_id);
            $midtransData = [
                'transaction_details' => [
                    'order_id' => (string) $transaction->order_id,
                    'gross_amount' => (int) $totalAfterDiscount,
                ],
                'item_details' => $this->prepareItemDetails($products),
                'customer_details' => [
                    'first_name' => $customer ? $customer->first_name : 'Customer',
                    'last_name' => $customer ? $customer->last_name : '',
                    'email' => $customer ? $customer->email : 'customer@example.com',
                    'phone' => $customer ? $customer->phone : '',
                ],
                'callbacks' => [
                    'finish' => route('transaction.midtrans.finish'),
                    'error' => route('transaction.midtrans.error'),
                    'pending' => route('transaction.midtrans.pending'),
                ]
            ];

            // Generate Snap token
            $snapToken = $this->midtransService->createSnapTransaction($midtransData);
            $tokenExpiresAt = now()->addHours(24);

            // Create payment record with Snap token
            Payment::create([
                'order_id' => $transaction->order_id,
                'payment_method' => 'Midtrans',
                'snap_token' => $snapToken,
                'token_expires_at' => $tokenExpiresAt,
                'amount' => $totalAfterDiscount,
                'currency' => $request->currency,
                'status' => 'pending',
                'change' => 0,
                'created_by' => $userId,
            ]);

            Log::info("Created Snap token for transaction {$transaction->order_id}: {$snapToken}");

            // Get Midtrans Snap URL and redirect directly to it
            $snapUrl = $this->midtransService->getSnapRedirectUrl($snapToken);

            Log::info("Redirecting to Midtrans Snap URL for transaction {$transaction->order_id}");

            // Direct redirect to Midtrans Snap page
            return redirect($snapUrl);

        } catch (\Exception $e) {
            // Rollback transaction if Midtrans fails
            if (isset($transaction)) {
                $transaction->delete();
            }

            return back()
                ->with('error', 'Failed to create Midtrans transaction: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show Snap payment page
     */
    public function showSnapPayment(Request $request)
    {
        Log::info("=== SNAP PAYMENT METHOD CALLED ===");

        try {
            $snapToken = $request->query('token');
            $orderId = $request->query('order_id');

            Log::info("Parameters received:", ['token' => $snapToken, 'order_id' => $orderId]);

            if (!$snapToken || !$orderId) {
                Log::error("Missing parameters");
                abort(400, 'Missing payment parameters');
            }

            Log::info("Looking for payment with token: {$snapToken} and order: {$orderId}");

            // Verify the token belongs to the order
            $payment = Payment::with('order.customer')
                ->where('snap_token', $snapToken)
                ->where('order_id', $orderId)
                ->first();

            if (!$payment) {
                Log::error("Payment not found in database");
                Log::error("Available payments with this token: " . Payment::where('snap_token', $snapToken)->count());
                Log::error("Available payments with this order: " . Payment::where('order_id', $orderId)->count());

                return redirect()
                    ->route('transaction.create')
                    ->with('error', 'Invalid payment session.');
            }

            Log::info("Payment found, rendering view...");

            return view('transaction.snap-payment', [
                'snapToken' => $snapToken,
                'orderId' => $orderId,
                'payment' => $payment,
                'transaction' => $payment->order
            ]);

        } catch (\Exception $e) {
            Log::error("Exception in showSnapPayment: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;

            // return redirect()
            //     ->route('transaction.create')
            //     ->with('error', 'Error loading payment page: ' . $e->getMessage());
        }
    }

    /**
     * Prepare item details for Midtrans
     */
    protected function prepareItemDetails($products)
    {
        $items = [];

        foreach ($products as $product) {
            $items[] = [
                'id' => $product['variant_id'],
                'price' => (int) $product['price'],
                'quantity' => (int) $product['quantity'],
                'name' => "{$product['product_name']} - {$product['variant_name']}",
            ];
        }

        return $items;
    }

    /**
     * Midtrans payment finish callback
     */
    public function midtransFinish(Request $request)
    {
        try {
            $orderId = $request->get('order_id');
            if (!$orderId) {
                return redirect()->route('transaction.index');
            }

            $transaction = Order::with('payment')->find($orderId);

            if (!$transaction) {
                return redirect()
                    ->route('transaction.index')
                    ->with('error', 'Transaction not found.');
            }

            return redirect()
                ->route('transaction.show', $orderId)
                ->with('info', 'Payment process completed. Waiting for confirmation...');

        } catch (\Exception $e) {
            return redirect()
                ->route('transaction.index')
                ->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    /**
     * Midtrans payment error callback
     */
    public function midtransError(Request $request)
    {
        $orderId = $request->get('order_id');

        if ($orderId) {
            Order::where('order_id', $orderId)->update(['status' => 'failed']);
            Payment::where('order_id', $orderId)->update(['status' => 'failed']);
        }

        return redirect()
            ->route('transaction.create')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Midtrans payment pending callback
     */
    public function midtransPending(Request $request)
    {
        $orderId = $request->get('order_id');

        if ($orderId) {
            return redirect()
                ->route('transaction.show', $orderId)
                ->with('warning', 'Payment is pending. Please complete your payment.');
        }

        return redirect()
            ->route('transaction.index')
            ->with('warning', 'Payment is pending.');
    }

    /**
     * Midtrans notification handler (webhook)
     */
    public function midtransNotification(Request $request)
    {
        Log::info('Midtrans notification received', $request->all());

        try {
            $notification = $request->all();

            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? null;

            $transaction = Order::with('payment', 'detail')->find($orderId);

            if (!$transaction) {
                Log::error("Transaction {$orderId} not found");
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            Log::info("Updating transaction {$orderId} to status: {$transactionStatus}");

            $statusUpdated = false;

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    // Payment completed
                    $transaction->update(['status' => 'completed']);
                    $transaction->payment->update([
                        'status' => 'complete',
                        'payment_date' => now(),
                    ]);

                    // Update stock only when payment is complete
                    foreach ($transaction->detail as $detail) {
                        ProductVariant::where('variant_id', $detail->variant_id)
                            ->decrement('stock_qty', $detail->quantity);
                    }
                    $statusUpdated = true;
                    Log::info("Transaction {$orderId} completed successfully");
                }
            } elseif ($transactionStatus == 'settlement') {
                // Payment completed
                $transaction->update(['status' => 'completed']);
                $transaction->payment->update([
                    'status' => 'complete',
                    'payment_date' => now(),
                ]);

                // Update stock only when payment is complete
                foreach ($transaction->detail as $detail) {
                    ProductVariant::where('variant_id', $detail->variant_id)
                        ->decrement('stock_qty', $detail->quantity);
                }
                $statusUpdated = true;
                Log::info("Transaction {$orderId} settled successfully");
            } elseif ($transactionStatus == 'pending') {
                $transaction->update(['status' => 'pending']);
                $transaction->payment->update(['status' => 'pending']);
                $statusUpdated = true;
                Log::info("Transaction {$orderId} is pending");
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $transaction->update(['status' => 'failed']);
                $transaction->payment->update(['status' => 'failed']);
                $statusUpdated = true;
                Log::info("Transaction {$orderId} failed with status: {$transactionStatus}");
            }

            return response()->json([
                'status' => 'success',
                'updated' => $statusUpdated,
                'current_status' => $transaction->fresh()->status
            ]);

        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaction = Order::with([
            'detail.product.variant',
            'customer.member',
            'payment',
            'userId.role'
        ])->findOrFail($id);

        return view('transaction.show', compact('transaction'));
    }

    /**
     * Regenerate Snap token and redirect to Midtrans payment page
     */
    public function retryPayment($id)
    {
        DB::beginTransaction();

        try {
            $transaction = Order::with('payment', 'detail', 'customer')->findOrFail($id);

            // Only allow retry for failed or pending Midtrans payments
            if ($transaction->payment->payment_method !== 'Midtrans') {
                return redirect()
                    ->route('transaction.show', $id)
                    ->with('error', 'This payment method does not support retry.');
            }

            if (!in_array($transaction->status, ['pending', 'failed'])) {
                return redirect()
                    ->route('transaction.show', $id)
                    ->with('error', 'Cannot retry payment for completed transactions.');
            }

            // Check if transaction is too old
            $transactionDate = Carbon::parse($transaction->order_date);
            $maxRetryDays = 1;
            if ($transactionDate->diffInDays(now()) > $maxRetryDays) {
                return redirect()
                    ->route('transaction.show', $id)
                    ->with('error', 'This transaction is too old to retry. Please create a new transaction.');
            }

            // Prepare Midtrans transaction data (same order ID, different token)
            $midtransData = [
                'transaction_details' => [
                    'order_id' => (string) $transaction->order_id,
                    'gross_amount' => (int) $transaction->total_amount,
                ],
                'item_details' => $this->prepareRetryItemDetails($transaction->detail),
                'customer_details' => [
                    'first_name' => $transaction->customer ? $transaction->customer->first_name : 'Customer',
                    'last_name' => $transaction->customer ? $transaction->customer->last_name : '',
                    'email' => $transaction->customer ? $transaction->customer->email : 'customer@example.com',
                    'phone' => $transaction->customer ? $transaction->customer->phone : '',
                ],
                'callbacks' => [
                    'finish' => route('transaction.midtrans.finish'),
                    'error' => route('transaction.midtrans.error'),
                    'pending' => route('transaction.midtrans.pending'),
                ]
            ];

            // Generate NEW Snap token
            $newSnapToken = $this->midtransService->createSnapTransaction($midtransData);
            $tokenExpiresAt = now()->addHours(24);

            // Update payment with new token
            $transaction->payment->update([
                'snap_token' => $newSnapToken,
                'token_expires_at' => $tokenExpiresAt,
                'status' => 'pending',
            ]);

            // Reset transaction status if it was failed
            if ($transaction->status === 'failed') {
                $transaction->update(['status' => 'pending']);
            }

            Log::info("Regenerated Snap token for transaction {$transaction->order_id}: {$newSnapToken}");

            // Get Midtrans Snap URL and redirect directly to it
            $snapUrl = $this->midtransService->getSnapRedirectUrl($newSnapToken);

            Log::info("Redirecting to Midtrans Snap page for retry: {$snapUrl}");

            DB::commit();

            return redirect($snapUrl);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error retrying payment for transaction {$id}: " . $e->getMessage());

            return redirect()
                ->route('transaction.show', $id)
                ->with('error', 'Failed to retry payment: ' . $e->getMessage());
        }
    }

    /**
     * Check if payment token is still valid and get payment URL
     */
    public function getPaymentUrl($id)
    {
        try {
            $transaction = Order::with('payment')->findOrFail($id);

            if ($transaction->payment->payment_method !== 'Midtrans') {
                return response()->json([
                    'success' => false,
                    'message' => 'This is not a Midtrans payment'
                ]);
            }

            $payment = $transaction->payment;

            // Check if token exists and is not expired
            if (!$payment->snap_token || $this->midtransService->isTokenExpired($payment->token_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment token expired or invalid',
                    'action_required' => true
                ]);
            }

            // Token is valid, return the payment URL
            $paymentUrl = $this->midtransService->getSnapRedirectUrl($payment->snap_token);

            return response()->json([
                'success' => true,
                'payment_url' => $paymentUrl,
                'token_expires_at' => $payment->token_expires_at
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting payment URL for transaction {$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error getting payment URL'
            ], 500);
        }
    }

    /**
     * Prepare item details for retry payment
     */
    protected function prepareRetryItemDetails($orderDetails)
    {
        $items = [];

        foreach ($orderDetails as $detail) {
            $items[] = [
                'id' => $detail->variant_id ?? $detail->product_id,
                'price' => (int) $detail->price_at_purchase,
                'quantity' => (int) $detail->quantity,
                'name' => $detail->product->product_name . ' - ' . ($detail->variant->variant_name ?? 'Standard'),
            ];
        }

        return $items;
    }

    /**
     * Check and update transaction status
     */
    public function checkStatus($id)
    {
        try {
            $transaction = Order::with('payment')->findOrFail($id);

            // Only check status for Midtrans pending payments
            if ($transaction->payment->payment_method === 'Midtrans' && in_array($transaction->status, ['pending', 'failed'])) {
                $statusUpdated = $this->checkMidtransStatus($transaction);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'updated' => $statusUpdated,
                        'status' => $transaction->fresh()->status,
                        'message' => $statusUpdated ? 'Status updated successfully!' : 'Payment status is still pending.'
                    ]);
                }

                if ($statusUpdated) {
                    return redirect()
                        ->route('transaction.show', $id)
                        ->with('success', 'Status updated successfully!');
                } else {
                    return redirect()
                        ->route('transaction.show', $id)
                        ->with('info', 'Payment status is still pending.');
                }
            }

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'updated' => false,
                    'status' => $transaction->status,
                    'message' => 'No status update needed.'
                ]);
            }

            return redirect()
                ->route('transaction.show', $id)
                ->with('info', 'No status update needed.');

        } catch (\Exception $e) {
            Log::error("Error checking status for transaction {$id}: " . $e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error checking status: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->route('transaction.show', $id)
                ->with('error', 'Error checking status: ' . $e->getMessage());
        }
    }

    /**
     * Check Midtrans status via API
     */
    protected function checkMidtransStatus($transaction)
    {
        try {
            $status = Transaction::status($transaction->order_id);

            Log::info("Midtrans API status check for {$transaction->order_id}", (array) $status);

            // Handle both array and object responses
            if (is_array($status)) {
                $transactionStatus = $status['transaction_status'] ?? null;
                $fraudStatus = $status['fraud_status'] ?? null;
            } else {
                $transactionStatus = $status->transaction_status ?? null;
                $fraudStatus = $status->fraud_status ?? null;
            }

            if (!$transactionStatus) {
                Log::error("No transaction status found in Midtrans response for {$transaction->order_id}");
                return false;
            }

            $updated = false;

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    // Payment completed
                    $transaction->update(['status' => 'completed']);
                    $transaction->payment->update([
                        'status' => 'complete',
                        'payment_date' => now(),
                    ]);

                    // Update stock
                    foreach ($transaction->detail as $detail) {
                        ProductVariant::where('variant_id', $detail->variant_id)
                            ->decrement('stock_qty', $detail->quantity);
                    }
                    $updated = true;
                    Log::info("Transaction {$transaction->order_id} completed via capture");
                }
            } elseif ($transactionStatus == 'settlement') {
                // Payment completed
                $transaction->update(['status' => 'completed']);
                $transaction->payment->update([
                    'status' => 'complete',
                    'payment_date' => now(),
                ]);

                // Update stock
                foreach ($transaction->detail as $detail) {
                    ProductVariant::where('variant_id', $detail->variant_id)
                        ->decrement('stock_qty', $detail->quantity);
                }
                $updated = true;
                Log::info("Transaction {$transaction->order_id} settled");
            } elseif ($transactionStatus == 'pending') {
                // Still pending, no update needed
                $updated = false;
                Log::info("Transaction {$transaction->order_id} is still pending");
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $transaction->update(['status' => 'failed']);
                $transaction->payment->update(['status' => 'failed']);
                $updated = true;
                Log::info("Transaction {$transaction->order_id} marked as failed due to: {$transactionStatus}");
            } else {
                Log::warning("Unknown transaction status for {$transaction->order_id}: {$transactionStatus}");
            }

            return $updated;

        } catch (\Exception $e) {
            Log::error("Error checking Midtrans status for {$transaction->order_id}: " . $e->getMessage());
            return false;
        }
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
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();

            return new ApiResource(204, 'Data deleted Successfully!', null);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data does not exist!'], 404);
        }
    }
}