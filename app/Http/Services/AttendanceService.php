<?php

namespace App\Http\Services;

use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Attendance;
use Carbon\Carbon;

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

                AllowedFilter::callback('start_date', function ($query, $value) {
                    $query->where('date', '>=', $value);
                }),

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
