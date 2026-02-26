<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Discount\CreateDiscountRequest;
use App\Http\Requests\Discount\UpdateDiscountRequest;
use App\Http\Resources\DiscountResource;
use App\Http\Services\DiscountService;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
  public function __construct(
    private DiscountService $discountService
  ) {
  }

  public function index()
  {
    $discount = $this->discountService->getAll();
    return DiscountResource::collection($discount);
  }


  public function store(CreateDiscountRequest $request)
  {
    $newDiscount = $this->discountService->create($request->validated());
    return new DiscountResource($newDiscount);
  }



  public function update(UpdateDiscountRequest $request, Discount $discount)
  {
    $discount = $this->discountService->update($discount, $request->validated());
    return new DiscountResource($discount);
  }

  public function destroy(Discount $discount)
  {
    $this->discountService->delete($discount);
    return response()->json([
      'message' => 'Discount deleted successfully'
    ]);
  }

  public function getEmployeeDiscounts($employee_id)
  {
    $discounts = $this->discountService->getByEmployeeId($employee_id);

    if ($discounts->isEmpty()) {
      return response()->json(['message' => 'No Discount found for this employee'], 404);
    }

    return DiscountResource::collection($discounts);
  }
}
