<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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

        if (isset($data['profile_image_url'])) {
            if ($user->profile_image_url) {
                $oldPath = str_replace(url('storage/'), '', $user->profile_image_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $data['profile_image_url']->store('profiles', 'public');

            $data['profile_image_url'] = url('storage/' . $path);
        }

        $user->update($data);
        return $user;
    }
}