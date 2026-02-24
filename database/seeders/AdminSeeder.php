<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Admin::factory()->create([
      'name' => 'Abdulrahman Nouh',
    ])->user()->create([
          'full_name' => 'Abdulrahman Nouh',
          'phone_number' => '0946963546',
          'profile_image_url' => null,
          'email' => 'abdulrahmannouh@nouh-agency.com',
          'password' => bcrypt('12345678'),
        ]);
    // Admin::factory()->create([
    //     'name' => 'Ahmed Shahrour',
    // ])->user()->create([
    //     'full_name' => 'Ahmed Shahrour',
    //     'phone_number'=>'0932893379',
    //     'profile_image_url'=>null,s
    //     'email'=>'ahmadshahrour@nouh-agency.com',
    //     'password'=>bcrypt('12345678'),
    // ]);
    // Admin::factory()->create([
    //     'name' => 'Hatem Alsaleh',
    // ])->user()->create([
    //     'full_name' => 'Hatem Alsaleh',
    //     'phone_number'=>'0935936396',
    //     'profile_image_url'=>null,
    //     'email'=>null,
    //     'password'=>bcrypt('12345678'),
    // ]);
    // Admin::factory()->create([
    //     'name' => 'Abdalhadi Lbabidi',
    // ])->user()->create([
    //     'full_name' => 'Abdalhadi Lbabidi',
    //     'phone_number'=>'0957464304',
    //     'profile_image_url'=>null,
    //     'email'=>null,
    //     'password'=>bcrypt('12345678'),
    // ]);
  }
}
