<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_always_creates_a_client_account(): void
    {
        $this->postJson('/api/v1/register', [
            'name' => 'Nouveau Client',
            'email' => 'client@example.com',
            'telephone' => '0600000001',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ])->assertCreated()
            ->assertJsonMissingPath('data.password');

        $this->assertDatabaseHas('users', [
            'email' => 'client@example.com',
            'telephone' => '0600000001',
            'role' => 'client',
        ]);
    }

    public function test_registration_rejects_a_duplicate_telephone_number(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/register', [
            'name' => 'Autre Client',
            'email' => 'another@example.com',
            'telephone' => $user->telephone,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['telephone']);
    }

    public function test_mobile_user_can_revoke_the_current_access_token(): void
    {
        $user = User::factory()->create([
            'email' => 'mobile@example.com',
        ]);

        $loginResponse = $this->postJson('/api/v1/mobile/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk();

        $token = $loginResponse->json('token');
        $this->assertNotEmpty($token);
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withToken($token)
            ->postJson('/api/v1/mobile/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_web_user_can_revoke_the_current_access_token(): void
    {
        $user = User::factory()->create();

        $loginResponse = $this->postJson('/api/v1/web/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk();

        $token = $loginResponse->json('token');
        $this->assertNotEmpty($token);

        $this->withToken($token)
            ->postJson('/api/v1/web/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Déconnexion réussie.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
