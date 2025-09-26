<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::latest()->paginate(5);

        return new CategoryResource(200, 'Success', $category);
    }

    public function store (Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'parent_id' => 'nullable|numeric',
        ]);

        if ($validate->fails())
        {
            return response()->json($validate->errors(), 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id
        ]);

        return new CategoryResource(201, 'Data created successfully!', $category);
    }
    public function show($id)
    {
        $category = Category::findOrFail($id);

        if($category == null)
        {
            return response()->json('Data does not exist', 200);
        }

        return new CategoryResource(200, 'Success', $category);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'parent_id' => 'nullable|numeric',
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::findOrFail($id);

        if($category == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id
        ]);

        return new CategoryResource(201, 'Data updated Successfully!', $category);
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if($category == null)
        {
            return response()->json('Data does not exist!', 200);
        }

        $category->delete();

        return new CategoryResource(204, 'Data deleted Successfully!', null);
    }
}
