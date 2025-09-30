<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::latest()->paginate(perPage: 5);

        return new ApiResource(status: 200, message: 'Success', resource: $category);
    }

    public function store (Request $request)
    {
        $validate = Validator::make(data: $request->all(), rules: [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'parent_id' => 'nullable|numeric',
        ]);

        if ($validate->fails())
        {
            return response()->json(data: $validate->errors(), status: 422);
        }

        $category = Category::create(attributes: [
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id
        ]);

        return new ApiResource(status: 201, message: 'Data created successfully!', resource: $category);
    }
    public function show($id)
    {
        $category = Category::findOrFail(id: $id);

        if($category == null)
        {
            return response()->json(data: 'Data does not exist', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success', resource: $category);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make(data: $request->all(), rules: [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'parent_id' => 'nullable|numeric',
        ]);

        if($validator->fails())
        {
            return response()->json(data: $validator->errors(), status: 422);
        }

        $category = Category::findOrFail(id: $id);

        if($category == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $category->update(attributes: [
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id
        ]);

        return new ApiResource(status: 201, message: 'Data updated Successfully!', resource: $category);
    }
    public function destroy($id)
    {
        $category = Category::findOrFail(id: $id);

        if($category == null)
        {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $category->delete();

        return new ApiResource(status: 204, message: 'Data deleted Successfully!', resource: null);
    }
}
