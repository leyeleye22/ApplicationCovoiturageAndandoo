<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    /**
     * A basic feature test example.
     * // $response = $this->post('/login');
     */
    public function testresgisterUser(): void
    {
        $user = Utilisateur::factory()->create();
        $unserinsert = $user->toArray();
        $this->assertDatabaseHas('Utilisateurs', $unserinsert);
    }
    public function testRegisterAdmin(): void
    {
        $user = User::factory()->create();
        $unserinsert = $user->toArray();
        $this->assertDatabaseHas('Users', $unserinsert);
    }
    public function testLoginUser(): void
    {
        $user = Utilisateur::factory()->create();
        $credentials = ['email' => $user->email, 'password' => $user->password];
        $response = $this->post('api/login', $credentials);
        $response->assertStatus(200);
    }
    public function testLoginAdmin(): void
    {
        $credentials = ['email' => 'leye@gmail.com', 'password' => 'leye22@22'];
        $response = $this->post('api/loginadmin', $credentials);
        $response->assertStatus(200);
    }
    public function testBloquerTemporairementUtilisateur(): void
    {
    }
    public function testBloquerDefinitivementUtilisateur(): void
    {
    }
    public function testDebloquerUtilisateur(): void
    {
    }
}
