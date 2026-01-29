<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeeklyHistoryResource extends JsonResource
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
            'week_info' => [
                'number' => $this->week_number,
                'month' => $this->month,
                'year' => $this->year,
            ],
            'amount' => $this->amount_paid,
            'status' => $this->is_paid ? 'تم الدفع' : 'معلق',
            'employee' => $this->employee?->user?->full_name,
            'workshop' => $this->workshop?->name,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}