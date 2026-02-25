<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $total = (float) $this->total_amount;
    $paid = (float) $this->amount_paid;
    $remaining = round($total - $paid, 2);

    return [
      'id' => $this->id,
      'employee_name' => $this->employee?->user?->full_name,
      'admin_name' => $this->admin?->name,
      'total_amount' => $total,
      'amount_paid' => $paid,
      'remaining_amount' => $remaining,
      'status' => ($remaining <= 0) ? 'Paid' : 'Pending',
      'payment_date' => $this->payment_date ? \Carbon\Carbon::parse($this->payment_date)->format('Y-m-d H:i') : null,
    ];
  }
}
