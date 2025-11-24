<?php

namespace App\Helpers;

use App\Models\Users;
use Exception;
use Illuminate\Support\Facades\Log;

class GetUserRoleHelper
{
    public static function getRoleName()
    {
        try {
            $user = Users::findOrFail(auth('web')->id());

            $getRoleName = $user->role->name;

            Log::info('Get role name from helper: '.$getRoleName);

            return $getRoleName;
        } catch (Exception $e) {
            Log::error('Unexpected error when trying to get user role: '.$e->getMessage());

            throw new Exception('Unexpected error when trying to get user role: '.$e->getMessage());
        }
    }
}
