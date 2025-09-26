<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Brand::latest()->paginate(5);

        if ($data == null)
        {
            return response()->json('No Data', 200);
        }

        return new BrandResource(200, 'Success', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required|string'
        ]);

        if($validate->fails())
        {
            return response()->json($validate->errors(), 422);
        }

        $data = Brand::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return new BrandResource(201, 'Data created successfully', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Brand::findOrFail($id);

        if ($data == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        return new BrandResource(200, 'Success', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Brand::findOrFail($id);

        if ($data == null){
            return response()->json('Data does not exist!', 200);
        }
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required|string'
        ]);

        if($validate->fails())
        {
            return response()->json($validate->errors(), 422);
        }

        $data->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return new BrandResource(201, 'Data updated successfully', $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Brand::findOrFail($id);

        if ($data == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        $data->delete();

        return new BrandResource(204, 'Data deleted Successfully', null);
    }
}
