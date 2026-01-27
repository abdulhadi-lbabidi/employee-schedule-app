<?php

namespace App\Http\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    public function getAll()
    {
        return Employee::with('users')
            ->whereNull('deleted_at')
            ->get();
    }
    public function getArchived()
    {
        return Employee::onlyTrashed()
            ->with([
                'users' => function ($q) {
                    $q->withTrashed();
                }
            ])
            ->get();
    }

    public function create(array $data)
    {
        $employee = Employee::create([
            'position' => $data['position'],
            'department' => $data['department'],
            'hourly_rate' => $data['hourly_rate'],
            'overtime_rate' => $data['overtime_rate'],
            'is_online' => $data['is_online'] ?? 0,
            'current_location' => $data['current_location'],
        ]);

        $employeeUser = User::create([
            'full_name' => $data['full_name'],
            'phone_number' => $data['phone_number'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'userable_id' => $employee->id,
            'userable_type' => Employee::class,
        ]);

        return $employee->load('users');
    }

    public function update(Employee $employee, array $data)
    {
        $employee->update($data);

        if ($employee->users) {
            $employee->users->update([
                'full_name' => $data['full_name'] ?? $employee->users->full_name,
                'phone_number' => $data['phone_number'] ?? $employee->users->phone_number,
                'email' => $data['email'] ?? $employee->users->email,
                'password' => isset($data['password']) ? Hash::make($data['password']) : $employee->users->password,
            ]);
        }

        return $employee->load('users');
    }

    public function delete(Employee $employee)
    {
        if ($employee->users) {
            $employee->users->delete();
        }

        return $employee->delete();
    }

    public function forceDelete(Employee $employee)
    {
        return $employee->forceDelete();
    }

    public function restore(Employee $employee)
    {
        $employee->restore();

        if ($employee->users()->withTrashed()->exists()) {
            $employee->users()->withTrashed()->restore();
        }

        return $employee->load('users');
    }

}
