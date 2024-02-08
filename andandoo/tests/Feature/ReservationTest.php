<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zones;
use App\Models\Trajet;
use App\Models\Voiture;
use App\Models\Reservation;
use App\Models\Utilisateur;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     */
    public function testTrajetOperations(): void
    {
        $admin = $this->createAdmin();
        $zone = $this->createZone($admin);
        $chauffeur = $this->createChauffeur($zone);
        $voiture = $this->addVoiture($chauffeur);
        $trajet = $this->createTrajet($voiture);
        $client = $this->createClient($zone);
        $reservation = $this->createReservation($trajet, $client);
        $this->updateReservation($reservation, $client);
        $this->listReservation($client);
        $this->deleteReservation($trajet, $client);
        $this->deleteAllReservation($client);
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
    protected function createClient($zone)
    {
        $client = Utilisateur::factory()->create([
            'id' => $this->faker->randomNumber(5),
            'Nom' => $this->faker->lastName,
            'Prenom' => $this->faker->firstName,
            'Email' => $this->faker->unique()->safeEmail,
            'Telephone' => $this->faker->unique()->phoneNumber,
            'role' => 'client',
            'zone_id' => $zone->id,
            'password' => bcrypt('password'),
        ]);

        return $client;
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
        $this->assertDatabaseHas('voitures', ['Descriptions' => 'Ma belle voiture']);

        return Voiture::first();
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
    protected function createReservation($trajet, $client)
    {
        $this->actingAs($client, 'apiut');
        $reservationData = [
            'NombrePlaces' => 2,
            'utilisateur_id' => $client->id,
            'trajet_id' => $trajet->id
        ];
        $response = $this->postJson('/api/CreateReservation', $reservationData);
        $response->assertStatus(200);
        // $this->assertDatabaseHas('reservations', ['Descriptions' => 'Ma belle voiture']);
        return Reservation::first();
    }
    protected function updateReservation($reservation, $client)
    {

        $response = $this->actingAs($client, 'apiut')->postJson("/api/UpdateReservation/{$reservation->id}", [
            'NombrePlaces' => 2,
            'utilisateur_id' => $client->id
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
    }
    protected function listReservation($client)
    {

        $response = $this->actingAs($client, 'apiut')->get('api/ListReservation');

        $response->assertStatus(200);
    }
    protected function deleteReservation($reservation, $client)
    {
        $response = $this->actingAs($client, 'apiut')->delete("/api/DeleteReservation/{$reservation->id}");
        $response->assertStatus(200);
    }
    protected function deleteAllReservation($client)
    {
        $response = $this->actingAs($client, 'apiut')->delete("/api/DeleteReservations");
        $response->assertStatus(200);
    }
}
