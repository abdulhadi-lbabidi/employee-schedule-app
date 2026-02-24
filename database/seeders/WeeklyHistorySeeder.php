<?php

namespace Database\Seeders;

use App\Models\WeeklyHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeeklyHistorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    WeeklyHistory::factory()->count(70)->create();
  }
}