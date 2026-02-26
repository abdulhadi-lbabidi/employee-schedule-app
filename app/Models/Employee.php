<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
  /** @use HasFactory<\Database\Factories\EmployeeFactory> */
  use HasFactory, SoftDeletes;
  protected $fillable = [
    'position',
    'department',
    'hourly_rate',
    'overtime_rate',
    'is_online',
    'current_location',
  ];

  protected $casts = [
    'is_online' => 'boolean',
    'hourly_rate' => 'double',
    'overtime_rate' => 'double',
  ];

  public function workshops()
  {
    return $this->belongsToMany(Workshop::class, 'attendances')
      ->withPivot([
        'date',
        'check_in',
        'check_out',
        'week_number',
        'note',
        'regular_hours',
        'overtime_hours',
        'status'
      ])
      ->withTimestamps();
  }

  public function attendances()
  {
    return $this->hasMany(Attendance::class);
  }

  public function loans()
  {
    return $this->hasMany(Loan::class);
  }
  public function rewards()
  {
    return $this->hasMany(Reward::class);
  }
  public function weeklyHistories()
  {
    return $this->hasMany(WeeklyHistory::class);
  }
  public function user()
  {
    return $this->morphOne(User::class, 'userable');
  }

  public function payments()
  {
    return $this->hasMany(Payment::class);
  }

  public function discounts()
  {
    return $this->hasMany(Discount::class);
  }
}