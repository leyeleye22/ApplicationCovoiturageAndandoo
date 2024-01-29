<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Voiture;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoitureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testInsererVoiture(): void
    {
        $user = Utilisateur::factory()->create();
        $this->actingAs($user, 'apiut');
        $voiture = Voiture::factory()->create();
        $voitureinsere = $voiture->toArray();
        $this->assertDatabaseHas('Voitures', $voitureinsere);
    }

    public function testModifierVoiture(): void
    {
        $user = Utilisateur::factory()->create();
        $this->actingAs($user, 'apiut');

        $voiture = [
            'ImageVoitures' => 'image faker',
            'Descriptions' => 'ma belle voiture',
            'NbrPlaces' => 4,
            'utilisateur_id' => Auth::guard('apiut')->user()->id
        ];
        $voituretr = Voiture::FindOrFail(3);
        $response = $this->post('api/ModifierVoiture/' . $voituretr->id, $voiture);
        $response->assertStatus(200);
    }
    public function testListerVoiture(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'apiut');
        $response = $this->get('api/listerVoitures');
        $response->assertStatus(200);
    }
    public function testSupprimerVoiture(): void
    {
        $user = Utilisateur::factory()->create();
        $this->actingAs($user, 'apiut');
        $voituretr = Voiture::FindOrFail(3);
        $response = $this->delete('api/deletezone/'.$voituretr->id);
        $response->assertStatus(200);
    }
}
