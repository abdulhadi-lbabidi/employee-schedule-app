<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthService
{
    public function loginUser(array $data)
    {
        $user = User::with('userable')->where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null;
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user
        ];
    }

    public function updateProfile(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return $user;
    }
}