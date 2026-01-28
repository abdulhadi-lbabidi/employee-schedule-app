<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $number = fake()->randomFloat(2,100,1000);
        return [
            'employee_id'=>Employee::all()->random()->id,
            'admin_id'=>Admin::all()->random()->id,
            'amount'=>$number,
            'paid_amount'=>fake()->randomElement([$number/2, $number/3, $number/4, $number]),
            'role'=>fake()->randomElement(['قيد الانتظار ', 'مدفوعة جزئياً', 'مسددة بالكامل']),
            'date'=>Carbon::createFromDate(2026,1),
        ];
    }
}
