<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ApiResource;
use App\Models\Product;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::latest()->paginate(perPage: 10);

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
        $validate = Validator::make(data: $request->all(), rules: [
            'product_name' => 'requred|max:150|string',
            'description' => 'required|string',
            'price' => 'required|decimal:2,2',
            'category' => 'required|exist:categories,category_id',
            'brand' => 'required|exist:brands,brand_id',
            'supplier' => 'required|exist:suppliers,supplier_id',
            'is_available' => 'required|boolean'
        ]);

        if ($validate->fails())
        {
            return response()->json(data: $validate->errors(), status: 422);
        }

        $data = Product::create(attributes: [
            'product_name' => $request->product_name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category,
            'brand_id' => $request->brand,
            'supplier_id' => $request->supplier,
            'is_available' => $request->is_available
        ]);

        return new ApiResource(status: 201, message: 'Data Created Successfully', resource: $data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Product::findOrFail(id: $id);

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
        $data = Product::findOrFail(id: $id);

        if ($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $validate = Validator::make(data: $request->all(), rules: [
            'product_name' => 'requred|max:150|string',
            'description' => 'required|string',
            'price' => 'required|decimal:2,2',
            'category' => 'required|exist:categories,category_id',
            'supplier' => 'required|exist:supplier,supplier_id',
            'is_available' => 'required|boolean'
        ]);

        if ($validate->fails())
        {
            return response()->json(data: $validate->errors(), status: 422);
        }

        $data->update(attributes: [
            'product_name' => $request->product_name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category,
            'supplier_id' => $request->supplier,
            'is_available' => $request->is_available
        ]);

        return new ApiResource(status: 201, message: 'Data Updated Successfully!', resource: $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Product::findOrFail(id: $id);

        if ($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        return new ApiResource(status: 204, message: 'Data Deleted Successfully', resource: null);
    }
}
