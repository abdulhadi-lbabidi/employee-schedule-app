<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Employee::all()->each(function ($employee) {
      $attendances = Attendance::factory(rand(5, 10))->create([
        'employee_id' => $employee->id,
      ]);


      $attendances->each(function ($attendance) use ($employee) {
        $attendance->update([
          'estimated_amount' => round(
            ($attendance->regular_hours * $employee->hourly_rate) +
            ($attendance->overtime_hours * $employee->overtime_rate),
            2
          )
        ]);
      });
    });


  }
}
