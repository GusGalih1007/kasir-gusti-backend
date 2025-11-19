<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($product)
    {
        // $data = ProductVariant::latest()->paginate(5);

        $productParent = Product::where('slug', '=', $product)->first();
        $data = ProductVariant::where('product_id', '=', $productParent->product_id)->get();

        switch ($data) {
            case null:
                $productParent->update([
                    'is_available' => false
                ]);
                break;
            case $data:
                $productParent->update([
                    'is_available' => true
                ]);
                break;
            default:
                return back()->withErrors('Unexpected errors, please try again later');
        }

        // dd($productParent);

        // return new ApiResource(status: 200, message: 'Success', resource: $data);
        return view('product-variant.index', compact('data', 'productParent'));
    }

    public function create($product)
    {
        $productParent = Product::where('slug', '=', $product)->first();
        $variant = null;

        // dd($productParent);

        return view('product-variant.form', compact('variant', 'productParent'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $product)
    {
        $productParent = Product::where('slug', '=', $product)->first();
        $validate = Validator::make($request->all(), [
            'variant_name' => 'required|string|max:100',
            'price' => 'required',
            'sku' => 'required|string|max:50',
            'stock_qty' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp'
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
            // return response()->json(data: $validate->errors(), status: 422);
        }

        $photoPath = null; //motor-photo/penjualan-motor.png

        // Jika ada file yang diunggah
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->
            store('product-variant', 'public'); // Simpan ke folder public/storage
        }

        ProductVariant::create(attributes: [
            'product_id' => $productParent->product_id,
            'variant_name' => $request->variant_name,
            'price' => $request->price,
            'sku' => $request->sku,
            'stock_qty' => $request->stock_qty,
            'photo' => $photoPath
        ]);

        if ($request->stock_qty != null || $request->stock_qty > 0) {
            $productEdit = Product::findOrFail($productParent->product_id);

            $productEdit->update([
                'is_available' => true,
            ]);
        } else {
            return back()->withErrors('Unexpected errors, pleas try again later');
        }

        // return new ApiResource(status: 201, message: 'Data Created Successfully', resource: $data);
        return redirect()->route('product-variant.index', $product)->with('success', 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($product, $id)
    {
        // $data = ProductVariant::findOrFail($id);
        // $productParent = Product::where('slug', '=', $product)->first();

        // if ($data == null) {
        //     return response()->json(data: 'Data does not exist!', status: 200);
        // }

        return redirect()->route('product-variant.index', $product);

        // return new ApiResource(status: 200, message: 'Success', resource: $data);
    }

    public function edit($product, $id)
    {
        $variant = ProductVariant::findOrFail($id);
        $productParent = Product::where('slug', '=', $product)->first();

        switch ($variant) {
            case null:
                $productParent->update([
                    'is_available' => false
                ]);
                break;
            default:
                $productParent->update([
                    'is_available' => true
                ]);
                break;
        }

        return view('product-variant.form', compact('variant', 'productParent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $product, $id)
    {
        $data = ProductVariant::findOrFail($id);
        $productParent = Product::where('slug', '=', $product)->first();

        if ($data == null) {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $validate = Validator::make(data: $request->all(), rules: [
            'variant_name' => 'required|string|max:100',
            'price' => 'required',
            'sku' => 'required|string|max:50',
            'stock_qty' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp'
        ]);

        $photoPath = $data->photo;

        // Jika ada file yang diunggah
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($data->photo) {
                Storage::disk('public')->delete($data->photo);
            }

            $photoPath = $request->file('photo')->
                store('product-variant', 'public'); // Simpan foto baru
        }

        if ($validate->fails()) {
            // return response()->json(data: $validate->errors(), status: 422);
            return redirect()->back()->withErrors($validate->errors())->withInput();
        }

        $data->update(attributes: [
            'variant_name' => $request->variant_name,
            'price' => $request->price,
            'sku' => $request->sku,
            'stock_qty' => $request->stock_qty,
            'photo' => $photoPath
        ]);

        // return new ApiResource(status: 201, message: 'Data Updated Successfully!', resource: $data);
        return redirect()->route('product-variant.index', $product)->with('success', 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($product, $id)
    {
        $data = ProductVariant::findOrFail($id);

        if ($data == null) {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        if ($data->photo) {
            Storage::disk('public')->delete($data->photo);
        }
        
        $data->delete();

        // return new ApiResource(status: 204, message: 'Data Deleted Successfully', resource: null);
        return redirect()->route('product-variant.index', ['product' => $product])->with('success', 'Data deleted successfully');
    }
}
