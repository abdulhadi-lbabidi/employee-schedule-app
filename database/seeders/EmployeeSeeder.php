<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::factory()->create([
            'position' => 'daily worker',
            'department' => 'Executer & Watcher',
            'hourly_rate' => 10,
            'overtime_rate' => 1,
            'is_online' => 0,
            'current_location' => 'Office',
        ])->user()->create([
                    'full_name' => 'Ahmad Aini',
                    'phone_number' => '0993227885',
                    'profile_image_url' => null,
                    'email' => 'ahmadaini@nouh-agency.com',
                    'password' => bcrypt('12345678'),
                ]);
        Employee::factory()->create([
            'position' => 'daily worker',
            'department' => 'Executer & Watcher',
            'hourly_rate' => 7,
            'overtime_rate' => 1,
            'is_online' => 0,
            'current_location' => 'W115',
        ])->user()->create([
                    'full_name' => 'Ahmad Mousa',
                    'phone_number' => '0954282944',
                    'profile_image_url' => null,
                    'email' => null,
                    'password' => bcrypt('12345678'),
                ]);
        Employee::factory()->create([
            'position' => 'daily worker',
            'department' => 'Executer & Watcher',
            'hourly_rate' => 7,
            'overtime_rate' => 1,
            'is_online' => false,
            'current_location' => 'W118',
        ])->user()->create([
                    'full_name' => 'Abdulkarim Ibrahim',
                    'phone_number' => '0981287087',
                    'profile_image_url' => null,
                    'email' => null,
                    'password' => bcrypt('12345678'),
                ]);
        Employee::factory()->create([
            'position' => 'daily worker',
            'department' => 'Executer & Watcher',
            'hourly_rate' => 7,
            'overtime_rate' => 1,
            'is_online' => false,
            'current_location' => 'W115',
        ])->user()->create([
                    'full_name' => 'Mahmoud Ibrahim',
                    'phone_number' => '0939405822',
                    'profile_image_url' => null,
                    'email' => null,
                    'password' => bcrypt('12345678'),
                ]);
        Employee::factory()->create([
            'position' => 'daily worker',
            'department' => 'Executer & Watcher',
            'hourly_rate' => 7,
            'overtime_rate' => 1,
            'is_online' => false,
            'current_location' => 'W112',
        ])->user()->create([
                    'full_name' => 'Anas Karman',
                    'phone_number' => '0968004871',
                    'profile_image_url' => null,
                    'email' => null,
                    'password' => bcrypt('12345678'),
                ]);
        Employee::factory()->create([
            'position' => 'daily worker',
            'department' => 'Executer & Watcher',
            'hourly_rate' => 10,
            'overtime_rate' => 1,
            'is_online' => false,
            'current_location' => 'W120',
        ])->user()->create([
                    'full_name' => 'fake user',
                    'phone_number' => '0999999999',
                    'profile_image_url' => null,
                    'email' => null,
                    'password' => bcrypt('12345678'),
                ]);
    }
}