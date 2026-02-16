<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Attendance\CreateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\WorkshopResource;
use App\Http\Services\AttendanceService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Workshop;

class AttendanceController extends Controller
{
  public function __construct(
    private AttendanceService $attendanceService
  ) {
  }

  public function employeeHistory($employeeId)
  {
    $records = $this->attendanceService->getEmployeeRecords($employeeId);

    return $records->map(function ($weekRecords, $weekLabel) {
      return [
        'week' => $weekLabel,
        'total_regular_hours' => round($weekRecords->sum('regular_hours'), 2),
        'total_overtime_hours' => round($weekRecords->sum('overtime_hours'), 2),
        'attendances' => AttendanceResource::collection($weekRecords),
      ];
    })->values();
  }

  public function hoursByWorkshop(Request $request, Employee $employee)
  {
    $user = $request->user();
    $isAdmin = $user->userable_type === 'Admin';
    $isOwn = $user->userable_type === 'Employee' && (int) $user->userable_id === (int) $employee->id;

    if (!$isAdmin && !$isOwn) {
      abort(403, 'غير مصرح لك بعرض ساعات هذا الموظف.');
    }

    $weeks = $this->attendanceService->getEmployeeWeeklyHoursAndPay($employee->id);

    $grandTotals = [
      'total_regular_hours' => $weeks->sum(fn($w) => $w['weekly_totals']['total_regular_hours']),
      'total_overtime_hours' => $weeks->sum(fn($w) => $w['weekly_totals']['total_overtime_hours']),
      'total_regular_pay' => round($weeks->sum(fn($w) => $w['weekly_totals']['total_regular_pay']), 2),
      'total_overtime_pay' => round($weeks->sum(fn($w) => $w['weekly_totals']['total_overtime_pay']), 2),
      'grand_total_pay' => round($weeks->sum(fn($w) => $w['weekly_totals']['grand_total_pay']), 2),
    ];

    return response()->json([
      'employee_id' => $employee->id,
      'full_name' => $employee->user->full_name ?? '',
      'hourly_rate' => (float) $employee->hourly_rate,
      'overtime_rate' => (float) $employee->overtime_rate,
      'weeks' => $weeks,
      'grand_totals' => $grandTotals
    ]);
  }


  // details employee
  public function employeeHoursAndPaySummary(Request $request, Employee $employee)
  {
    $user = $request->user();
    $isAdmin = $user->userable_type === 'Admin';
    $isOwn = $user->userable_type === 'Employee' && (int) $user->userable_id === (int) $employee->id;
    if (!$isAdmin && !$isOwn)
      abort(403, 'غير مصرح لك.');

    $globalHours = $this->attendanceService->getEmployeeTotalHours($employee->id);
    $workshopsData = $this->attendanceService->getEmployeeWorkshopsDetailedSummary($employee->id);

    $workshopsSummary = $workshopsData->map(function ($ws) use ($employee) {
      $regPay = round($ws->total_regular_hours * $employee->hourly_rate, 2);
      $ovPay = round($ws->total_overtime_hours * $employee->overtime_rate, 2);

      return [
        'workshop_id' => $ws->id,
        'workshop_name' => $ws->name,
        'location' => $ws->location,
        'regular_hours' => (float) $ws->total_regular_hours,
        'overtime_hours' => (float) $ws->total_overtime_hours,
        'regular_pay' => $regPay,
        'overtime_pay' => $ovPay,
        'total_pay' => round($regPay + $ovPay, 2),
      ];
    });

    $grandRegularPay = round($globalHours['total_regular_hours'] * $employee->hourly_rate, 2);
    $grandOvertimePay = round($globalHours['total_overtime_hours'] * $employee->overtime_rate, 2);

    return response()->json([
      'employee' => new EmployeeResource($employee),
      'workshops_summary' => $workshopsSummary,
      'grand_totals' => [
        'total_regular_hours' => $globalHours['total_regular_hours'],
        'total_overtime_hours' => $globalHours['total_overtime_hours'],
        'total_regular_pay' => $grandRegularPay,
        'total_overtime_pay' => $grandOvertimePay,
        'grand_total_pay' => round($grandRegularPay + $grandOvertimePay, 2),
      ]
    ]);
  }

  // details workshop
  public function workshopHoursByEmployee(Workshop $workshop)
  {
    $rows = $this->attendanceService->getWorkshopHoursByEmployee($workshop->id);

    $grandTotalRegular = $rows->sum('total_regular_hours');
    $grandTotalOvertime = $rows->sum('total_overtime_hours');

    return response()->json([
      'workshop' => new WorkshopResource($workshop),
      'employees' => $rows->map(fn($row) => [
        'employee' => [
          'id' => $row['employee']->id,
          'position' => $row['employee']->position,
          'department' => $row['employee']->department,
          'user' => new UserResource($row['employee']->user),
        ],
        'total_regular_hours' => $row['total_regular_hours'],
        'total_overtime_hours' => $row['total_overtime_hours'],
        'total_combined_hours' => round($row['total_regular_hours'] + $row['total_overtime_hours'], 2),
      ]),
      'workshop_totals' => [
        'all_employees_regular_hours' => round($grandTotalRegular, 2),
        'all_employees_overtime_hours' => round($grandTotalOvertime, 2),
        'total_workshop_hours' => round($grandTotalRegular + $grandTotalOvertime, 2),
        'employees_count' => $rows->count(),
      ]
    ]);
  }


  public function sync(CreateAttendanceRequest $request)
  {
    $result = [];

    foreach ($request->validated() as $item) {
      $result[] = $this->attendanceService->syncAttendance($item);
    }

    return AttendanceResource::collection($result);
  }

}
