<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;
use App\Http\Resources\ApiResource;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Role::latest()->paginate(perPage: 5);

        if ($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success', resource: $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make(data: $request->all(), rules: [
            'name' => 'required|string|max:30'
        ]);

        if($validate->fails())
        {
            return response()->json(data: $validate->errors(), status: 422);
        }

        $data = Role::create(attributes: [
            'name' => $request->name
        ]);

        return new ApiResource(status: 201, message: 'Data created successfully!', resource: $data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Role::findOrFail(id: $id);

        if($data == null)
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
        $data = Role::findOrFail(id: $id);

        if($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $validate = Validator::make(data: $request->all(), rules: [
            'name' => 'required|string|max:30'
        ]);

        if($validate->fails())
        {
            return response()->json(data: $validate->errors(), status: 422);
        }

        $data->update([
            'name' => $request->name
        ]);

        return new ApiResource(status: 201, message: 'Data updated Successfully', resource: $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Role::findOrFail(id: $id);

        if($data == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $data->delete();

        return new ApiResource(status: 204, message: 'Data Deleted Successfully', resource: null);
    }
}
