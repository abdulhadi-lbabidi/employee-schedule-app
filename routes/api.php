<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\WorkshopController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('login', [AuthController::class, 'login']);

Route::apiResource('admins', AdminController::class);
/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('me', [AuthController::class, 'me']);
    Route::post('update-profile', [AuthController::class, 'updateProfile']);

    /*
    |----------------------------------------------------------------------
    |   (Admin Panel)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::get('admins-archived', [AdminController::class, 'archived']);
        Route::post('admins/{id}/restore', [AdminController::class, 'restore']);
        Route::delete('admins/{id}/force-delete', [AdminController::class, 'forceDelete']);

        Route::apiResource('employees', EmployeeController::class);
        Route::get('employees-archived', [EmployeeController::class, 'archived']);
        Route::post('employees/{id}/restore', [EmployeeController::class, 'restore']);
        Route::delete('employees/{id}/force-delete', [EmployeeController::class, 'forceDelete']);

        Route::apiResource('workshops', WorkshopController::class);
        Route::get('workshops-archived', [WorkshopController::class, 'archived']);
        Route::post('workshops/{id}/restore', [WorkshopController::class, 'restore']);
        Route::delete('workshops/{id}/force-delete', [WorkshopController::class, 'forceDelete']);

        Route::get('attendances', [AttendanceController::class, 'index']);
    });

    /*
    |----------------------------------------------------------------------
    |   (Employee Panel)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:employee')->group(function () {
        Route::prefix('attendances')->group(function () {
            Route::post('check-in', [AttendanceController::class, 'checkIn']);
            Route::post('check-out/{employee}', [AttendanceController::class, 'checkOut']);
        });
    });

});
