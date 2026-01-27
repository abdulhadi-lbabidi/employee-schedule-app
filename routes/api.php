<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\WorkshopController;
use Illuminate\Support\Facades\Route;


// Admin Routes
Route::apiResource('admins', AdminController::class);

// Employee Routes
Route::apiResource('employees', EmployeeController::class);

// Workshops Routes
Route::apiResource('workshops', WorkshopController::class);