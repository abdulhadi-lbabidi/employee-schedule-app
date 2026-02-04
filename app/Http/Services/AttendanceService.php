<?php

namespace App\Http\Services;

use App\Models\Attendance;

use Carbon\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AttendanceService
{

    public function getAll()
    {
        return QueryBuilder::for(Attendance::class)
            ->with(['employee', 'workshop'])
            ->allowedFilters([
                AllowedFilter::exact('employee_id'),
                AllowedFilter::exact('workshop_id'),
                AllowedFilter::exact('week_number'),

                AllowedFilter::exact('status'),

                'date',

                AllowedFilter::partial('employee_name', 'employee.user.full_name'),

                AllowedFilter::callback('min_overtime', function ($query, $value) {
                    $query->where('overtime_hours', '>=', $value);
                }),

                // فلتر من تاريخ
                AllowedFilter::callback('start_date', function ($query, $value) {
                    $query->where('date', '>=', $value);
                }),

                // فلتر إلى تاريخ
                AllowedFilter::callback('end_date', function ($query, $value) {
                    $query->where('date', '<=', $value);
                }),
            ])
            ->defaultSort('-check_in')
            ->allowedSorts(['date', 'week_number', 'regular_hours'])
            ->paginate(15);
    }

    public function getEmployeeRecords($employeeId)
    {
        return QueryBuilder::for(Attendance::class)
            ->where('employee_id', $employeeId)
            ->with('workshop')
            ->allowedFilters([
                AllowedFilter::exact('workshop_id'),
                AllowedFilter::exact('status'),
                'date',
                AllowedFilter::callback('start_date', function ($query, $value) {
                    $query->where('date', '>=', $value);
                }),
                AllowedFilter::callback('end_date', function ($query, $value) {
                    $query->where('date', '<=', $value);
                }),

                AllowedFilter::callback('month', function ($query, $value) {
                    $query->whereMonth('date', $value);
                }),

                AllowedFilter::callback('year', function ($query, $value) {
                    $query->whereYear('date', $value);
                }),

            ])
            ->allowedSorts(['date', 'check_in'])
            ->defaultSort('-date', '-check_in')
            ->paginate(10);
    }

    // for online
    // public function checkIn($employeeId, $workshopId, $note = null)
    // {
    //     $open = Attendance::where('employee_id', $employeeId)
    //         ->whereNull('check_out')
    //         ->first();

    //     if ($open) {
    //         throw new \Exception('Employee already checked in');
    //     }

    //     return Attendance::create([
    //         'employee_id' => $employeeId,
    //         'workshop_id' => $workshopId,
    //         'date' => now()->toDateString(),
    //         'week_number' => now()->weekOfYear,
    //         'check_in' => now(),
    //         'note' => $note,
    //         'status' => 'قيد الرفع',
    //     ]);
    // }

    // public function checkOut($employeeId)
    // {
    //     $attendance = Attendance::where('employee_id', $employeeId)
    //         ->whereNull('check_out')
    //         ->latest('check_in')
    //         ->first();

    //     if (!$attendance) {
    //         throw new \Exception('No open attendance found');
    //     }

    //     $checkIn = $attendance->check_in;
    //     $checkOut = now();

    //     $hours = round($checkIn->diffInMinutes($checkOut) / 60, 2);


    //     $regular = min($hours, 8);
    //     $overtime = max($hours - 8, 0);

    //     $attendance->update([
    //         'check_out' => $checkOut,
    //         'regular_hours' => $regular,
    //         'overtime_hours' => $overtime,
    //         'status' => 'مؤرشف',
    //     ]);

    //     return $attendance;
    // }


    public function syncAttendance(array $data)
    {
        $data['check_in'] = Carbon::parse($data['check_in'])->toDateTimeString();
        if (isset($data['check_out'])) {
            $data['check_out'] = Carbon::parse($data['check_out'])->toDateTimeString();
        }

        return Attendance::updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'check_in' => $data['check_in'],
                'workshop_id' => $data['workshop_id'],
            ],
            $data
        );
    }
}
