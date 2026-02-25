<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Workshop;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $workshopId = Workshop::first()->id ?? 1;

    $data = [
      ["date" => "2026-02-01", "regular_hours" => 6, "overtime_hours" => 2, "note" => null],
      ["date" => "2026-02-02", "regular_hours" => 1, "overtime_hours" => 0, "note" => "تأخير"],
      ["date" => "2026-02-03", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-04", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-05", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-06", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-07", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-08", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-09", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-10", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-11", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-12", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-13", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-14", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-15", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-16", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-17", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-18", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-19", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-20", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-21", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-22", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-23", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-24", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-25", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-26", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-27", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
      ["date" => "2026-02-28", "regular_hours" => 8, "overtime_hours" => 1, "note" => "تأخير"],
    ];

    Employee::all()->each(function ($employee) use ($data, $workshopId) {
      foreach ($data as $entry) {
        $date = Carbon::parse($entry['date']);
        $estimatedAmount = round(
          ($entry['regular_hours'] * $employee->hourly_rate) +
          ($entry['overtime_hours'] * $employee->overtime_rate),
          2
        );

        Attendance::create([
          'employee_id' => $employee->id,
          'workshop_id' => $workshopId,
          'date' => $entry['date'],
          'week_number' => $date->weekOfYear,
          'check_in' => '08:00:00',
          'check_out' => '16:00:00',
          'regular_hours' => $entry['regular_hours'],
          'overtime_hours' => $entry['overtime_hours'],
          'estimated_amount' => $estimatedAmount,
          'paid_amount' => 0,
          'note' => $entry['note'],
          'status' => 'قيد الرفع',
        ]);
      }
    });
  }
}
