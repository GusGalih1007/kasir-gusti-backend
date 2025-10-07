<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Page::latest()->paginate(perPage: 5);

        if ($data == null){
            return response()->json(data: 'No Data', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success', resource: $data);
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
