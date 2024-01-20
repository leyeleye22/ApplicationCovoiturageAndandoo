<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voiture>
 */
class VoitureFactory extends Factory
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
            'ImageVoitures' => $faker->imageUrl(640, 480),
            'Descriptions' => $faker->sentence,
            'NbrPlaces' => $faker->numberBetween(1, 10),
            'utilisateur_id' => Auth::guard('apiut')->user()->id
        ];
    }
}
