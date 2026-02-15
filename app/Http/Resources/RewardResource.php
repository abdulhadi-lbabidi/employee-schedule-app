<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardResource extends JsonResource
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
      'employee' => new EmployeeResource($this->whenLoaded('employee')),
      'admin_name' => $this->admin?->name,
      'created_at' => $this->created_at->format('Y-m-d'),
    ];
  }
}