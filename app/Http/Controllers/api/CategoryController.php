<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        // $category = Category::latest()->paginate(perPage: 5);

        $data = Category::get();

        return view('category.index', compact('data'));

        // return new ApiResource(status: 200, message: 'Success', resource: $category);
    }

    public function create()
    {
        $parent = Category::get();

        $category = null;

        return view('category.form', compact('parent', 'category'));
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
            // return response()->json(data: $validate->errors(), status: 422);
            return redirect()->back()->withErrors('errors', $validate->errors())->withInput();
        }

        $category = Category::create(attributes: [
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            // 'created_by' => auth()->guard('api')->id()
        ]);

        return redirect()->route('category.index')->with('Success', 'Data Created Successfully!');
    }
    public function show($id)
    {
        $category = Category::findOrFail(id: $id);

        if($category == null)
        {
            // return response()->json(data: 'Data does not exist', status: 200);
            return redirect()->back()->with('errors', 'Data does not exist!');
        }

        return new ApiResource(status: 200, message: 'Success', resource: $category);
    }
    public function edit($id)
    {
        $parent = Category::get();

        $category = Category::findOrFail($id);

        if($category == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->with('errors', 'Data does not exist!');
        }

        return view('category.form', compact('parent', 'category'));
    }
    public function update(Request $request, $id)
    {
        $validate = Validator::make(data: $request->all(), rules: [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'parent_id' => 'nullable|numeric',
        ]);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate->errors())->withInput();

            // return response()->json(data: $validator->errors(), status: 422);
        }

        $category = Category::findOrFail(id: $id);

        if($category == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->with('errors', 'Data does not exist!');
        }

        $category->update(attributes: [
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id
        ]);

        return redirect()->route('category.index')->with('Success', 'Data Updated Successfully!');
        // return new ApiResource(status: 201, message: 'Data updated Successfully!', resource: $category);
    }
    public function destroy($id)
    {
        $category = Category::findOrFail(id: $id);

        if($category == null)
        {
            // return response()->json(data: 'Data does not exist!', status: 200);
            return redirect()->back()->with('errors', 'Data does not exist!');
        }

        $category->delete();

        // return new ApiResource(status: 204, message: 'Data deleted Successfully!', resource: null);
        return redirect()->route('category.index')->with('success', 'Data deleted successfully');
    }
}
