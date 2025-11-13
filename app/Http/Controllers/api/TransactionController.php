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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
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

        // dd($data);

        // return new ApiResource(200, 'Success', $data);
        return view('transaction.index', compact('data'));
    }

    public function create()
    {
        $customer = Customers::get();
        $product = Product::get();
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
        try {
            // Decode product input (can be JSON string or array)
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
                    'payment_method' => 'required|string|max:50',
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
                return response()->json($validate->errors(), 422);
            }

            // Authentication check
            $userId = Auth::guard('web')->id();
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Customer discount logic (optional)
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
                $variant = ProductVariant::with('product')->findOrFail($productData['variant_id']);

                // Ensure stock is enough
                if ($variant->stock_qty < $productData['qty']) {
                    return response()->json([
                        'error' => "Insufficient stock for variant {$variant->variant_name}"
                    ], 422);
                }

                // Determine price
                $price = $variant->price ?? $variant->product->price ?? 0;
                $itemTotal = $price * $productData['qty'];
                $totalPrice += $itemTotal;

                $products[] = [
                    'product_id' => $variant->product_id,
                    'variant_id' => $variant->variant_id,
                    'quantity' => $productData['qty'],
                    'price' => $price,
                    'total' => $itemTotal,
                ];
            }

            // Apply discount
            $discountValue = ($totalPrice * $discountPercent / 100);
            $totalAfterDiscount = max(0, $totalPrice - $discountValue);

            // Payment and change calculation
            $change = $request->payment - $totalAfterDiscount;
            if ($change < 0) {
                return response()->json(['error' => 'Payment amount is insufficient'], 422);
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
                'amount' => $request->payment,
                'currency' => $request->currency,
                'status' => 'complete',
                'change' => $change,
                'payment_date' => now(),
                'created_by' => $userId,
            ]);

            // ✅ Success redirect
            return redirect()
                ->route('transaction.index')
                ->with('success', 'Transaction successfully created!');

        } catch (\Exception $e) {
            // For debugging, return error JSON
            return response()->json([
                'error' => 'An error occurred during transaction creation.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaction = Order::with('detail.product.variant', 'customer', 'payment', 'userId')->findOrFail($id);

        if ($transaction == null) {
            // return response()->json('Data does not exist!', 200);
            return back()->withErrors('Data does not exist!');
        }

        // dd($transaction->detail->product->product_name);

        // return new ApiResource(200, 'success', $transaction);
        return view('transaction.show', compact('transaction'));
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
        $data = Order::where('transaction_id', '=', $id);

        if ($data == null) {
            return response()->json('Data does not exist!', 200);
        }

        $data->delete();

        return new ApiResource(204, 'Data deleted Successfully!', null);
    }
}
