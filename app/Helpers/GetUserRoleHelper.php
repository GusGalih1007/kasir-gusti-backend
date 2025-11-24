<?php

namespace App\Helpers;

use App\Models\Users;
use Exception;

class GetUserRoleHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        try
        {
            $user = Users::findOrFail(auth('web')->id());

            $getRole = $user->role->role_id;

            return $getRole;
        }
        catch (Exception $e)
        {

        }
    }
}
