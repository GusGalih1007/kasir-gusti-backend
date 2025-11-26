<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Users;
use Exception;
use Illuminate\Support\Facades\Log;

class GetUserRoleHelper
{
    public static function getRoleName()
    {
        try {
            $user = Users::findOrFail(auth('web')->id());

            Log::info('Get user from auth: '. $user->username);

            $getRoleName = $user->role->name;

            Log::info('Get role name from helper: '.$getRoleName);

            return $getRoleName;
        } catch (Exception $e) {
            Log::error('Unexpected error when trying to get user role: '.$e->getMessage());

            throw new Exception('Unexpected error when trying to get user role: '.$e->getMessage());
        }
    }

    public static function getPhotoName()
    {
        try {
            $user = Users::findOrFail(auth('web')->id());

            Log::info('Get user from auth: '. $user->username);

            $getUserPhoto = $user->photo;

            Log::info('Get user photo path: '. $getUserPhoto);

            return $getUserPhoto;
        } catch (Exception $e)
        {
            Log::error('Unexpected error when trying to get user photo: '.$e->getMessage());

            throw new Exception('Unexpected error when trying to get user photo: '.$e->getMessage());
        }
    }
}
