<?php


namespace App\Http\Services;

use App\Models\Employee;

class DashboardService
{

  public function statistics()
  {
    $employee = Employee::whereNotNull('deleted_at')->count();

    $worker = Employee::whereNotNull('deleted_at')->count();

    $loans = Employee::whereNotNull('deleted_at')->count();

    return [
      'employee' => $employee,
      'worker' => $worker,
      'loans' => $loans
    ];

  }
}
