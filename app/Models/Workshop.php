<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    /** @use HasFactory<\Database\Factories\WorkshopFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'latitude',
        'longitude',
        'radiusInMeters',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
// Workshop.php
public function employees() {
    return $this->hasMany(Employee::class);
}
}
