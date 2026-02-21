<?php

namespace App\Http\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\Reward;
use App\Models\Workshop;
use Carbon\Carbon;

class DashboardService
{
  public function statistics()
  {
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();

    $employeeCount = Employee::count();
    $workshopCount = Workshop::count();

    $monthlyRewardsCount = Reward::whereBetween('date_issued', [$startOfMonth, $endOfMonth])->count();
    $totalRewardsAmount = Reward::whereBetween('date_issued', [$startOfMonth, $endOfMonth])->sum('amount');

    $monthlyLoansCount = Loan::whereBetween('date', [$startOfMonth, $endOfMonth])
      ->where('status', '!=', 'rejected')
      ->count();
    $totalLoansAmount = Loan::whereBetween('date', [$startOfMonth, $endOfMonth])
      ->where('status', '!=', 'rejected')
      ->sum('amount');

    $totalEstimatedAttendance = Attendance::whereBetween('date', [$startOfMonth, $endOfMonth])
      ->sum('estimated_amount');

    return [
      'month_name' => Carbon::now()->translatedFormat('F'),
      'general_counts' => [
        'total_employees' => $employeeCount,
        'total_workshops' => $workshopCount,
      ],
      'rewards_stats' => [
        'count' => $monthlyRewardsCount,
        'total_amount' => round($totalRewardsAmount, 2),
      ],
      'loans_stats' => [
        'count' => $monthlyLoansCount,
        'total_amount' => round($totalLoansAmount, 2),
      ],
      'attendance_earnings' => [
        'total_estimated_amount' => round($totalEstimatedAttendance, 2),
        'regular_hours' => Attendance::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('regular_hours'),
        'overtime_hours' => Attendance::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('overtime_hours'),
      ]
    ];
  }
}
