<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PageRoleAction;
use App\Http\Resources\PageRoleActionResource;
use App\Models\Role;
use App\Models\Page;

class PageRoleActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = PageRoleAction::latest()->paginate();

        if ($data == null)
        {
            return response()->json('No Data', 200);
        }

        return new PageRoleActionResource(200, 'Success', $data);
    }

    /**
     * Display the specified resource.
     */
    public function getByRole($id)
    {
        $role = Role::findOrFail($id);
        if ($role == null)
        {
            return response()->json('Role does not exist!', 200);
        }

        $data = PageRoleAction::where('role_id', '=', $id);

        if ($data == null)
        {
            return response()->json('Role '. $role->role_name. ' have no access', 200);
        }

        return new PageRoleActionResource(200, 'Success', $data);
    }

    public function getByPage(string $code)
    {
        $page = Page::findOrFail($code);

        if($page == null)
        {
            return response()->json('Page does not exist!', 200);
        }

        return new PageRoleActionResource(200, 'Success', $page);
    }
}
