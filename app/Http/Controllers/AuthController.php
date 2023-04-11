<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // [POST] user login account
    public function login(Request $request) {
        
        $findUser = User::where('email', $request->email)->first();
        
        if(!$findUser)
        {
            return response()->json([
                'status' => 400,
                'message' => 'Email does not sign-up or does not exists.'
            ], 400);
        }

        if (!Hash::check($request->password, $findUser->password, [])) {
            return response()->json([
                'status' => 400,
                'message' => 'Password is incorrect.'
            ], 400);
        }

        $tokenResult = $findUser->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => 200,
            'message' => 'Login successfully.',
            'access_token' => $tokenResult,
            'token_type' => 'Bearer'
        ], 200);
    }

    // [POST] create user account
    public function signUp(Request $request) {
        
        $findUser = User::where('email', $request->email);

        if($findUser->count() > 0)
        {
            return response()->json([
                'status' => 400,
                'message' => 'Email sign-up is exists.'
            ], 400);
        }

        $createUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if($createUser) {
            return response()->json([
                'status' => 200,
                'message' => 'Sign-up successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Sign-up is failed. Plase sign-up to again.'
            ], 400);
        }
    }

    // [GET] detail user
    public function detailUser($id) {
        $findUserById = User::find($id);

        if($findUserById) {
            return response()->json([
                'status' => 200,
                'user' => $findUserById
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'User is not define.'
            ], 400);
        }
    }
}
