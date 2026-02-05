<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request)
    {
        $result = $this->authService->loginUser($request->validated());

        if (!$result) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            "token" => $result['token'],
            "user" => $result['user'],
            'status' => 200,
            'role' => $result['role']
        ]);
    }
    public function me()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->load('userable');

        $role = ($user->userable_type === 'Admin') ? 'admin' : 'employee';

        return response()->json([
            'user' => $user,
            'role' => $role,
            'status' => 200
        ], 200);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->authService->updateProfile(
            Auth::user(),
            $request->validated()
        );
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
            'status' => 200
        ]);
    }


    public function logout()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
            'status' => 200
        ]);
    }
}