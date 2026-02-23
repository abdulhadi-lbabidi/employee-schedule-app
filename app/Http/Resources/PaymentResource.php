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
    return [
      'id' => $this->id,
      'employee_name' => $this->employee?->user?->full_name,
      'admin_name' => $this->admin?->name,
      'total_amount' => $this->total_amount,
      'amount_paid' => $this->amount_paid,
      'remaining_amount' => round($this->total_amount - $this->amount_paid, 2),

      'status' => $this->is_paid ? 'Paid' : 'Pending',
      'payment_date' => $this->payment_date?->format('Y-m-d H:i'),
    ];
  }
}