<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PageRoleAction;
use App\Http\Resources\ApiResource;
use App\Models\Role;
use App\Models\Page;

class PageRoleActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = PageRoleAction::latest()->paginate(perPage: 5);

        if ($data == null)
        {
            return response()->json(data: 'No Data', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success', resource: $data);
    }

    /**
     * Display the specified resource.
     */
    public function getByRole($id)
    {
        $role = Role::findOrFail(id: $id);
        if ($role == null)
        {
            return response()->json(data: 'Role does not exist!', status: 200);
        }

        $data = PageRoleAction::where(column: 'role_id', operator: '=', value: $id);

        if ($data == null)
        {
            return response()->json(data: 'Role '. $role->role_name. ' have no access', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success', resource: $data);
    }

    public function getByPage($code)
    {
        $page = Page::findOrFail(id: $code);

        if($page == null)
        {
            return response()->json(data: 'Page does not exist!', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success', resource: $page);
    }
}
