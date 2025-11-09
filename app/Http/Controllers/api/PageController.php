<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Faker\Factory as Faker;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Page::get();

        if ($data == null){
            return response()->json(data: 'No Data', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success', resource: $data);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'page_name' => 'required|string',
            'action' => 'required|array|min:1'
        ]);

        if ($validate->fails())
        {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        }

        $faker = Faker::create();

        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => $request->page_name,
            'action' => $request->action
        ]);

        return redirect()->route('role-permission.index')->with('success', 'Page Permission');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Page::findOrFail($id);

        if ($data == null)
        {
            return response()->json(data: 'Data does not exist', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success', resource: $data);
    }
}
