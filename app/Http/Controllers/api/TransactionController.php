<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Product;
use App\Models\Customers;
use App\Models\Order;
use App\Models\OrderDetail;
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
            'detail.product', 'customer', 'userId', 'payment',
        )->orderBy('order_id', 'desc');

        return new ApiResource(200, 'Success', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:customers,customer_id',
            'payment_method' => 'required|string',
            'product' => 'required|array',
            'product.*.product_id' => 'required|exists:products,product_id',
            'product.*.variant_id' => 'required|exists:product_variants,variant_id',
            'product.*.qty' => 'required|integer|min:1',
            'payment' => 'required|numeric|min:0',
        ]);

        if ($validate->fails())
        {
            return response()->json($validate->errors(), 422);
        }

        $customer = Customers::findOrFail($request->customer_id);
        $discount = 0;

        if ($customer->is_member == true)
        {
            $discount = $request->discount;
        }

        $totalPrice = 0;

        $products = [];
        $variants = [];
        foreach ($request->product as $productData )
        {
            $product = Product::find($productData['product_id']);

            $totalPrice += $product->price * $productData['qty'];

            // Simpan produk beserta kuantitas untuk TransactionDetails
            $products[] = [
                'product_id' => $product->product_id,
                'quantity' => $productData['qty'],
                'price' => $product->price,
                'total' => $product->price * $productData['qty'],
            ];
        }

        $totalAfterDiscount = $totalPrice - $discount;

        $transaction = Order::create([
            'user_id' => Auth::id(),
            'customer_id' => $request->customer_id,
            'total_amount' => $totalAfterDiscount,
            'discount' => $discount,
            'order_date' => today(),
            'status' => 'completed'
        ]);

        foreach ($products as $p)
        {
            OrderDetail::create([
                'order_id' => $transaction->order_id,
                'product_id' => $p['product_id'],
                ''
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
}
