<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trajet>
 */
class TrajetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create();
        return [
            'LieuDepart' => $faker->city,
            'LieuArrivee' => $faker->city,
            'DateDepart' => $faker->dateTimeThisYear->format('Y-m-d'),
            'HeureD' => $faker->time('H:i:s'),
            'Prix' => $faker->randomFloat(2, 10, 100),
            'utilisateur_id' =>1
        ];
    }
}
