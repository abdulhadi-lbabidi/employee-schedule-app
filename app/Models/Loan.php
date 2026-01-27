<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    /** @use HasFactory<\Database\Factories\LoanFactory> */
    use HasFactory;
        protected $fillable = [
        'position',
        'department',
        'hourly_rate',
        'overtime_rate',
        'is_online',
        'current_location',
    ];

    public function employee() {
    return $this->belongsTo(Employee::class);
}
}
