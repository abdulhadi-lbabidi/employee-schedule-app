<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{

  use SoftDeletes;
  /** @use HasFactory<\Database\Factories\AdminFactory> */
  use HasFactory;
  protected $fillable = [
    'name',
  ];
  public function user()
  {
    return $this->morphOne(User::class, 'userable');
  }

  public function discounts()
  {
    return $this->hasMany(Admin::class);
  }
}
