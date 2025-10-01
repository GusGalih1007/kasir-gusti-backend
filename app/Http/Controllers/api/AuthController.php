<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);

        if ($validate->fails())
        {
            return response()->json($validate->errors(), 422);
        }

        $credential = $request->only('email', 'password');

        try
        {
            $token = auth()->guard('api')->attempt($credential);

            if(!$token)
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Credential!'
                ], 401);
            }
        }
        catch (JWTException $e)
        {
            return response()->json(['Message' => 'Could not create token',
                                            'Error' => $e ], 500);
        }

        return new ApiResource(200, 'Login Success', [
            'user' => auth()->guard('api')->user(), 
            'access_token' => $token,
        ]);
    }

    public function logout()
    {
        try
        {
            auth()->guard('api')->logout();
            return new ApiResource(204, 'Successfully Logout', null);
        } catch (JWTException $e)
        {
            return response()->json(['Message' => 'Log-Out Failed', 
                                     'Error' => $e], 500);
        }
    }
}