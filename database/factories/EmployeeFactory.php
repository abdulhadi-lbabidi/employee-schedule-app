<?php

namespace Database\Factories;

use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'position' => fake()->randomElement(['employee']),
      'department' => fake()->randomElement(['IT', 'HR', 'Sales']),
      'hourly_rate' => fake()->randomFloat(2, 0.5, 1),
      'overtime_rate' => fake()->randomFloat(2, 1, 1.5),
      'is_online' => fake()->boolean(20),
      'current_location' => Workshop::all()->random()->name,
    ];
  }
}
