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
    ) {
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->loginUser($request->validated());

        if (!$result) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            "token" => $result['token'],
            "user" => $result['user'],
            'status' => 200
        ]);
    }
    public function me()
    {
        $user = Auth::user()->load('userable');

        return response()->json([
            'user' => $user,
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
}