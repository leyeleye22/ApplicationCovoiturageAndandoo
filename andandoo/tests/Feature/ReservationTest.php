<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testInsererReservation(): void
    {
        $user = Utilisateur::factory()->create();
        $this->actingAs($user, 'apiut');
        $reservation = Reservation::factory()->create();
        $reservationinsert = $reservation->toArray();
        $this->assertDatabaseHas('Reservations', $reservationinsert);
    }
    public function testModifierReservation(): void
    {
        $user = Utilisateur::factory()->create();
        $this->actingAs($user, 'apiut');

        $reservation = [
            'NombrePlaces' => 2,
            'voiture_id' => 2,
            'utilisateur_id' => Auth::guard('apiut')->user()->id
        ];
        $reservationtr = Reservation::FindOrFail(1);
        $response = $this->post('api/UpdateReservation/' . $reservationtr->id, $reservation);
        $response->assertStatus(200);
    }
    public function testListerReservation(): void
    {   $user = Utilisateur::factory()->create();
        $this->actingAs($user,'apiut');
        $response = $this->get('api/ListReservation');
        $response->assertStatus(200);
    }
    public function testAnnulerReservation(): void
    {
        $user = Utilisateur::factory()->create();
        $this->actingAs($user,'apiut');
        $reservationtr=Reservation::FindOrFail(1);
        $response = $this->delete('api/DeleteReservation/'.$reservationtr->id);
        $response->assertStatus(200);
    }
}
