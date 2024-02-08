<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Zones;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ZoneTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testZoneOperations()
    {
        $admin = $this->createAdmin();
        $zoneData = $this->generateZoneData();
        $createdZone = $this->addZone($admin, $zoneData);
        $this->updateZone($admin, $createdZone);
        $this->listZones();
        $this->deleteZone($createdZone);
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

    protected function generateZoneData()
    {
        return [
            'NomZ' => $this->faker->city,
        ];
    }

    protected function addZone($admin, $zoneData)
    {
        $response = $this->postJson('/api/createzone', $zoneData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('zones', $zoneData);
        return Zones::where('NomZ', $zoneData['NomZ'])->first();
    }

    protected function updateZone($admin, $zone)
    {
        $updatedZoneData = [
            'NomZ' => 'Nouveau nom de zone',
        ];

        $response = $this->postJson("/api/updatezone/{$zone->id}", $updatedZoneData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('zones', ['id' => $zone->id, 'NomZ' => $updatedZoneData['NomZ']]);
    }

    protected function listZones()
    {
        $response = $this->get('/api/listzone');
        $response->assertStatus(200);
    }

    protected function deleteZone($zone)
    {
        $response = $this->delete("/api/deletezone/{$zone->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('zones', ['id' => $zone->id]);
    }
}
