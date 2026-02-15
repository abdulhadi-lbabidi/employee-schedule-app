<?php

namespace App\Http\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Workshop;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

                AllowedFilter::callback('start_date', function ($query, $value) {
                    $query->where('date', '>=', $value);
                }),

                AllowedFilter::callback('end_date', function ($query, $value) {
                    $query->where('date', '<=', $value);
                }),
            ])
            ->defaultSort('-check_in')
            ->allowedSorts(['date', 'week_number', 'regular_hours'])
            ->get();
    }



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

    public function getEmployeeHoursByWorkshop($employeeId)
    {
        $aggregated = Attendance::query()
            ->where('employee_id', $employeeId)
            ->selectRaw('workshop_id, SUM(regular_hours) as total_regular_hours, SUM(overtime_hours) as total_overtime_hours')
            ->groupBy('workshop_id')
            ->get();

        $workshopIds = $aggregated->pluck('workshop_id')->unique()->filter()->values()->all();
        $workshops = Workshop::query()->whereIn('id', $workshopIds)->get()->keyBy('id');

        return $aggregated->map(function ($row) use ($workshops) {
            $workshop = $workshops->get($row->workshop_id);
            return [
                'workshop' => $workshop,
                'total_regular_hours' => round((float) $row->total_regular_hours, 2),
                'total_overtime_hours' => round((float) $row->total_overtime_hours, 2),
            ];
        })->filter(fn($row) => $row['workshop'] !== null)->values();
    }


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

    public function getWorkshopHoursByEmployee($workshopId)
    {
        $aggregated = Attendance::query()
            ->where('workshop_id', $workshopId)
            ->selectRaw('employee_id, SUM(regular_hours) as total_regular_hours, SUM(overtime_hours) as total_overtime_hours')
            ->groupBy('employee_id')
            ->get();

        $employeeIds = $aggregated->pluck('employee_id')->unique()->filter()->values()->all();
        $employees = Employee::query()->with('user')->whereIn('id', $employeeIds)->get()->keyBy('id');

        return $aggregated->map(function ($row) use ($employees) {
            $employee = $employees->get($row->employee_id);
            return [
                'employee' => $employee,
                'total_regular_hours' => round((float) $row->total_regular_hours, 2),
                'total_overtime_hours' => round((float) $row->total_overtime_hours, 2),
            ];
        })->filter(fn($row) => $row['employee'] !== null)->values();
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
                'date' => $data['date'],
            ],
            $data
        );
    }
}
