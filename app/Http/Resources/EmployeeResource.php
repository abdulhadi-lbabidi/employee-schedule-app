<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'department' => $this->department,
            'hourly_rate' => $this->hourly_rate,
            'overtime_rate' => $this->overtime_rate,
            'is_online' => $this->is_online,
            'current_location' => $this->current_location,
            'user' => [
                'id' => $this->users->id ?? null,
                'full_name' => $this->users->full_name ?? null,
                'phone_number' => $this->users->phone_number ?? null,
                'email' => $this->users->email ?? null,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}