<?php

namespace App\Http\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    public function getAll()
    {
        return Employee::with(['user', 'workshops'])
            ->whereNull('deleted_at')
            ->get();
    }
    public function getArchived()
    {
        return Employee::onlyTrashed()
            ->with([
                'user' => function ($q) {
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
            'userable_type' => 'Employee',
        ]);

        return $employee->load('user');
    }

    public function update(Employee $employee, array $data)
    {
        $employee->update($data);

        if ($employee->user) {
            $employee->user->update([
                'full_name' => $data['full_name'] ?? $employee->user->full_name,
                'phone_number' => $data['phone_number'] ?? $employee->user->phone_number,
                'email' => $data['email'] ?? $employee->user->email,
                'password' => isset($data['password']) ? Hash::make($data['password']) : $employee->user->password,
            ]);
        }

        return $employee->load('user');
    }

    public function delete(Employee $employee)
    {
        if ($employee->user) {
            $employee->user->delete();
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

        if ($employee->user()->withTrashed()->exists()) {
            $employee->user()->withTrashed()->restore();
        }

        return $employee->load('user');
    }

}
