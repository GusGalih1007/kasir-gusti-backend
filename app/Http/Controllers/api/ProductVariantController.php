<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Resources\ApiResource;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        // $data = ProductVariant::latest()->paginate(5);

        $data = ProductVariant::where('product_id', '=', $id)->get();

        // dd($data);
        
        // return new ApiResource(status: 200, message: 'Success', resource: $data);
        return view('product.variant.index', compact('data'));
    }

    public function create($id)
    {
        $variant = null;
        $data = ProductVariant::where('product_id', '=', $id)->first();

        return view('product.variant.form', compact('variant', 'data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'variant_name' => 'required|string|max:100',
            'price' => 'required|numeric',
            'sku' => 'required|string|max:50',
            'stock_qty' => 'required|numeric',
        ]);

        if ($validate->fails())
        {
            return redirect()->back()->withErrors($validate->errors())->withInput();
            // return response()->json(data: $validate->errors(), status: 422);
        }

        $data = ProductVariant::create(attributes: [
            'product_id' => $id,
            'variant_name' => $request->variant_name,
            'price' => $request->price,
            'sku' => $request->sku,
            'stock_qty' => $request->stock_qty
        ]);

        if ($request->stock_qty !== null || $request->stock_qty > 0)
        {
            $product = Product::findOrFail($request->product_id);
    
            $product->update([
                'is_available' => true
            ]);
        }

        // return new ApiResource(status: 201, message: 'Data Created Successfully', resource: $data);
        return redirect()->route('product-variant.index', ['id' => $id])->with('success', 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = ProductVariant::findOrFail($id);

        if ($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success', resource: $data);
    }

    public function edit($id, $variant)
    {
        $data = ProductVariant::where('product_id', '=', $id)->first();
        $variant = ProductVariant::findOrFail($variant);

        return view('product.variant.form', compact('variant', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id, $variant)
    {
        $data = ProductVariant::findOrFail($variant);

        if ($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $validate = Validator::make(data: $request->all(), rules: [
            'variant_name' => 'required|string|max:100',
            'price' => 'required|numeric',
            'sku' => 'required|string|max:50',
            'stock_qty' => 'required|numeric',
        ]);

        if ($validate->fails())
        {
            return response()->json(data: $validate->errors(), status: 422);
        }

        if (!$request->stock_qty == 0)
        {
            $product = Product::findOrFail($request->product_id);

            $product->update([
                'is_available' => false
            ]);
        } else {
            $product = Product::findOrFail($request->product_id);

            $product->update([
                'is_available' => true
            ]);
        }

        $data->update(attributes: [
            'variant_name' => $request->variant_name,
            'price' => $request->price,
            'sku' => $request->sku,
            'stock_qty' => $request->stock_qty
        ]);

        // return new ApiResource(status: 201, message: 'Data Updated Successfully!', resource: $data);
        return redirect()->route('product-variant.index', ['id' => $id])->with('success', 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $variant)
    {
        $data = ProductVariant::findOrFail($variant);

        if ($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $data->delete();

        // return new ApiResource(status: 204, message: 'Data Deleted Successfully', resource: null);
        return redirect()->route('product-variant.index', ['id' => $id])->with('success', 'Data deleted successfully');
    }
}
