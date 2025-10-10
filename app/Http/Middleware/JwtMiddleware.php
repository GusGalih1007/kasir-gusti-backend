<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try
		{
			$validate = JWTAuth::parseToken()->authenticate();
		}
		catch (Exception $e) {
			if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
				return response()->json(['status' => 'Token is Invalid']);
			}else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
				return response()->json(['status' => 'Token is Expired']);
			}else{
				return response()->json(['status' => 'Authorization Token not found']);
			}
		}
		// $user = Auth::user();
        // // Adjust this based on your DB structure
        // if ($user->role !== $role) {
        //     return response()->json([
        //         'message' => 'Access Denied!'
        //     ], 403);
        // }
		return $next($request);
    }
}
