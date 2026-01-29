<?php

namespace App\Http\Services;

use App\Models\Attendance;

use Carbon\Carbon;

class AttendanceService
{

    public function getAll()
    {
        return Attendance::with(['employee', 'workshop'])
            ->orderBy('check_in', 'desc')
            ->get();
    }
    public function checkIn($employeeId, $workshopId, $note = null)
    {
        $open = Attendance::where('employee_id', $employeeId)
            ->whereNull('check_out')
            ->first();

        if ($open) {
            throw new \Exception('Employee already checked in');
        }

        return Attendance::create([
            'employee_id' => $employeeId,
            'workshop_id' => $workshopId,
            'date' => now()->toDateString(),
            'week_number' => now()->weekOfYear,
            'check_in' => now(),
            'note' => $note,
            'status' => 'قيد الرفع',
        ]);
    }

    public function checkOut($employeeId)
    {
        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();

        if (!$attendance) {
            throw new \Exception('No open attendance found');
        }

        $checkIn = $attendance->check_in;
        $checkOut = now();

        $hours = round($checkIn->diffInMinutes($checkOut) / 60, 2);


        $regular = min($hours, 8);
        $overtime = max($hours - 8, 0);

        $attendance->update([
            'check_out' => $checkOut,
            'regular_hours' => $regular,
            'overtime_hours' => $overtime,
            'status' => 'مؤرشف',
        ]);

        return $attendance;
    }


    public function syncAttendance(array $data)
    {
        return Attendance::updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'check_in' => $data['check_in'],
            ],
            $data
        );
    }



}
