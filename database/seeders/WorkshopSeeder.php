<?php

namespace Database\Seeders;

use App\Models\Workshop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkshopSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Workshop::factory()->create([
      'name' => 'W75',
      'location' => 'حلب الجديدة شمالي خلف مدرسة لانا زينو',
      'description' => 'شقة منزلية',
      'latitude' => 18.25252525,
      'longitude' => 17.25252525,
      'radiusInMeters' => 100,
    ]);
    Workshop::factory()->create([
      'name' => 'W64',
      'location' => 'حلب الجديدة جنوبي قرب جامع الرحمة',
      'description' => 'شقة منزلية',
      'latitude' => 18.25252525,
      'longitude' => 17.25252525,
      'radiusInMeters' => 100,
    ]);
    // Workshop::factory()->create([
    //     'name' => 'W120',
    //     'location'=> 'حلب الجديدة جنوبي بالقرب من دوار الموت',
    //     'description'=> 'متجر قهوة كوكنز',
    //     'latitude'=> 18.25252525,
    //     'longitude'=> 17.25252525,
    //     'radiusInMeters'=> 100,
    // ]);
    // Workshop::factory()->create([
    //     'name' => 'W112',
    //     'location'=> 'الجميلية مقابل مشفى فريشو',
    //     'description'=> 'متجر قهوة وماكينات بيع الإكسبريس كوفي زون',
    //     'latitude'=> 18.25252525,
    //     'longitude'=> 17.25252525,
    //     'radiusInMeters'=> 100,
    // ]);
    // Workshop::factory()->create([
    //     'name' => 'ًW115',
    //     'location'=> 'الشهباء الجديدة',
    //     'description'=> 'شقة سكنية',
    //     'latitude'=> 18.25252525,
    //     'longitude'=> 17.25252525,
    //     'radiusInMeters'=> 100,
    // ]);
    // Workshop::factory()->create([
    //     'name' => 'W114',
    //     'location'=> 'الليرمون بالقرب من دوار الليرمون على بعد 400 متر',
    //     'description'=> 'مباني للعب التينيس والرياضات بالإضافة الى مقاهي ومتاجر رياضية',
    //     'latitude'=> 18.25252525,
    //     'longitude'=> 17.25252525,
    //     'radiusInMeters'=> 100,
    // ]);
  }
}
