<?php

namespace Tests\Feature;

use Tests\TestCase;

class CorsTest extends TestCase
{
    public function test_local_frontend_origin_is_allowed_for_google_login_preflight(): void
    {
        config()->set('cors.allowed_origins', [
            'http://localhost:5173',
            'http://127.0.0.1:5173',
        ]);

        $response = $this->call('OPTIONS', '/api/v1/google/login', [], [], [], [
            'HTTP_ORIGIN' => 'http://localhost:5173',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'authorization,content-type',
        ])
            ->assertNoContent()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173');

        self::assertStringContainsString(
            'POST',
            $response->headers->get('Access-Control-Allow-Methods')
        );
        self::assertStringContainsString(
            'authorization',
            strtolower($response->headers->get('Access-Control-Allow-Headers'))
        );
        $response->assertHeaderMissing('Access-Control-Allow-Credentials');
    }

    public function test_unlisted_origin_is_not_allowed(): void
    {
        config()->set('cors.allowed_origins', [
            'http://localhost:5173',
            'http://127.0.0.1:5173',
        ]);

        $this->call('OPTIONS', '/api/v1/google/login', [], [], [], [
            'HTTP_ORIGIN' => 'https://untrusted.example',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'content-type',
        ])
            ->assertNoContent()
            ->assertHeaderMissing('Access-Control-Allow-Origin');
    }
}
