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
            'employee_name' => $this->employee?->full_name,
            'admin_name' => $this->admin?->name,
            'week_number' => $this->week_number,
            'total_amount' => $this->total_amount,
            'amount_paid' => $this->amount_paid,
            'remaining_amount' => $this->total_amount - $this->amount_paid,
            'status' => $this->is_paid ? 'Paid' : 'Pending',
            'payment_date' => $this->payment_date?->format('Y-m-d H:i'),

        ];
    }
}
