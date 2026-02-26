<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
  protected $fillable = ['employee_id', 'workshop_id', 'admin_id', 'amount', 'reason', 'date_issued'];

  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }

  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }

  public function workshop()
  {
    return $this->belongsTo(Workshop::class);
  }
}