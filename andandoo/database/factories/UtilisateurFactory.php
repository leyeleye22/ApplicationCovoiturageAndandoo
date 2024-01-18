<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Utilisateur::class;
    public function definition(): array
    {
        $faker = app(Faker::class);

        return [
            'Nom' => $faker->name,
            'Prenom' => $faker->name,
            'Email' => $faker->email,
            'Telephone' => $faker->regexify('/^(77|78|70|75)[0-9]{7}$/'),
            'ImageProfile' => $faker->imageUrl(),
            'PermisConduire' => $faker->text(255),
            'role' => $faker->randomElement(['chauffeur', 'chauffeur']),
            'zone_id' => $faker->numberBetween(1,2),
            'password' => $faker->password(8),
        ];
    }
}
