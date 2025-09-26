<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Page::latest()->paginate(5);

        if ($data == null){
            return response()->json('No Data', 200);
        }

        return new PageResource(200, 'Success', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Page::findOrFail($id);

        if ($data == null)
        {
            return response()->json('Data does not exist', 200);
        }

        return new PageResource(200, 'Success', $data);
    }
}
