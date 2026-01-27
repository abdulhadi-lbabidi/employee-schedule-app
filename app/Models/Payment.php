<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'week_number',
        'total_amount',
        'amount_paid',
        'is_paid',
        'payment_date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


}
