<?php

namespace App\Http\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AttendanceService
{


  // get employee records
  public function getEmployeeRecords($employeeId)
  {
    $records = QueryBuilder::for(Attendance::class)
      ->where('employee_id', $employeeId)
      ->with('workshop')
      ->allowedFilters([
        AllowedFilter::exact('workshop_id'),
        AllowedFilter::exact('status'),
        AllowedFilter::callback('start_date', function ($query, $value) {
          $query->whereDate('date', '>=', $value);
        }),
        AllowedFilter::callback('end_date', function ($query, $value) {
          $query->whereDate('date', '<=', $value);
        }),
        AllowedFilter::callback('month', function ($query, $value) {
          $query->whereMonth('date', $value);
        }),
        AllowedFilter::callback('year', function ($query, $value) {
          $query->whereYear('date', $value);
        }),
      ])
      ->orderBy('date')
      ->get();

    $grouped = $records->groupBy(function ($item) {
      $date = Carbon::parse($item->date);

      $startOfWeek = $date->copy()->startOfWeek(Carbon::SATURDAY);
      $endOfWeek = $date->copy()->endOfWeek(Carbon::FRIDAY);

      return $startOfWeek->format('Y-m-d') . ' إلى ' . $endOfWeek->format('Y-m-d');
    });

    return $grouped;
  }

  // employee summery
  public function getEmployeeWeeklyHoursAndPay($employeeId)
  {
    $employee = Employee::findOrFail($employeeId);

    $records = Attendance::where('employee_id', $employeeId)
      ->with('workshop')
      ->orderBy('date', 'desc')
      ->get();

    return $records->groupBy(function ($item) {
      $date = Carbon::parse($item->date);
      $startOfWeek = $date->copy()->startOfWeek(Carbon::SATURDAY);
      $endOfWeek = $date->copy()->endOfWeek(Carbon::FRIDAY);

      return $startOfWeek->format('Y-m-d') . ' إلى ' . $endOfWeek->format('Y-m-d');
    })->map(function ($weekRecords, $weekRange) use ($employee) {

      $workshopsSummary = $weekRecords->groupBy('workshop_id')
        ->map(function ($workshopGroup) use ($employee) {
          $workshop = $workshopGroup->first()->workshop;

          $totalReg = (float) $workshopGroup->sum('regular_hours');
          $totalOvt = (float) $workshopGroup->sum('overtime_hours');

          $regPay = round($totalReg * $employee->hourly_rate, 2);
          $ovtPay = round($totalOvt * $employee->overtime_rate, 2);

          return [
            'workshop' => [
              'id' => $workshop->id,
              'name' => $workshop->name,
              'location' => $workshop->location,
            ],
            'total_regular_hours' => $totalReg,
            'total_overtime_hours' => $totalOvt,
            'regular_pay' => $regPay,
            'overtime_pay' => $ovtPay,
            'total_pay' => round($regPay + $ovtPay, 2),
          ];
        })->values();

      return [
        'week_range' => $weekRange,
        'workshops' => $workshopsSummary,
        'weekly_totals' => [
          'total_regular_hours' => $workshopsSummary->sum('total_regular_hours'),
          'total_overtime_hours' => $workshopsSummary->sum('total_overtime_hours'),
          'total_regular_pay' => round($workshopsSummary->sum('regular_pay'), 2),
          'total_overtime_pay' => round($workshopsSummary->sum('overtime_pay'), 2),
          'grand_total_pay' => round($workshopsSummary->sum('total_pay'), 2),
        ]
      ];
    })->values();
  }


  // details employee
  public function getEmployeeWorkshopsDetailedSummary($employeeId)
  {
    return Attendance::query()
      ->where('employee_id', $employeeId)
      ->join('workshops', 'attendances.workshop_id', '=', 'workshops.id')
      ->select(
        'workshops.id',
        'workshops.name',
        'workshops.location',
        DB::raw('SUM(regular_hours) as total_regular_hours'),
        DB::raw('SUM(overtime_hours) as total_overtime_hours')
      )
      ->groupBy('workshops.id', 'workshops.name', 'workshops.location')
      ->get();
  }

  public function getEmployeeTotalHours($employeeId)
  {
    $row = Attendance::query()
      ->where('employee_id', $employeeId)
      ->selectRaw('COALESCE(SUM(regular_hours), 0) as total_regular_hours, COALESCE(SUM(overtime_hours), 0) as total_overtime_hours')
      ->first();

    return [
      'total_regular_hours' => round((float) $row->total_regular_hours, 2),
      'total_overtime_hours' => round((float) $row->total_overtime_hours, 2),
    ];
  }

  //details workshop
  public function getWorkshopHoursByEmployee($workshopId)
  {
    $aggregated = Attendance::query()
      ->where('workshop_id', $workshopId)
      ->selectRaw('employee_id, SUM(regular_hours) as total_regular_hours, SUM(overtime_hours) as total_overtime_hours')
      ->groupBy('employee_id')
      ->get();

    $employeeIds = $aggregated->pluck('employee_id');
    $employees = Employee::with('user')->whereIn('id', $employeeIds)->get()->keyBy('id');

    return $aggregated->map(function ($row) use ($employees) {
      $employee = $employees->get($row->employee_id);

      return [
        'employee' => $employee,
        'total_regular_hours' => round((float) $row->total_regular_hours, 2),
        'total_overtime_hours' => round((float) $row->total_overtime_hours, 2),
      ];
    })->filter(fn($row) => $row['employee'] !== null)->values();
  }


  // register adn logout attendances
  public function syncAttendance(array $data)
  {
    $date = Carbon::parse($data['date']);

    $data['week_number'] = $date->weekOfYear;

    $employee = Employee::findOrFail($data['employee_id']);

    $data['check_in'] = Carbon::parse($data['check_in'])->toDateTimeString();

    if (isset($data['check_out'])) {
      $data['check_out'] = Carbon::parse($data['check_out'])->toDateTimeString();
    }

    $regularCost = ($data['regular_hours'] ?? 0) * $employee->hourly_rate;
    $overtimeCost = ($data['overtime_hours'] ?? 0) * $employee->overtime_rate;

    $data['estimated_amount'] = $regularCost + $overtimeCost;

    return Attendance::updateOrCreate(
      [
        'employee_id' => $data['employee_id'],
        'check_in' => $data['check_in'],
        'workshop_id' => $data['workshop_id'],
        'date' => $data['date'],
      ],
      $data
    );
  }
}