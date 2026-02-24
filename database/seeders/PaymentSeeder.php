<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Payment::factory()->count(30)->create();
    Employee::all()->each(function ($employee) {
      Payment::factory()->create([
        'employee_id' => $employee->id,
      ]);
    });
  }
}