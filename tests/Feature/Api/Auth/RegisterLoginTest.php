<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_returns_json_and_creates_user()
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test+api@example.com',
            'password' => 'verysecurepassword',
            'locale' => 'en',
            'time_zone' => 'UTC',
            'device_name' => 'phpunit',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'token',
                ],
                'meta',
                'errors',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'test+api@example.com']);
    }

    public function test_login_returns_json_and_token()
    {
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
        ];

        $response = $this->postJson('/api/auth/login', $payload);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'token',
                ],
                'meta',
                'errors',
            ]);
    }
}
