<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Product;
use App\Models\Customers;
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
        $data = Order::latest()->paginate(5);

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
            'currency' => 'required|max:3',
            'product' => 'required|array',
            'product[product_id]' => 'required|exists:products,product_id',
            'product[variant_id]' => 'required|exists:product_variants,variant_id',
            'product[qty]' => 'required|integer|min:1',
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

        $item = json_decode($request->product);

        $products = [];
        // $variants = [];
        foreach ($item as $productData )
        {
            $product = Product::find($productData['product_id']);

            $totalPrice += $product->price * $productData['qty'];

            // Simpan produk beserta kuantitas untuk TransactionDetails
            $products[] = [
                'product_id' => $product->product_id,
                'quantity' => $productData['qty'],
                'price' => $product->price,
                'total' => $product->price * $productData['qty'],
                'variant_id' => $productData['variant_id'],
            ];
        }

        $totalAfterDiscount = $totalPrice - $discount;
        $change = $request->payment - $totalAfterDiscount;

        $transaction = Order::create([
            'user_id' => Auth::id(),
            'customer_id' => $request->customer_id,
            'total_amount' => $totalAfterDiscount,
            'discount' => $discount,
            'change' => $change,
            'order_date' => today(),
            'status' => 'completed',
            'created_by' => auth()->guard()->id(),
        ]);

        foreach ($products as $p)
        {
            $detail = OrderDetail::create([
                'order_id' => $transaction->order_id,
                'product_id' => $p['product_id'],
                'variant_id' => $p['variant_id'],
                'quantity' => $p['quantity'],
                'price_at_purchase'=> $p['price'],
                'total_price' => $p['total']
            ]);

            $variantModel = ProductVariant::find($p['variant_id']);
            $variantModel->stock -= $p['quantity'];
            $variantModel->save();
        }

        $payment = Payment::create([
            'order_id' => $transaction->order_id,
            'payment_method' => $request->payment_method,
            'amount'         => $request->payment,
            'currency'  => $request->currency,
            'status' => 'complete',
            'change'         => $change,
            'payment_date'   => today(),
            'created_by'       => auth()->guard('api')->id(),  // ID user yang menerima pembayaran
        ]);

        return new ApiResource(201, 'Data Successfully created!', [
            'order' => $transaction,
            'order detail' => $detail,
            'payment' => $payment]);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaction = Order::with('details.product', 'customer', 'payment', 'userId')->findOrFail($id);

        if ($transaction == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        return new ApiResource(200, 'success', $transaction);
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
        $data = Order::where('transaction_id', $id)->delete();

        if ($data == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        return new ApiResource(204, 'Data deleted Successfully!', null);
    }
}
