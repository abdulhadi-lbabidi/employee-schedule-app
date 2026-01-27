<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\WorkshopController;
use Illuminate\Support\Facades\Route;


// Admin Routes
Route::apiResource('admins', AdminController::class);
Route::get('admins-archived', [AdminController::class, 'archived']);
Route::post('admins/{id}/restore', [AdminController::class, 'restore']);
Route::delete('admins/{id}/force-delete', [AdminController::class, 'forceDelete']);

// Employee Routes
Route::apiResource('employees', EmployeeController::class);
Route::get('employees-archived', [EmployeeController::class, 'archived']);
Route::post('employees/{id}/restore', [EmployeeController::class, 'restore']);
Route::delete('employees/{id}/force-delete', [EmployeeController::class, 'forceDelete']);

// Workshops Routes
Route::apiResource('workshops', WorkshopController::class);
Route::get('workshops-archived', [WorkshopController::class, 'archived']);
Route::post('workshops/{id}/restore', [WorkshopController::class, 'restore']);
Route::delete('workshops/{id}/force-delete', [WorkshopController::class, 'forceDelete']);