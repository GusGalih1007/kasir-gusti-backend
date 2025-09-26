<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Customers::latest()->paginate(5);

        return new CustomerResource(200, 'Success!', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'alamat' => 'required|string|max:15',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'is_member' => 'required|boolean'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $data = Customers::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'email' => $request->email,
            'is_member' => $request->is_member
        ]);

        return new CustomerResource(201, 'Data created successfully!', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Customers::findOrFail($id);

        if($data == null) {
            return response()->json('Data does not exist!', 200);
        }

        return new CustomerResource(200, 'Success!', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'alamat' => 'required|string',
            'phone' => 'required|numeric|max:15',
            'email' => 'required|email',
            'is_member' => 'required|boolean'
        ]);
        
        if ($validate->fails())
        {
            return response()->json($validate->errors(), 422);
        }
        
        $data = Customers::findOrFail($id);
        if ($data == null) {
            return response()->json('Data does not exist!', 200);
        }

        $data->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'email' => $request->email,
            'is_member' => $request->is_member
        ]);

        return new CustomerResource(201, 'Data updated Successfully!', $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Customers::findOrFail($id);

        if ($data == null) {
            return response()->json('Data does not exist!', 200);
        }
        
        $data->delete();

        return new CustomerResource(204, 'Data deleted Successfully!', null);
    }
}
