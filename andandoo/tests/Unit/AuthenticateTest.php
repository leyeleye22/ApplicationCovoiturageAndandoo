<?php

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController;
use App\Http\Requests\LoginAdminRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticateTest extends TestCase
{
    use RefreshDatabase;

    protected $authController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authController = new AuthController();
    }

    protected function createUser()
    {
        User::factory()->create([
            "email" => "leye@gmail.com",
            'password' => Hash::make('Andandoo@12'),
            'remember_token' => Str::random(10),
        ]);
    }

    public function testLoginWithValidCredentials()
    {
        $this->createUser();

        $request = new LoginAdminRequest([
            'email' => 'leye@gmail.com',
            'password' => 'Andandoo@12',
        ]);

        $response = $this->authController->login($request);
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('data', $responseData);
        $responseData = $responseData['data'];

        // Assert that the 'data' array contains the required keys
        $this->assertArrayHasKey('access_token', $responseData);
        $this->assertArrayHasKey('utilisateur', $responseData);
        $this->assertArrayHasKey('statusCode', $responseData);
        $this->assertArrayHasKey('token_type', $responseData);
        $this->assertArrayHasKey('expires_in', $responseData);
    }
    public function testUnitRegisterAdmin()
    {
        $this->createUser();
        $user = User::where('email', 'leye@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('leye@gmail.com', $user->email);
        $this->assertNotNull($user, 'User was not created successfully.');
    }
}
