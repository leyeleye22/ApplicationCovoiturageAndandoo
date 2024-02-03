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

        $createdZone = Zones::where('NomZ', $zoneData['NomZ'])->first();
    

        $updatedZoneData = [
            'NomZ' => 'Nouveau nom de zone', 
            'user_id' => $user->id
        ];
        $response = $this->postJson("/api/updatezone/{$createdZone->id}", $updatedZoneData);
        $response->assertStatus(200);
    
        $this->assertDatabaseHas('zones', ['id' => $createdZone->id, 'NomZ' => $updatedZoneData['NomZ']]);
    

        $response = $this->get('/api/listzone');
        $response->assertStatus(200);
    

        $response = $this->delete("/api/deletezone/{$createdZone->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('zones', ['id' => $createdZone->id]);
    }
    
    
    
}
