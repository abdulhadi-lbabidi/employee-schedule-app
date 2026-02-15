<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyHistory extends Model
{
  /** @use HasFactory<\Database\Factories\WeeklyHistoryFactory> */
  use HasFactory;
  protected $fillable = [
    'employee_id',
    'week_number',
    'month',
    'year',
    'workshops',
    'amount_paid',
    'is_paid'
  ];

  protected $casts = [
    'workshops' => 'array',
    'is_paid' => 'boolean',
    'amount_paid' => 'decimal:2',
  ];

  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }
  public function workshop()
  {
    return $this->belongsTo(Workshop::class);
  }
}