<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ApiResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::get();

        // return new ApiResource(status: 200, message: 'Success', resource: $data);
        return view('product.index', compact('data'));
    }

    public function create()
    {
        $product = null;
        $category = Category::get();
        $brand = Brand::get();
        $supplier = Supplier::get();

        return view('product.form', compact('product', 'category', 'brand', 'supplier'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make(data: $request->all(), rules: [
            'product_name' => 'required|max:150|string',
            'description' => 'required|string',
            'price' => 'required',
            'category' => 'required|exists:categories,category_id',
            'brand' => 'required|exists:brands,brand_id',
            'supplier' => 'required|exists:suppliers,supplier_id',
            'is_available' => 'nullable|boolean'
        ]);

        $available = false;

        if (!$request->is_available == null)
        {
            $available = $request->is_available;
        }

        if ($validate->fails())
        {
            // return response()->json(data: $validate->errors(), status: 422);
            return redirect()->back()->withErrors($validate->errors())->withInput();
        }

        $data = Product::create(attributes: [
            'product_name' => $request->product_name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category,
            'brand_id' => $request->brand,
            'supplier_id' => $request->supplier,
            'is_available' => $available
        ]);

        // return new ApiResource(status: 201, message: 'Data Created Successfully', resource: $data);
        return redirect()->route('product.index')->with('success', 'Data created successfully');
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

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $category = Category::get();
        $brand = Brand::get();
        $supplier = Supplier::get();

        if ($product == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->withErrors('Data does not exist!');
        }

        return view('product.form', compact('product', 'category', 'brand', 'supplier'));   
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Product::findOrFail(id: $id);

        if ($data == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->withErrors('Data does not exist!');
        }

        $validate = Validator::make(data: $request->all(), rules: [
            'product_name' => 'required|max:150|string',
            'description' => 'required|string',
            'price' => 'required',
            'category' => 'required|exists:categories,category_id',
            'brand' => 'required|exists:brands,brand_id',
            'supplier' => 'required|exists:suppliers,supplier_id',
            'is_available' => 'nullable|boolean'
        ]);

        $available = false;

        if (!$request->is_available == null)
        {
            $available = $request->is_available;
        }

        if ($validate->fails())
        {
            // return response()->json(data: $validate->errors(), status: 422);
            return redirect()->back()->withErrors($validate->errors())->withInput();
        }

        $data->update([
            'product_name' => $request->product_name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category,
            'brand_id' => $request->brand,
            'supplier_id' => $request->supplier,
            'is_available' => $available
        ]);

        // return new ApiResource(status: 201, message: 'Data Updated Successfully!', resource: $data);
        return redirect()->route('product.index')->with('success', 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Product::findOrFail(id: $id);

        if ($data == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->withErrors('Data does not exist!');
        }

        $data->delete();

        // return new ApiResource(status: 204, message: 'Data Deleted Successfully', resource: null);
        return redirect()->route('product.index')->with('success', 'Data deleted successfully');
    }
}
