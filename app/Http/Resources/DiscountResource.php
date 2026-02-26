<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
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
      'amount' => $this->amount,
      'reason' => $this->reason,
      'date_issued' => $this->date_issued,
      'employee_name' => $this->employee?->user?->full_name,
      'admin_name' => $this->admin?->name,
      'workshop_name' => $this->workshop?->name,
      'created_at' => $this->created_at->format('Y-m-d'),
    ];
  }
}
