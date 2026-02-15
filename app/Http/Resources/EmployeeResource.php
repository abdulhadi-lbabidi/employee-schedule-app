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
            'user' => new UserResource($this->user),
        ];
    }
}
