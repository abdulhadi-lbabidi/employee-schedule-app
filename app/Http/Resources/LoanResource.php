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
            'amount'=> $this->amount,
            'paid_amount'=> $this->paid_amount,
            'role'=> $this->role,
            'date'=> $this->date,
            'employee' => [
                'id' => $this->employee->id ?? null,
                'full_name' => $this->employee->user->full_name ?? null,
                'phone_number' => $this->employee->userphone_number ?? null,
                'email' => $this->users->email ?? null,
            ],
        ];
    }
}
