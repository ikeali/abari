<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Http\Requests\GeneralRequest;
use Illuminate\Http\JsonResponse;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{

    public function register(GeneralRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        
        $user = User::where('email', $credentials['email'])->first();


        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'invalid credentials'], 401);
        }

          // Generate API token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'Login successful!','token' => $token, 'user' => $user], 200);

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }


}
