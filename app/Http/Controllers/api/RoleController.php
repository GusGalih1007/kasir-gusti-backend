<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;
use App\Http\Resources\ApiResource;
use App\Models\PageRoleAction;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Role::with('pageRoleActionRole')->get();

        $page = Page::all()->map(function ($page) {
            return [
                'page_code' => $page->page_code,
                'page_name' => $page->page_name,
                'action' => $page->action ?? [] // Ensure action is always an array
            ];
        })->toArray();

        // Debug: Check what data is being sent to the view
        // dd($page); // Uncomment this temporarily to see the page data

        // return new ApiResource(status: 200, message: 'Success', resource: $data);
        return view('role-permission.index', compact('data', 'page'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make(data: $request->all(), rules: [
            'role_name' => 'required|string|max:30'
        ]);

        if ($validate->fails()) {
            // return response()->json(data: $validate->errors(), status: 422);
            return back()->withErrors($validate->errors())->withInput();
        }

        $data = Role::create(attributes: [
            'name' => $request->role_name
        ]);

        // return new ApiResource(status: 201, message: 'Data created successfully!', resource: $data);
        return redirect()->route('role-permission.index')->with('success', 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Role::findOrFail(id: $id);

        if ($data == null) {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        return new ApiResource(status: 200, message: 'Success', resource: $data);
    }

    public function getUserRole()
    {
        if (auth()->guard('web')->check()) {
            $user = auth()->guard('web')->user();
            return $user->role ?? 'No Role';
        }

        return 'No Role';
    }

    public function updatePermission(Request $request)
    {
        try {
            Log::info('Permission update request:', $request->all());

            $request->validate([
                'role_id' => 'required|exists:roles,role_id',
                'page_code' => 'required',
                'action' => 'required|string',
                'is_checked' => 'required|boolean'
            ]);

            // Check if page_code exists
            $pageExists = Page::where('page_code', $request->page_code)->exists();
            if (!$pageExists) {
                Log::error('Page code not found: ' . $request->page_code);
                return response()->json([
                    'success' => false,
                    'message' => 'Page code not found: ' . $request->page_code
                ], 422);
            }

            $roleId = $request->role_id;
            $pageCode = $request->page_code;
            $action = $request->action;
            $isChecked = $request->is_checked;

            if ($isChecked) {
                // Check if permission already exists
                $existingPermission = PageRoleAction::where('role_id', $roleId)
                    ->where('page_code', $pageCode)
                    ->where('action', $action)
                    ->first();

                if (!$existingPermission) {
                    PageRoleAction::create([
                        'role_id' => $roleId,
                        'page_code' => $pageCode,
                        'action' => $action
                    ]);

                    Log::info('Permission created:', ['role_id' => $roleId, 'page_code' => $pageCode, 'action' => $action]);
                }
            } else {
                // Remove permission
                $deleted = PageRoleAction::where('role_id', $roleId)
                    ->where('page_code', $pageCode)
                    ->where('action', $action)
                    ->delete();

                Log::info('Permission deleted:', ['role_id' => $roleId, 'page_code' => $pageCode, 'action' => $action, 'deleted_count' => $deleted]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Permission update error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Role::findOrFail(id: $id);

        if ($data == null) {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $validate = Validator::make(data: $request->all(), rules: [
            'role_name_edit' => 'required|string|max:30'
        ]);

        if ($validate->fails()) {
            return response()->json(data: $validate->errors(), status: 422);
        }

        $data->update([
            'name' => $request->role_name_edit
        ]);

        // return new ApiResource(status: 201, message: 'Data updated Successfully', resource: $data);
        return redirect()->route('role-permission.index')->with('success', 'Data created successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Role::findOrFail(id: $id);

        if ($data == null) {
            return response()->json(data: 'Data does not exist!', status: 200);
        }

        $data->delete();

        // return new ApiResource(status: 204, message: 'Data Deleted Successfully', resource: null);
        return redirect()->route('role-permission.index')->with('success', 'Data created successfully');
    }
}
