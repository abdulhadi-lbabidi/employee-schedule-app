<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workshop extends Model
{
  /** @use HasFactory<\Database\Factories\WorkshopFactory> */
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'name',
    'location',
    'description',
    'latitude',
    'longitude',
    'radiusInMeters',
  ];

  public function employees()
  {
    return $this->belongsToMany(Employee::class, 'attendances')
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

  public function discounts()
  {
    return $this->hasMany(Discount::class);
  }

}