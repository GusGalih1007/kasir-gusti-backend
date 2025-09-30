<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Resources\ApiResource;
use Illuminate\Support\Facades\Validator;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ProductVariant::latest()->paginate(5);

        if ($data == null)
        {
            return response()->json(data: 'No Data', status: 200);
        }
        
        return new ApiResource(status: 200, message: 'Success', resource: $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,product_id',
            'variant_name' => 'required|string|max:100',
            'price' => 'required|decimal:2',
            'sku' => 'required|string|max:50',
            'stock_qty' => 'required|numeric',
        ]);

        if ($validate->fails())
        {
            return response()->json(data: $validate->errors(), status: 422);
        }

        $data = ProductVariant::create(attributes: [
            'product_id' => $request->product_id,
            'variant_name' => $request->variant_name,
            'price' => $request->price,
            'sku' => $request->sku,
            'stock_qty' => $request->stock_qty
        ]);

        return new ApiResource(status: 201, message: 'Data Created Successfully', resource: $data);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = ProductVariant::findOrFail($id);

        if ($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $validate = Validator::make(data: $request->all(), rules: [
            'product_id' => 'required|exists:products,product_id',
            'variant_name' => 'required|string|max:100',
            'price' => 'required|decimal:2',
            'sku' => 'required|string|max:50',
            'stock_qty' => 'required|numeric',
        ]);

        if ($validate->fails())
        {
            return response()->json(data: $validate->errors(), status: 422);
        }

        $data->update(attributes: [
            'product_id' => $request->product_id,
            'variant_name' => $request->variant_name,
            'price' => $request->price,
            'sku' => $request->sku,
            'stock_qty' => $request->stock_qty
        ]);

        return new ApiResource(status: 201, message: 'Data Updated Successfully!', resource: $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = ProductVariant::findOrFail($id);

        if ($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $data->delete();

        return new ApiResource(status: 204, message: 'Data Deleted Successfully', resource: null);
    }
}
