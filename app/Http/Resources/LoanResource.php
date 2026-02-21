<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
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
      'paid_amount' => (double) $this->paid_amount,
      'status' => $this->status,
      'date' => $this->date->format('d-m-Y'),
      'employee' => [
        'id' => $this->employee->id ?? null,
        'full_name' => $this->employee->user->full_name ?? null,
        'phone_number' => $this->employee->user->phone_number ?? null,
        'email' => $this->user->email ?? null,
      ],
    ];
  }
}
