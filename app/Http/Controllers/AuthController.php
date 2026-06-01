<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



class AuthController extends Controller
{
    
    // LOGIN
    public function login(Request $request)
    {
        // Validate incoming request
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login
        if (!$token = auth('api')->attempt($credentials)) {

            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Return token
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'bearer',
        ]);
    }

     // LOGOUT
    public function logout()
    {
        auth('api')->logout();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    // CURRENT USER
    public function me()
    {
        return response()->json(auth('api')->user());
    }
}
