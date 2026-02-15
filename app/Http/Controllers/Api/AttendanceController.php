<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Attendance\CreateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\WorkshopResource;
use App\Http\Services\AttendanceService;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\UserResource;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Workshop;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService
    ) {
    }

    public function index()
    {
        $attendances = $this->attendanceService->getAll();
        return AttendanceResource::collection($attendances);
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

        $rows = $this->attendanceService->getEmployeeHoursByWorkshop($employee->id);

        return response()->json([
            'employee_id' => $employee->id,
            'workshops' => $rows->map(fn($row) => [
                'workshop' => new WorkshopResource($row['workshop']),
                'total_regular_hours' => $row['total_regular_hours'],
                'total_overtime_hours' => $row['total_overtime_hours'],
            ]),
        ]);
    }


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

    public function workshopHoursByEmployee(Workshop $workshop)
    {
        $rows = $this->attendanceService->getWorkshopHoursByEmployee($workshop->id);

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
            ]),
        ]);
    }

    public function show(Attendance $attendance)
    {
        return new AttendanceResource($attendance->load(['employee', 'workshop']));
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