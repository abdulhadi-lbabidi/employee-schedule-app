<?php

namespace App\Http\Services;

use App\Models\Discount;

class DiscountService
{
  public function getAll()
  {
    return Discount::with('employee', 'admin')
      ->get();
  }

  public function create(array $data)
  {
    return Discount::create($data);
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
