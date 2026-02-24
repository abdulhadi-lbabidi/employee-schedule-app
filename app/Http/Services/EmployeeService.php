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


  public function getEmployeesDues()
  {

    $employeesCollection = Employee::with(['user'])
      ->withSum('attendances as total_all_regular_hours', 'regular_hours')
      ->withSum('attendances as total_all_overtime_hours', 'overtime_hours')
      ->withSum('payments as total_paid_to_date', 'amount_paid')
      ->get();


    $details = $employeesCollection->map(function ($employee) {
      $regHours = (double) ($employee->total_all_regular_hours ?? 0);
      $overHours = (double) ($employee->total_all_overtime_hours ?? 0);

      $totalEarned = round(($regHours * $employee->hourly_rate) + ($overHours * $employee->overtime_rate), 2);
      $totalPaid = round((double) ($employee->total_paid_to_date ?? 0), 2);

      $remainingDue = round($totalEarned - $totalPaid, 2);

      return [
        'id' => $employee->id,
        'full_name' => $employee->user?->full_name,
        'total_earned' => $totalEarned,
        'total_paid' => $totalPaid,
        'remaining_due' => $remainingDue,
        'total_hours' => round($regHours + $overHours, 2)
      ];
    });

    $debtors = $details->filter(fn($e) => $e['remaining_due'] >= 0)->values();

    return [
      'employees' => $debtors,
      'summary' => [
        'total_employees_count' => $debtors->count(),
        'grand_total_debt' => round($debtors->sum('remaining_due'), 2),
      ]
    ];
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
