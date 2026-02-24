<?php

namespace Database\Seeders;

use App\Models\User;
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
    ]);
    $this->call([
      WorkshopSeeder::class,
    ]);
    $this->call([
      EmployeeSeeder::class,
    ]);
    $this->call([
      AttendanceSeeder::class,
    ]);
    $this->call([
      LoanSeeder::class,
    ]);
    $this->call([
      RewardSeeder::class,
    ]);
    $this->call([
      PaymentSeeder::class,
    ]);


  }
}