<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zones;
use App\Models\Trajet;
use App\Models\Voiture;
use App\Models\Utilisateur;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrajetTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test complet des opérations sur les trajets.
     */
    public function testTrajetOperations(): void
    {
        $admin = $this->createAdmin();
        $zone = $this->createZone($admin);
        $chauffeur = $this->createChauffeur($zone);
        $voiture = $this->addVoiture($chauffeur);
        $trajet = $this->createTrajet($voiture);
        $this->updateTrajet($trajet, $chauffeur);
        $this->listTrajet($chauffeur);
        $this->deleteTrajet($trajet, $chauffeur);
    }

    protected function createAdmin()
    {
        $admin = User::factory()->create([
            'email' => 'admin@andandoo.com',
            'password' => bcrypt('andandoo12'),
        ]);

        $this->actingAs($admin);

        return $admin;
    }

    protected function createZone($admin)
    {
        $zoneData = [
            'NomZ' => $this->faker->city,
            'user_id' => $admin->id
        ];

        $response = $this->postJson('/api/createzone', $zoneData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('zones', $zoneData);

        return Zones::where('NomZ', $zoneData['NomZ'])->first();
    }

    protected function createChauffeur($zone)
    {
        $chauffeur = Utilisateur::factory()->create([
            'id' => $this->faker->randomNumber(5),
            'Nom' => $this->faker->lastName,
            'Prenom' => $this->faker->firstName,
            'Email' => $this->faker->unique()->safeEmail,
            'Telephone' => $this->faker->unique()->phoneNumber,
            'role' => 'chauffeur',
            'zone_id' => $zone->id,
            'password' => bcrypt('password'),
        ]);

        return $chauffeur;
    }

    protected function addVoiture($chauffeur)
    {
        $voitureData = [
            'ImageVoitures' => UploadedFile::fake()->image('voiture.jpg'),
            'Descriptions' => 'Ma belle voiture',
            'NbrPlaces' => 4,
            'utilisateur_id' => $chauffeur->id,
        ];

        $response = $this->postJson('/api/AjouterVoiture', $voitureData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('voitures', ['Descriptions' => 'Ma belle voiture']); // Vérifie si la voiture a été ajoutée à la base de données avec les données fournies

        return Voiture::first(); // Retourne la première voiture ajoutée à la base de données
    }

    protected function createTrajet($voiture)
    {
        // Utiliser le chauffeur associé à la voiture pour créer le trajet
        $response = $this->actingAs($voiture->utilisateur, 'apiut')->postJson('/api/CreateTrajet', [
            'LieuDepart' => $this->faker->city,
            'LieuArrivee' => $this->faker->city,
            'DateDepart' => $this->faker->date('Y-m-d'),
            'HeureD' => $this->faker->time('H:i:s'),
            'Prix' => $this->faker->randomFloat(2, 10, 100),
            'voiture_id' => $voiture->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trajets', ['voiture_id' => $voiture->id]);

        return Trajet::first();
    }

    protected function updateTrajet($trajet, $chauffeur)
    {
        // Logique pour mettre à jour un trajet
    }

    protected function listTrajet($chauffeur)
    {
        // Logique pour lister les trajets d'un chauffeur
    }

    protected function deleteTrajet($trajet, $chauffeur)
    {
        // Logique pour supprimer un trajet
    }
}
