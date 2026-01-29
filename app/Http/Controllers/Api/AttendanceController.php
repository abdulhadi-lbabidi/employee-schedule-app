<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\CreateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Http\Services\AttendanceService;
use App\Models\Attendance;

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

    public function checkIn(CreateAttendanceRequest $request)
    {
        $attendance = $this->attendanceService->checkIn(
            $request->employee_id,
            $request->workshop_id,
            $request->note ?? null
        );

        return new AttendanceResource($attendance);
    }

    public function checkOut($employeeId)
    {
        $attendance = $this->attendanceService->checkOut($employeeId);
        return new AttendanceResource($attendance);
    }


    public function show(Attendance $attendance)
    {
        return new AttendanceResource($attendance->load(['employee', 'workshop']));
    }
    public function sync(CreateAttendanceRequest $request)
    {
        $attendance = $this->attendanceService->syncAttendance($request->validated());

        return new AttendanceResource($attendance);
    }
}