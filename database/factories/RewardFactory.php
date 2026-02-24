<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reward>
 */
class RewardFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'employee_id' => Employee::all()->random()->id,
      'admin_id' => Admin::all()->random()->id,
      'amount' => fake()->randomFloat(2, 0.5, 5),
      'reason' => fake()->text(),
      'date_issued' => Carbon::createFromDate(2026, 1),
    ];
  }
}