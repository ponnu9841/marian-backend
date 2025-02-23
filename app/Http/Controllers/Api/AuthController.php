<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Cookie;

class AuthController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    public function user()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response([
                'data' => $user,
            ], 200);
        } else {
            return response([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:55',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'type'=> 'admin',
                'password' => Hash::make($request->password)
            ]);

            return response([
                'user' => $user,
            ], 200);
        } else {
            return response([
                'status' => false,
                'errors' => $validator->errors()
            ], 404);
        }
    }

    public function login(Request $request)
    {
        $token = Auth::attempt($request->only('email', 'password'));

        if (!$token) {
            return response([
                'message' => "Invalid Credentials"
            ], 401);
        }

        $user = Auth::user();

        return response([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'token' => $this->respondWithToken(auth()->refresh())
        ]);
    }
}
