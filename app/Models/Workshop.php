<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workshop extends Model
{
    /** @use HasFactory<\Database\Factories\WorkshopFactory> */
    use HasFactory, SoftDeletes;

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
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}