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
    $totalAmount = fake()->randomFloat(2, 100, 1000);

    $amountPaid = fake()->randomFloat(2, 50, $totalAmount);

    $isPaid = $amountPaid >= $totalAmount;

    return [
      'employee_id' => Employee::inRandomOrder()->first()->id ?? Employee::factory(),
      'admin_id' => Admin::inRandomOrder()->first()->id ?? Admin::factory(),
      'total_amount' => $totalAmount,
      'amount_paid' => $amountPaid,
      'is_paid' => $isPaid,
      'payment_date' => Carbon::now()->subDays(rand(1, 30)),
    ];
  }
}