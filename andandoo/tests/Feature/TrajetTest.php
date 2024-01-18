<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Trajet;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrajetTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testInsererTrajet(): void
    {
        $trajet = Trajet::factory()->create();
        $trajetinsert = $trajet->toArray();
        $this->assertDatabaseHas('Trajets', $trajetinsert);
    }
    public function testModifierTrajet(): void
    {
        $user = Utilisateur::factory()->create();
        $this->actingAs($user,'apiut');
        
        $trajet = [
            'LieuDepart' => 'Nouvelle ville de départ',
            'LieuArrivee' => 'Nouvelle ville d\'arrivée',
            'DateDepart' => '2024-01-21',
            'HeureD' => '14:30:00',
            'Prix' => 60.00,
            'utilisateur_id' => 1
        ];
        $trajetTr=Trajet::FindOrFail(1);
        $response = $this->post('api/UpdateTrajet/'.$trajetTr->id,$trajet);
        $response->assertStatus(200);
    }
    public function testListerTrajet(): void
    {   $user = Utilisateur::factory()->create();
        $this->actingAs($user,'apiut');
        $response = $this->get('api/ListTrajet');
        $response->assertStatus(200);
    }
    public function testSupprimerTrajet(): void
    {
        $user = Utilisateur::factory()->create();
        $this->actingAs($user,'apiut');
        $trajetTr=Trajet::FindOrFail(1);
        $response = $this->delete('api/DeleteTrajet/'.$trajetTr->id);
        $response->assertStatus(200);
    }
}
