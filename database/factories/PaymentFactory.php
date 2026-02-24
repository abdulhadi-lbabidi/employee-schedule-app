<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $employee = Employee::inRandomOrder()->first() ?? Employee::factory();

    $regHours = $employee->attendances()->sum('regular_hours');
    $overHours = $employee->attendances()->sum('overtime_hours');

    $totalEarned =
      ($regHours * $employee->hourly_rate) +
      ($overHours * $employee->overtime_rate);

    $totalEarned = max($totalEarned, 0);

    $amountPaid = fake()->randomFloat(2, 0, $totalEarned);

    return [
      'employee_id' => $employee->id,
      'admin_id' => Admin::inRandomOrder()->first()->id ?? Admin::factory(),
      'total_amount' => $totalEarned,
      'amount_paid' => $amountPaid,
      'is_paid' => $amountPaid >= $totalEarned,
      'payment_date' => now()->subDays(rand(1, 30)),
    ];
  }
}
