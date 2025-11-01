<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Customers;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Customers::get();

        // return new ApiResource(status: 200, message: 'Success!', resource: $data);
        return view('customer.index', compact('data'));
    }

    public function create()
    {
        $membership = Membership::get();
        $customer = null;

        return view('customer.form', compact('customer', 'membership'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make(data: $request->all(), rules: [
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'alamat' => 'required|string',
            'phone' => 'required|numeric|max_digits:15',
            'email' => 'required|email',
            'membership' => 'required'
        ]);

        $isMembership = false;

        if ($request->membership != 'regular')
        {
            $isMembership = true;
        }

        if ($validate->fails()) {
            // return response()->json(data: $validate->errors(), status: 422);
            return redirect()->back()->withErrors('errors', $validate->errors())->withInput();
        }

        $data = Customers::create(attributes: [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'email' => $request->email,
            'is_member' => $isMembership,
            'membership_id' => $request->membership
        ]);

        // return new ApiResource(status: 201, message: 'Data created successfully!', resource: $data);
        return redirect()->route('customer.index')->with('success', 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Customers::findOrFail(id: $id);

        if($data == null) {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success!', resource: $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make(data: $request->all(), rules: [
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'alamat' => 'required|string',
            'phone' => 'required|numeric|max_digits:15',
            'email' => 'required|email',
            'is_member' => 'nullable|boolean'
        ]);
        
        if ($validate->fails())
        {
            return response()->json(data: $validate->errors(), status: 422);
        }
        
        $data = Customers::findOrFail(id: $id);
        if ($data == null) {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $data->update(attributes: [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'email' => $request->email,
            'is_member' => (!$request->is_member) ? false : $request->is_member
        ]);

        return new ApiResource(status: 201, message: 'Data updated Successfully!', resource: $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Customers::findOrFail(id: $id);

        if ($data == null) {
            return response()->json(data: 'Data does not exist!', status: 200);
        }
        
        $data->delete();

        return new ApiResource(status: 204, message: 'Data deleted Successfully!', resource: null);
    }
}
