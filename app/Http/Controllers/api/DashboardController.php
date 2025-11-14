<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Users;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $user = Users::get();
        // $role = Role::findOrFail($user->role_id);
        return view('dashboard.admin');
    }
}
