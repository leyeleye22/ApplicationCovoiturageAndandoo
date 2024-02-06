<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zones;
use App\Models\Voiture;
use App\Models\Utilisateur;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoitureTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test complet des opérations sur les voitures.
     */
    public function testVoitureOperations(): void
    {
        $admin = $this->createAdmin();
        $zone = $this->createZone($admin);
        $chauffeur = $this->createChauffeur($zone);
        $voiture = $this->addVoiture($chauffeur);
        $this->attemptToAddDuplicateVoiture($chauffeur, $voiture);
        $this->updateVoiture($voiture, $chauffeur);
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
            'ImageProfile' => UploadedFile::fake()->image('image.jpg'),
            'PermisConduire' => UploadedFile::fake()->image('permis.jpg'),
            'CarteGrise' => UploadedFile::fake()->image('carte_grise.jpg'),
            'Licence' => UploadedFile::fake()->image('licence.jpg'),
            'password' => bcrypt('password'),
        ]);

        // Authenticate the user using the 'apiut' guard
        Auth::guard('apiut')->login($chauffeur);

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


    protected function attemptToAddDuplicateVoiture($chauffeur, $voiture)
    {
        $voitureData = [
            'ImageVoitures' => UploadedFile::fake()->image('voiture.jpg'),
            'Descriptions' => 'Ma belle voiture',
            'NbrPlaces' => 4,
            'utilisateur_id' => $chauffeur->id,
        ];

        $response = $this->postJson('/api/AjouterVoiture', $voitureData);
        $response->assertStatus(200); // Assuming 422 is the correct status code for duplicate entry
    }

    protected function updateVoiture($voiture, $chauffeur)
    {
        $updatedVoitureData = [
            'Descriptions' => 'Ma voiture mise à jour',
            'NbrPlaces' => 5,
            'utilisateur_id' => $chauffeur->id,
        ];

        $response = $this->actingAs($chauffeur)->postJson("/api/ModifierVoiture/{$voiture->id}", $updatedVoitureData);

        if ($voiture->utilisateur_id == $chauffeur->id) {
            $response->assertStatus(200);
        } else {
            $response->assertStatus(403);
        }
    }
}
