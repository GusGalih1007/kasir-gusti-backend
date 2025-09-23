<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;
use App\Http\Resources\RoleResource;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Role::latest()->paginate();

        return new RoleResource(200, 'Success', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($validate->fails())
        {
            return response()->json($validate->errors(), 422);
        }

        $data = Role::create([
            'name' => $request->name
        ]);

        return new RoleResource(201, 'Data created successfully!', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Role::findOrFail($id);

        if($data == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        return new RoleResource(200, 'Success', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Role::findOrFail($id);

        if($data == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($validate->fails())
        {
            return response()->json($validate->errors(), 422);
        }

        $data->update([
            'name' => $request->name
        ]);

        return new RoleResource(201, 'Data updated Successfully', $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Role::findOrFail($id);

        if($data == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        $data->delete();

        return new RoleResource(204, 'Data Deleted Successfully', null);
    }
}
