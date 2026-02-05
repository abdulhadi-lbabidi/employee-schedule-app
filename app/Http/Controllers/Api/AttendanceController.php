<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Attendance\CreateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Http\Services\AttendanceService;
use App\Http\Controllers\Controller;
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

    public function employeeHistory($employeeId)
    {
        $records = $this->attendanceService->getEmployeeRecords($employeeId);
        return AttendanceResource::collection($records);
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