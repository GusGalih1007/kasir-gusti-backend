<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Supplier::get();

        // if ($data == null)
        // {
        //     return response()->json(data: 'No Data', status: 200);
        // }

        // return new ApiResource(status: 200, message: 'Success', resource: $data);
        return view('supplier.index', compact('data'));
    }

    public function create()
    {
        $supplier = null;

        return view('supplier.form', compact('supplier'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make(data: $request->all(), rules: [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'alamat' => 'required|string'
        ]);

        if ($validate->fails())
        {
            // return response()->json(data: $validate->errors(), status: 422);
            return redirect()->back()->withErrors($validate->errors())->withInput();
        }

        $data = Supplier::create(attributes: [
            'name' => $request->name,
            'description' => $request->description,
            'alamat' => $request->alamat
        ]);

        // return new ApiResource(status: 201, message: 'Data created successfully', resource: $data);
        return redirect()->route('supplier.index')->with('success', 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Supplier::findOrFail(id: $id);

        if ($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success', resource: $data);
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        if ($supplier == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->withErrors('errors', 'Data does not exist!');
        }

        return view('supplier.form', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Supplier::findOrFail(id: $id);

        if ($data == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->withErrors('errors', 'Data does not exist!');
        }

        $validate = Validator::make(data: $request->all(), rules: [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'alamat' => 'required|string'
        ]);

        if ($validate->fails())
        {
            return redirect()->back()->withErrors($validate->errors())->withInput();
            // return response()->json(data: $validate->errors(), status: 422);
        }

        $data->update([
            'name' => $request->name,
            'description' => $request->description,
            'alamat' => $request->alamat
        ]);

        // return new ApiResource(status: 201, message: 'Data updated successfully', resource: $data);
        return redirect()->route('supplier.index')->with('success', 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Supplier::findOrFail(id: $id);

        if ($data == null)
        {
            return redirect()->back()->withErrors('errors', 'Data does not exist!');
            // return response()->json(data: 'Data does not exist!', status: 200);
        }

        $data->delete();

        // return new ApiResource(status: 204, message: 'Data deleted successfully', resource: null);
        return redirect()->route('supplier.index')->with('success', 'Data deleted successfully');
    }
}
