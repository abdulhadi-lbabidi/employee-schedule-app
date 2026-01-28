<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeeklyHistory>
 */
class WeeklyHistoryFactory extends Factory
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
            'week_number'=>fake()->numberBetween(1,5),
            'month'=>fake()->numberBetween(1,12),
            'year'=>fake()->numberBetween(2025,2026),
            'workshops'=>Workshop::all()->random()->name,
            'amount_paid'=>fake()->randomFloat(2,100,300),
            'is_paid'=>fake()->boolean(20),
        ];
    }
}
