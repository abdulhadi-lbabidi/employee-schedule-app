<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

        protected $fillable = [
        'user_id',
        'week_number',
        'total_amount',
        'amount_paid',
        'is_paid',
        'payment_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
