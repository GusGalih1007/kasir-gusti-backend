<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource;
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
        $data = Supplier::latest()->paginate(5);

        if ($data == null)
        {
            return response()->json('No Data', 200);
        }

        return new SupplierResource(200, 'Success', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'alamat' => 'required|string'
        ]);

        if ($validate->fails())
        {
            return response()->json($validate->errors(), 422);
        }

        $data = Supplier::create([
            'name' => $request->name,
            'description' => $request->description,
            'alamat' => $request->alamat
        ]);

        return new SupplierResource(201, 'Data created successfully', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Supplier::findOrFail($id);

        if ($data == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        return new SupplierResource(200, 'Success', $data);
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
