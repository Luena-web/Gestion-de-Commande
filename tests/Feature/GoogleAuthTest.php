<?php

namespace Tests\Feature;

use App\DTO\Auth\GoogleIdentityDTO;
use App\Models\User;
use App\Services\Auth\Interfaces\GoogleIdentityVerifierInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_login_registers_a_verified_client_without_a_phone_number(): void
    {
        $this->mock(GoogleIdentityVerifierInterface::class)
            ->shouldReceive('verify')
            ->once()
            ->with('valid-google-credential')
            ->andReturn(new GoogleIdentityDTO(
                googleId: 'google-sub-123',
                email: 'new-client@example.com',
                name: 'New Client',
            ));

        $response = $this->postJson('/api/v1/google/login', [
            'credential' => 'valid-google-credential',
        ])->assertOk()
            ->assertJsonPath('data.email', 'new-client@example.com')
            ->assertJsonPath('data.telephone', null)
            ->assertJsonPath('data.role', 'client');

        $this->assertNotEmpty($response->json('token'));
        $this->assertDatabaseHas('users', [
            'google_id' => 'google-sub-123',
            'email' => 'new-client@example.com',
            'telephone' => null,
            'role' => 'client',
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_google_login_links_an_existing_account_by_verified_email(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'telephone' => '0600000099',
            'email_verified_at' => null,
        ]);

        $this->mock(GoogleIdentityVerifierInterface::class)
            ->shouldReceive('verify')
            ->once()
            ->andReturn(new GoogleIdentityDTO(
                googleId: 'google-sub-existing',
                email: 'existing@example.com',
                name: 'Google Name',
            ));

        $response = $this->postJson('/api/v1/google/login', [
            'credential' => 'valid-google-credential',
        ])->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.telephone', '0600000099');

        $this->assertNotEmpty($response->json('token'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'google_id' => 'google-sub-existing',
            'email_verified_at' => $user->fresh()->email_verified_at,
        ]);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_google_login_rejects_an_account_already_linked_to_another_google_identity(): void
    {
        User::factory()->create([
            'email' => 'linked@example.com',
            'google_id' => 'another-google-sub',
        ]);

        $this->mock(GoogleIdentityVerifierInterface::class)
            ->shouldReceive('verify')
            ->once()
            ->andReturn(new GoogleIdentityDTO(
                googleId: 'unrelated-google-sub',
                email: 'linked@example.com',
                name: 'Client',
            ));

        $this->postJson('/api/v1/google/login', [
            'credential' => 'valid-google-credential',
        ])->assertUnauthorized();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_google_login_rejects_an_invalid_google_credential(): void
    {
        $this->mock(GoogleIdentityVerifierInterface::class)
            ->shouldReceive('verify')
            ->once()
            ->andThrow(new AuthenticationException('The Google credential is invalid.'));

        $this->postJson('/api/v1/google/login', [
            'credential' => 'invalid-google-credential',
        ])->assertUnauthorized();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_google_login_requires_a_credential(): void
    {
        $this->postJson('/api/v1/google/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['credential']);
    }
}
