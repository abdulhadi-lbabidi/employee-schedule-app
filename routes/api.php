<?php

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\WorkshopController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RewardController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\LoanController;
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

  Route::get('workshops', [WorkshopController::class, 'index']);
  Route::get('workshops/{workshop}', [WorkshopController::class, 'show']);

  Route::get('my-attendance/{employeeId}', [AttendanceController::class, 'employeeHistory']);
  Route::get('employees/{employee}/hours-by-workshop', [AttendanceController::class, 'hoursByWorkshop']);
  Route::get('employees/{employee}/hours-and-pay-summary', [AttendanceController::class, 'employeeHoursAndPaySummary']);
  Route::get('/attendance', [AttendanceController::class, 'index']);

  Route::get('loans', [LoanController::class, 'index']);
  Route::get('loans/{loan}', [LoanController::class, 'show']);
  Route::post('loans', [LoanController::class, 'store']);

  Route::get('rewards/employee/{reward}', [RewardController::class, 'getEmployeeRewards']);
  Route::get('discounts/employee/{discount}', [DiscountController::class, 'getEmployeeDiscounts']);

  Route::get('notifications', [NotificationController::class, 'getUnreadNotifications']);
  Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
  Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);

  Route::post('/notifications/user-check-in', [NotificationController::class, 'userCheckIn']);
  Route::post('/notifications/user-check-out', [NotificationController::class, 'userCheckOut']);

  Route::post('/logout', [AuthController::class, 'logout']);

  /*
  |----------------------------------------------------------------------
  |   (Admin Panel)
  |----------------------------------------------------------------------
  */

  Route::middleware('role:admin')->group(function () {

    Route::get('/dashboard/statistics', [DashboardController::class, 'index']);

    Route::get('admins-archived', [AdminController::class, 'archived']);
    Route::post('admins/{id}/restore', [AdminController::class, 'restore']);
    Route::delete('admins/{id}/force-delete', [AdminController::class, 'forceDelete']);

    // employee
    Route::get('employees/dues-report', [EmployeeController::class, 'duesReport']);
    Route::apiResource('employees', EmployeeController::class);
    Route::get('employees-archived', [EmployeeController::class, 'archived']);

    Route::post('employees/{id}/restore', [EmployeeController::class, 'restore']);
    Route::delete('employees/{id}/force-delete', [EmployeeController::class, 'forceDelete']);



    // workshop
    Route::get('workshops/{workshop}/hours-by-employee', [AttendanceController::class, 'workshopHoursByEmployee']);
    Route::post('workshops', [WorkshopController::class, 'store']);
    Route::put('workshops/{workshop}', [WorkshopController::class, 'update']);
    Route::delete('workshops/{workshop}', [WorkshopController::class, 'destroy']);
    Route::get('workshops-archived', [WorkshopController::class, 'archived']);
    Route::post('workshops/{id}/restore', [WorkshopController::class, 'restore']);
    Route::delete('workshops/{id}/force-delete', [WorkshopController::class, 'forceDelete']);

    Route::get('attendances', [AttendanceController::class, 'index']);

    //loan
    Route::get('loans-archived', [LoanController::class, 'archived']);
    Route::post('loans/{id}/restore', [LoanController::class, 'restore']);
    Route::delete('loans/{id}/force-delete', [LoanController::class, 'forceDelete']);
    Route::delete('loans/{loan}', [LoanController::class, 'destroy']);
    Route::post('loans/{loan}/approve', [LoanController::class, 'approve']);
    Route::post('loans/{loan}/reject', [LoanController::class, 'reject']);
    Route::post('loans/{loan}/pay', [LoanController::class, 'pay']);

    // payment
    Route::get('payments/unpaid-weeks/{employee}', [PaymentController::class, 'getUnpaidWeeks']);
    Route::post('payments/pay-records', [PaymentController::class, 'payRecords']);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::get('payments-archived', [PaymentController::class, 'archived']);
    Route::post('payments/{id}/restore', [PaymentController::class, 'restore']);
    Route::delete('payments/{id}/force-delete', [PaymentController::class, 'forceDelete']);

    // reward
    Route::get('rewards', [RewardController::class, 'index']);
    Route::post('rewards', [RewardController::class, 'store']);
    Route::put('rewards/{reward}', [RewardController::class, 'update']);
    Route::delete('rewards/{reward}', [RewardController::class, 'destroy']);
    Route::get('rewards-archived', [RewardController::class, 'archived']);
    Route::post('rewards/{id}/restore', [RewardController::class, 'restore']);
    Route::delete('rewards/{id}/force-delete', [RewardController::class, 'forceDelete']);


    // discount
    Route::get('discounts', [DiscountController::class, 'index']);
    Route::post('discounts', [DiscountController::class, 'store']);
    Route::put('discounts/{reward}', [DiscountController::class, 'update']);
    Route::delete('discounts/{reward}', [DiscountController::class, 'destroy']);

    Route::post('notifications/send', [NotificationController::class, 'send']);



  });

  /*
  |----------------------------------------------------------------------
  |   (Employee Panel)
  |----------------------------------------------------------------------
  */
  Route::middleware('role:employee')->group(function () {
    Route::get('rewards/employee/{employee_id}', [RewardController::class, 'getEmployeeRewards']);
    Route::prefix('attendances')->group(function () {
      // offline
      Route::post('sync', [AttendanceController::class, 'sync']);
    });
    Route::put('loans/{loan}', [LoanController::class, 'update']);

  });


});