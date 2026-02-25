<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $this->call([
      AdminSeeder::class,
      WorkshopSeeder::class,
      EmployeeSeeder::class,
      AttendanceSeeder::class,
        // PaymentSeeder::class,
      LoanSeeder::class,
      RewardSeeder::class,
      PaymentSeeder::class,

    ]);

  }
}
