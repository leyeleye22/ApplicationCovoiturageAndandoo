<?php

namespace Database\Factories;

use App\Models\Voiture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
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
            'NombrePlaces' => $faker->numberBetween(1, 10),
            'voiture_id' => Voiture::factory()->create()->id,
            'utilisateur_id' => Auth::guard('apiut')->user()->id
        ];
    }
    
}
