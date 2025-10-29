<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Debug\VirtualRequestStack;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Brand::latest()->paginate(perPage: 5);

        // if ($data == null)
        // {
        //     return response()->json(data: 'No Data', status: 200);
        // }

        // return new ApiResource(status: 200, message: 'Success', resource: $data);

        return view('brand.index', compact('data'));
    }

    public function create()
    {
        $brand = null;

        return view('brand.form', compact('brand'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make(data: $request->all(), rules: [
            'name' => 'required|string|max:100',
            'description' => 'required|string'
        ]);

        if ($validate->fails())
        {
            // return response()->json(data: $validate->errors(), status: 422);
            return redirect()->back()->withErrors('errors', $validate->errors())->withInput();
        }

        $data = Brand::create(attributes: [
            'name' => $request->name,
            'description' => $request->description
        ]);

        // return new ApiResource(status: 201, message: 'Data created successfully', resource: $data);
        return redirect()->route('brand.index')->with('success', 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Brand::findOrFail(id: $id);

        if ($data == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->withErrors('errors', 'Data does not exist!');
        }

        return new ApiResource(status: 200, message: 'success', resource: $data);
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);

        if($brand == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->withErrors('errors', 'Data does not exist!');
        }

        return view('brand.form', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Brand::findOrFail(id: $id);

        if($data == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->withErrors('errors', 'Data does not exist!');
        }
        $validate = Validator::make(data: $request->all(), rules: [
            'name' => 'required|string|max:100',
            'description' => 'required|string'
        ]);

        if($validate->fails())
        {
            // return response()->json(data: $validate->errors(), status: 422);
            return redirect()->back()->withErrors('errors', $validate->errors())->withInput();
        }

        $data->update(attributes: [
            'name' => $request->name,
            'description' => $request->description
        ]);

        // return new ApiResource(status: 201, message: 'Data updated successfully', resource: $data);
        return redirect()->route('brand.index')->with('success', 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Brand::findOrFail(id: $id);

        if ($data == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->withErrors('errrors', 'Data does not exist!');
        }

        $data->delete();

        // return new ApiResource(status: 204, message: 'Data deleted Successfully', resource: null);
        return redirect()->route('brand.index')->with('success', 'Data Deleted Successfully');
    }
}
