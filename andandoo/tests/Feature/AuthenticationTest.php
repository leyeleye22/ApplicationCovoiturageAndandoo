<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Zones;
use App\Models\Utilisateur;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test the authentication process.
     *
     * @return void
     */
    public function testAuthentication()
    {
        $admin = $this->createAdmin();
        $zone = $this->createZone($admin);
        $chauffeur = $this->createChauffeur($zone);
        $client = $this->createClient($zone);
        $this->loginAdmin($admin);
        $this->loginUser($client);
        $this->blockTemporarilyUser($admin, $chauffeur);
        $this->unlockUser($admin, $chauffeur);
        $this->blockDefinitelyUser($admin, $client);

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

    protected function loginUser($client)
    {
        $loginData = [
            'email' => $client->email,
            'password' => 'password'
        ];

        $response = $this->post('api/login', $loginData);
        $response->assertStatus(200);
    }

    protected function loginAdmin($admin)
    {
        $loginData = [
            'email' => $admin->email,
            'password' => 'andandoo12'
        ];

        $response = $this->post('api/loginadmin', $loginData);
        $response->assertStatus(200);
    }
    protected function blockTemporarilyUser($admin, $chauffeur)
    {

        $response = $this->actingAs($admin, 'api')->postJson("api/BlockerTemporairement/{$chauffeur->id}");


        $response->assertStatus(200);
    }

    protected function blockDefinitelyUser($admin, $client)
    {

        $response = $this->actingAs($admin, 'api')->postJson("api/BlockerDefinitivement/{$client->id}");

        $response->assertStatus(200);
    }

    protected function unlockUser($admin, $chauffeur)
    {

        if ($chauffeur->TemporaryBlock) {
            $response = $this->actingAs($admin, 'api')->postJson("api/Debloquer/{$chauffeur->id}");
            $response->assertStatus(200);
        }
    }

    protected function logoutAdmin($admin)
    {
        $loginData = [
            'email' => $admin->email,
            'password' => 'andandoo12'
        ];

        $login =  $this->post('/api/login', $loginData);
        $token = $login->Json('token');
        $response = $this->withHeaders(['Authorization' => "Bearer $token)"])->post('/api/logoutadmin');
        $response->assertStatus(200)
            ->assertJson([
                "status" => true,
                "message" => $response->json('message')
            ]);
    }

    protected function logoutUser($client)
    {
        $token = JWTAuth::fromUser($client);
        $response = $this->withHeader('Authorization', 'Bearer' . $token)->post('/api/logout/user');
    }
}
