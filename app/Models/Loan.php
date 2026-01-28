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
        'amount',
        'paid_amount',
        'role',
        'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}