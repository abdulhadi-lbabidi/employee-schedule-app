<?php

namespace App\Http\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function getAll()
    {
        return Admin::with('user')
            ->whereNull('deleted_at')

            ->get();
    }

    public function getArchived()
    {
        return Admin::onlyTrashed()
            ->with([
                'user' => function ($q) {
                    $q->withTrashed();
                }
            ])
            ->get();
    }

    public function create(array $data)
    {
        $admin = Admin::create([
            'name' => $data['name'],
        ]);

        $adminUser = User::create([
            'full_name' => $data['full_name'],
            'phone_number' => $data['phone_number'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'userable_id' => $admin->id,
            'userable_type' => 'Admin',
        ]);


        return $admin->load('user');
    }

    public function update(Admin $admin, array $data)
    {
        $admin->update([
            'name' => $data['name'] ?? $admin->name,
        ]);

        if ($admin->user) {
            $admin->user->update([
                'full_name' => $data['full_name'] ?? $admin->user->full_name,
                'phone_number' => $data['phone_number'] ?? $admin->user->phone_number,
                'email' => $data['email'] ?? $admin->user->email,
                'password' => isset($data['password']) ? Hash::make($data['password']) : $admin->user->password,
            ]);
        }

        return $admin->load('user');
    }

    public function delete(Admin $admin)
    {
        if ($admin->user) {
            $admin->user->delete();
        }

        return $admin->delete();
    }


    public function forceDelete(Admin $admin)
    {
        return $admin->forceDelete();
    }



    public function restore(Admin $admin)
    {
        $admin->restore();

        if ($admin->user()->withTrashed()->exists()) {
            $admin->user()->withTrashed()->restore();
        }

        return $admin->load('user');
    }


}