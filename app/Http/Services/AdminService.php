<?php

namespace App\Http\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function getAll()
    {
        return Admin::with('users')
            ->whereNull('deleted_at')

            ->get();
    }

    public function getArchived()
    {
        return Admin::onlyTrashed()
            ->with([
                'users' => function ($q) {
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
            'userable_type' => Admin::class,
        ]);


        return $admin->load('users');
    }

    public function update(Admin $admin, array $data)
    {
        $admin->update([
            'name' => $data['name'] ?? $admin->name,
        ]);

        if ($admin->users) {
            $admin->users->update([
                'full_name' => $data['full_name'] ?? $admin->users->full_name,
                'phone_number' => $data['phone_number'] ?? $admin->users->phone_number,
                'email' => $data['email'] ?? $admin->users->email,
                'password' => isset($data['password']) ? Hash::make($data['password']) : $admin->users->password,
            ]);
        }

        return $admin->load('users');
    }

    public function delete(Admin $admin)
    {
        if ($admin->users) {
            $admin->users->delete();
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

        if ($admin->users()->withTrashed()->exists()) {
            $admin->users()->withTrashed()->restore();
        }

        return $admin->load('users');
    }


}
