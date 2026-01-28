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
        return [
            'employee_id'=>Employee::all()->random()->id,
            'admin_id'=>Admin::all()->random()->id,
            'week_number'=>fake()->numberBetween(1,5),
            'total_amount'=>fake()->randomFloat(2,0.5,1),
            'amount_paid'=>fake()->randomFloat(2,0.5,1),
            'is_paid'=>fake()->boolean(20),
            'payment_date'=>Carbon::createFromDate(2026,1),
        ];
    }
}
