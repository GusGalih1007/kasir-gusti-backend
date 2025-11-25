<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Users;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Clock\now;

class AuthController extends Controller
{
    // Web auth start
    public function loginPage()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('auth.login');
    }

    public function loginWeb(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|string',
            'password' => 'required|string',
        ]);

        if ($validate->fails()) {
            // return response()->json(data: $validate->errors(), status: 422);
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {

            $credential = $request->only('email', 'password');

            $account = Users::where('email', $credential['email'])->first();

            // dd($account);

            if (!$account)
            {
                return redirect()->back()->with('no-account', 'No account found for this email, ask administrator for an account');
            }

            if (!Hash::check($credential['password'], $account->password))
            {
                return redirect()->back()->with('invalid-password', 'Password incorrect, please try again');
            }

            if ($account->status == 'InActive')
            {
                return redirect()->back()->with('InActive', 'Your account is disabled, ask administrator to enable it');
            }

            $user = auth()->guard('web')->attempt($credential);

            if (! $user) {
                return redirect()->back()->with('errors', 'Login failed, please try again later');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors('Something went wrong, try again later');
        }

        $lastLogin = Users::findOrFail(auth('web')->id());

        $lastLogin->update([
            'last_login' => now(),
        ]);

        return redirect('/');
    }

    public function logoutWeb()
    {
        try {
            $user = Users::findOrFail(auth('web')->id());

            auth()->guard('web')->logout();

            $user->update([
                'last_logout' => now(),
            ]);

            return redirect()->route('login.form');
        } catch (Exception $e) {
            // return response()->json(['Message' => 'Log-Out Failed',
            //                          'Error' => $e], 500);

            return redirect()->back()->withErrors($e);
        }
    }
    // End of auth web

    // Api auth start
    public function loginJwt(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $credential = $request->only('email', 'password');

        try {
            $token = auth()->guard('api')->attempt($credential);

            if (! $token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Credential!',
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['Message' => 'Could not create token',
                'Error' => $e], 500);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->guard('api')->user());
    }

    public function respondWithToken($token)
    {
        return new ApiResource(200, 'Login Success', [
            'user' => auth()->guard('api')->user(),
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function logoutApi()
    {
        try {
            auth()->guard('api')->logout();

            return new ApiResource(204, 'Successfully Logout', null);
        } catch (JWTException $e) {
            return response()->json(['Message' => 'Log-Out Failed',
                'Error' => $e], 500);
        }
    }
    // End of api auth
}
