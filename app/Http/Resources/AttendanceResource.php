<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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

            'employee' => [
                'id' => $this->employee?->id,
                'position' => $this->employee?->position,
                'department' => $this->employee?->department,
            ],

            'workshop' => [
                'id' => $this->workshop?->id,
                'name' => $this->workshop?->name,
                'location' => $this->workshop?->location,
            ],

            'date' => $this->date?->format('Y-m-d'),

            'check_in' => $this->check_in?->format('H:i:s'),
            'check_out' => $this->check_out?->format('H:i:s'),


            'week_number' => $this->week_number,
            'regular_hours' => $this->regular_hours,
            'overtime_hours' => $this->overtime_hours,

            'status' => $this->status,
            'note' => $this->note,
        ];
    }
}
