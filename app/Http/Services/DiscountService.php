<?php

namespace App\Http\Services;

use App\Models\Discount;
use App\Models\Employee;
use App\Models\User;

class DiscountService
{

  public function __construct(private NotificationService $notificationService)
  {
  }

  public function getAll()
  {
    return Discount::with('employee', 'admin')
      ->get();
  }



  public function create(array $data)
  {
    $discount = Discount::create($data);

    $employee = Employee::find($data['employee_id']);

    $user = User::where('userable_id', $employee->id)
      ->where('userable_type', Employee::class)
      ->first();

    if ($user) {
      $this->notificationService->sendToUser(
        $user,
        'تنبيه: تسجيل خصم 📉',
        "تم تسجيل خصم جديد بمبلغ {$discount->amount}. السبب: {$discount->reason}",
        [
          'type' => 'discount_created',
          'discount_id' => (string) $discount->id,
          'route' => '/discounts'
        ]
      );
    }

    return $discount;
  }

  public function update(Discount $Discount, array $data)
  {
    $Discount->update($data);
    return $Discount;
  }

  public function delete(Discount $discount)
  {
    return $discount->delete();
  }

  public function getByEmployeeId($employeeId)
  {
    return Discount::with('employee', 'admin')
      ->where('employee_id', $employeeId)
      ->get();
  }
}
