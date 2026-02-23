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

    if (isset($data['fcm_token']) && $data['fcm_token']) {
      $user->update([
        'fcm_token' => $data['fcm_token']
      ]);
    }

    $user->tokens()->delete();

    $role = ($user->userable_type === 'Admin') ? 'admin' : 'employee';

    $token = $user->createToken('auth_token', [$role])->plainTextToken;

    return [
      'token' => $token,
      'user' => $user,
      'role' => $role
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