<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Utilisateur;
use App\Models\Zones;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test creating user, zone and authentication processes.
     *
     * @return void
     */
    public function testAuthentication()
    {
        $admin = User::factory()->create(['email' => 'admin@andandoo.com', 'password' => bcrypt('andandoo12')]);
        $zone = Zones::factory()->create(['NomZ' => $this->faker->city, 'user_id' => $admin->id]);
        $chauffeur = Utilisateur::factory()->create(['role' => 'chauffeur', 'zone_id' => $zone->id, 'password' => bcrypt('password')]);
        $client = Utilisateur::factory()->create(['role' => 'client', 'zone_id' => $zone->id, 'password' => bcrypt('password')]);
        $this->assertNotNull($admin);
        $this->assertNotNull($zone);
        $this->assertNotNull($chauffeur);
        $this->assertNotNull($client);
        $this->actingAs($admin);
        $this->assertEquals(auth()->id(), $admin->id);
        $this->actingAs($client);
        $this->assertEquals(auth()->id(), $client->id);
        $chauffeur->TemporaryBlock = true;
        $chauffeur->save();
        $this->assertTrue($chauffeur->TemporaryBlock);
        $chauffeur->TemporaryBlock = false;
        $chauffeur->save();
        $this->assertFalse($chauffeur->TemporaryBlock);
        $client->PermanentBlock = true;
        $client->save();
        $this->assertTrue($client->PermanentBlock);
    }
}
