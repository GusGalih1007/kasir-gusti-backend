<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Illuminate\Bus\UpdatedBatchJobCounts;
use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Users::latest()->paginate(5);

        if ($data == null){
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
            'username' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'phone' => 'required|numeric|max:15',
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'required|string'
        ]);

        if ($validate->fails())
        {
            return response()->json(data: $validate->errors(), status: 422);
        }

        $data = Users::create(attributes: [
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt(value: $request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'status' => $request->status
        ]);

        return new ApiResource(status: 201, message: 'Data Successfully created!', resource: $data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Users::findOrFail(id: $id);

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
        $data = Users::findOrFail(id: $id);

        if($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $validate = Validator::make(data: $request->all(), rules: [
            'username' => 'required|string',
            'email' => 'required|email',
            'password' => 'nullable|min:8',
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'phone' => 'required|numeric|max:15',
            'role_id' => 'required|exists:Role,role_id',
            'status' => 'required|string'
        ]);

        if ($validate->fails())
        {
            return response()->json(data: $validate->errors(), status: 422);
        }

        $field = [
            'username' => $request->username,
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'status' => $request->status
        ];

        if (filled(value: $request->password))
        {
            $field['password'] = bcrypt(value: $request->password);
        }

        $data->update(attributes: $field);

        return new ApiResource(status: 201, message: 'Data updated successfully', resource: $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Users::findOrFail(id: $id);

        if($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $data->delete();

        return new ApiResource(status: 204, message: 'Data deleted successfully', resource: null);
    }
}
