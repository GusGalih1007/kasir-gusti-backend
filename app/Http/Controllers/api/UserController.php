<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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
        $data = Users::latest()->paginate();

        if ($data == null){
            return response()->json('No Data', 200);
        }

        return new UserResource(200, 'Success', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'phone' => 'required|numeric',
            'role_id' => 'required|exists:Role,role_id',
            'status' => 'required|string'
        ]);

        if ($validate->fails())
        {
            return response()->json($validate->errors(), 422);
        }

        $data = Users::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'status' => $request->status
        ]);

        return new UserResource(201, 'Data Successfully created!', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Users::findOrFail($id);

        if ($data == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        return new UserResource(200, 'Success', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Users::findOrFail($id);

        if($data == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        $validate = Validator::make($request->all(), [
            'username' => 'required|string',
            'email' => 'required|email',
            'password' => 'nullable|min:8',
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'phone' => 'required|numeric',
            'role_id' => 'required|exists:Role,role_id',
            'status' => 'required|string'
        ]);

        if ($validate->fails())
        {
            return response()->json($validate->errors(), 422);
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

        if (filled($request->password))
        {
            $field['password'] = bcrypt($request->password);
        }

        $data->update($field);

        return new UserResource(201, 'Data updated successfully', $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Users::findOrFail($id);

        if($data == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        $data->delete();

        return new UserResource(204, 'Data deleted successfully', null);
    }
}
