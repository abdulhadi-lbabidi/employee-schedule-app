<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RewardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WeeklyHistoryController;
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
    Route::post('update-fcm-token', [UserController::class, 'updateFcmToken']);

    Route::get('me', [AuthController::class, 'me']);
    Route::post('update-profile', [AuthController::class, 'updateProfile']);

    Route::get('workshops', [WorkshopController::class, 'index']);
    Route::get('workshops/{workshop}', [WorkshopController::class, 'show']);

    Route::get('my-attendance/{employeeId}', [AttendanceController::class, 'employeeHistory']);
    Route::get('/attendance', [AttendanceController::class, 'index']);

    Route::get('loans', [LoanController::class, 'index']);
    Route::get('loans/{loan}', [LoanController::class, 'show']);
    Route::post('loans', [LoanController::class, 'store']);

    Route::post('/logout', [AuthController::class, 'logout']);

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

        Route::post('workshops', [WorkshopController::class, 'store']);
        Route::put('workshops/{workshop}', [WorkshopController::class, 'update']);
        Route::delete('workshops/{workshop}', [WorkshopController::class, 'destroy']);
        Route::get('workshops-archived', [WorkshopController::class, 'archived']);
        Route::post('workshops/{id}/restore', [WorkshopController::class, 'restore']);
        Route::delete('workshops/{id}/force-delete', [WorkshopController::class, 'forceDelete']);

        Route::get('attendances', [AttendanceController::class, 'index']);

        Route::get('loans-archived', [LoanController::class, 'archived']);
        Route::post('loans/{id}/restore', [LoanController::class, 'restore']);
        Route::delete('loans/{id}/force-delete', [LoanController::class, 'forceDelete']);
        Route::delete('loans/{loan}', [LoanController::class, 'destroy']);

        Route::apiResource('payments', PaymentController::class);

        Route::get('payments-archived', [PaymentController::class, 'archived']);
        Route::post('payments/{id}/restore', [PaymentController::class, 'restore']);
        Route::delete('payments/{id}/force-delete', [PaymentController::class, 'forceDelete']);

        Route::apiResource('rewards', RewardController::class);
        Route::get('rewards-archived', [RewardController::class, 'archived']);
        Route::post('rewards/{id}/restore', [RewardController::class, 'restore']);
        Route::delete('rewards/{id}/force-delete', [RewardController::class, 'forceDelete']);

        Route::post('notifications/send', [NotificationController::class, 'send']);

        Route::apiResource('weekly-histories', WeeklyHistoryController::class)->only(['index', 'store']);
        Route::post('weekly-histories/{weeklyHistory}/toggle-payment', [WeeklyHistoryController::class, 'togglePayment']);

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
            // offline
            Route::post('sync', [AttendanceController::class, 'sync']);
        });
        Route::put('loans/{loan}', [LoanController::class, 'update']);

    });


});
