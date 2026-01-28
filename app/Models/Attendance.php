<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'workshop_id',
        'date',
        'check_in',
        'check_out',
        'week_number',
        'note',
        'regular_hours',
        'overtime_hours',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];


    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
