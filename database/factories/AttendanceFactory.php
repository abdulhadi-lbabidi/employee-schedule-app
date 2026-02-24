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

    $regularHours = fake()->randomFloat(2, 4, 8);
    $overtimeHours = fake()->randomFloat(2, 0, 4);


    return [
      'workshop_id' => Workshop::all()->random()->id,
      'date' => Carbon::now()->subDays(rand(0, 30)),
      'check_in' => Carbon::now()->setTime(8, 0),
      'check_out' => Carbon::now()->setTime(17, 0),
      'week_number' => fake()->numberBetween(1, 52),
      'note' => fake()->sentence(),
      'regular_hours' => $regularHours,
      'overtime_hours' => $overtimeHours,
      'estimated_amount' => 0,
      'paid_amount' => 0,
      'status' => 'قيد الرفع',
    ];
  }
}
