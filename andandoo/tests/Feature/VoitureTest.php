<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VoitureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testInsererVoiture(): void
    {
        $user = User::factory()->create([
            'email' => 'leye@gmail.com',
            'password' => bcrypt('leye22@22'),
        ]);
        $this->actingAs($user, 'api');
        $zone = Zones::factory()->create();
        $zoneinsere = $zone->toArray();
        $this->assertDatabaseHas('Zones', $zoneinsere);
    }

    // public function testModifierVoiture(): void
    // {
    //     $user = User::factory()->create([
    //         'email' => 'leyeadmin@gmail.com',
    //         'password' => bcrypt('leye22@22'),
    //     ]);
    //     $this->actingAs($user, 'api');

    //     $zone = [
    //         'NomZ' => 'Pikine Thies',
    //         'user_id' => auth()->user()->id
    //     ];
    //     $zoneTr = Zones::FindOrFail(1);
    //     $response = $this->post('api/updatezone/' . $zoneTr->id, $zone);
    //     $response->assertStatus(200);
    // }
    // public function testListerVoiture(): void
    // {
    //     $user = User::factory()->create([
    //         'email' => 'leyeadmin2@gmail.com',
    //         'password' => bcrypt('leye22@22'),
    //     ]);
    //     $this->actingAs($user, 'api');
    //     $response = $this->get('api/listzone');
    //     $response->assertStatus(200);
    // }
    // public function testSupprimerVoiture(): void
    // {
    //     $user = User::factory()->create([
    //         'email' => 'leyeadmin23@gmail.com',
    //         'password' => bcrypt('leye22@22'),
    //     ]);
    //     $this->actingAs($user, 'api');
    //     $zoneTr = Zones::FindOrFail(1);
    //     $response = $this->delete('api/deletezone/'.$zoneTr->id);
    //     $response->assertStatus(200);
    // }
}
