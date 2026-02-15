<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
  /** @use HasFactory<\Database\Factories\LoanFactory> */
  use HasFactory, SoftDeletes;
  protected $fillable = [
    'employee_id',
    'admin_id',
    'amount',
    'paid_amount',
    'status',
    'date',
  ];

  protected $casts = [
    'date' => 'datetime',
    'amount' => 'double',
    'role' => 'string'
  ];


  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }
  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }
}