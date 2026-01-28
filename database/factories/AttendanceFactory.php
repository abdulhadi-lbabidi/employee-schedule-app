<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use App\Models\Workshop;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nette\Utils\Random;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'employee_id'=>Employee::all()->random()->id,
        'workshop_id'=>Workshop::all()->random()->id,
        'date'=>Carbon::today(),
        'check_in'=>Carbon::createFromDate(2026,1),
        'check_out'=>Carbon::createFromDate(2026,1),
        'week_number'=>fake()->numberBetween(1,5),
        'note'=>fake()->text(),
        'regular_hours'=>fake()->randomFloat(2,0.5,1),
        'overtime_hours'=>fake()->randomFloat(2,1,1.5),
        ];
    }
}
