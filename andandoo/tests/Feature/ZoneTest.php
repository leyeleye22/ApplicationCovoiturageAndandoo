<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zones;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ZoneTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testInsererZone(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@andandoo.com',
            'password' => bcrypt('andandoo12'),
        ]);
        $this->actingAs($user, 'api');
        $zone = Zones::factory()->create();
        $zoneinsere = $zone->toArray();
        $this->assertDatabaseHas('Zones', $zoneinsere);
    }

    public function testModifierZone(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@andandoo.com',
            'password' => bcrypt('andandoo12'),
        ]);
        $this->actingAs($user, 'api');

        $zone = [
            'NomZ' => 'Pikine Thies',
            'user_id' => auth()->user()->id
        ];
        $zoneTr = Zones::FindOrFail(2);
        $response = $this->post('api/updatezone/' . $zoneTr->id, $zone);
        $response->assertStatus(200);
    }
    public function testListerZone(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@andandoo.com',
            'password' => bcrypt('andandoo12'),
        ]);
        $this->actingAs($user, 'api');
        $response = $this->get('api/listzone');
        $response->assertStatus(200);
    }
    public function testSupprimerZone(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@andandoo.com',
            'password' => bcrypt('andandoo12'),
        ]);
        $this->actingAs($user, 'api');
        $zoneTr = Zones::FindOrFail(1);
        $response = $this->delete('api/deletezone/'.$zoneTr->id);
        $response->assertStatus(200);
    }
}
