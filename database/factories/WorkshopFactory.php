<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workshop>
 */
class WorkshopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>fake()->company(),
            'location'=>fake()->city(),
            'description'=>fake()->text(),
            'latitude'=>fake()->latitude(),
            'longitude'=>fake()->longitude(),
            'radiusInMeters'=>fake()->randomFloat(2,10,100),
        ];
    }
}
