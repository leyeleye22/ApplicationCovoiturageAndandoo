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
        $this->actingAs($chauffeur, 'apiut');
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
        $response = $this->actingAs($voiture->utilisateur, 'apiut')->postJson('/api/CreateTrajet', [
            'LieuDepart' => $this->faker->city,
            'LieuArrivee' => $this->faker->city,
            'DateDepart' => "2024-02-25",
            'HeureD' => $this->faker->time('H:i'),
            'Prix' => 300,
            'voiture_id' => $voiture->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trajets', ['voiture_id' => $voiture->id]);

        return Trajet::first();
    }

    protected function updateTrajet($trajet, $chauffeur)
    {
        $response = $this->actingAs($chauffeur, 'apiut')->postJson("/api/UpdateTrajet/{$trajet->id}", [
            'LieuDepart' => $this->faker->city,
            'LieuArrivee' => $this->faker->city,
            'DateDepart' => "2024-02-26",
            'HeureD' => $this->faker->time('H:i'),
            'Prix' => 350,
            'voiture_id' => $trajet->voiture_id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trajets', ['id' => $trajet->id]);
    }

    protected function listTrajet($chauffeur)
    {
        $response = $this->actingAs($chauffeur, 'apiut')->get('api/mestrajets');

        $response->assertStatus(200);
    }

    protected function deleteTrajet($trajet, $chauffeur)
    {
        $response = $this->actingAs($chauffeur, 'apiut')->delete("api/DeleteTrajet/{$trajet->id}");
        $response->assertStatus(403);
        // $this->assertDatabaseMissing('trajets', ['id' => $trajet->id]);
    }
}
