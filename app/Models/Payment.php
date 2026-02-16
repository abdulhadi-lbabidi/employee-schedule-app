<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
  /** @use HasFactory<\Database\Factories\PaymentFactory> */
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'employee_id',
    'admin_id',
    'week_number',
    'total_amount',
    'amount_paid',
    'is_paid',
    'payment_date',
  ];

  protected $casts = [
    'payment_date' => 'datetime',
    'is_paid' => 'boolean',
    'total_amount' => 'double',
    'amount_paid' => 'double',
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
