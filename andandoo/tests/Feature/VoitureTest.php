<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Voiture;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VoitureTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test complet des opérations sur les voitures.
     */
    public function testVoitureOperations(): void
    {
        // Création d'un administrateur
        $user = User::factory()->create([
            'email' => 'admin@andandoo.com',
            'password' => bcrypt('andandoo12'),
        ]);
    
        $this->actingAs($user);
    

        $zoneData = [
            'NomZ' => $this->faker->city,
            'user_id' => $user->id
        ];
        $response = $this->postJson('/api/createzone', $zoneData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('zones', $zoneData);

    
        // Enregistrement d'un chauffeur avec tous les attributs requis
        $chauffeurData = [
            'Nom' => $this->faker->lastName,
            'Prenom' => $this->faker->firstName,
            'Email' => 'chauffeur@example.com',
            'Telephone' => $this->faker->unique()->phoneNumber,
            'role' => 'chauffeur',
            'zone_id' => $zoneData['id'], // Utiliser $zone pour obtenir l'id de la zone
            'ImageProfile' => UploadedFile::fake()->image('image.jpg'),
            'PermisConduire' => UploadedFile::fake()->image('permis.jpg'),
            'CarteGrise' => UploadedFile::fake()->image('carte_grise.jpg'),
            'Licence' => UploadedFile::fake()->image('licence.jpg'),
            'password' => 'password',
        ];
    
        $response = $this->postJson('/api/register', $chauffeurData);
        $response->assertStatus(201);
    
        // Connexion du chauffeur
        $response = $this->postJson('/api/login', [
            'email' => 'chauffeur@example.com',
            'password' => 'password',
        ]);
        $response->assertStatus(200);
        $token = json_decode($response->getContent(), true)['access_token'];
    
        // Création d'une voiture associée au chauffeur connecté
        $voitureData = [
            'ImageVoitures' => UploadedFile::fake()->image('voiture.jpg'),
            'Descriptions' => 'Ma belle voiture',
            'NbrPlaces' => 4,
            'utilisateur_id' => auth()->user()->id,
        ];
    
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/AjouterVoiture', $voitureData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('voitures', $voitureData);
    
        // Modifier la voiture
        $voiture = Voiture::first();
        $updatedVoitureData = [
            'ImageVoitures' => UploadedFile::fake()->image('updated_voiture.jpg'),
            'Descriptions' => 'Ma voiture mise à jour',
            'NbrPlaces' => 5,
            'utilisateur_id' => auth()->user()->id,
        ];
    
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson("/api/ModifierVoiture/{$voiture->id}", $updatedVoitureData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('voitures', $updatedVoitureData);
    }
    
}
