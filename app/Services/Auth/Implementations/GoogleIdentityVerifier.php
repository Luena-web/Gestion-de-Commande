<?php

namespace App\Services\Auth\Implementations;

use App\DTO\Auth\GoogleIdentityDTO;
use App\Services\Auth\Interfaces\GoogleIdentityVerifierInterface;
use Google\Client;
use Illuminate\Auth\AuthenticationException;
use LogicException;
use UnexpectedValueException;

class GoogleIdentityVerifier implements GoogleIdentityVerifierInterface
{
    public function verify(string $credential): GoogleIdentityDTO
    {
        $clientId = config('services.google.client_id');

        if (! is_string($clientId) || $clientId === '') {
            throw new LogicException('Google OAuth client ID is not configured.');
        }

        $client = new Client(['client_id' => $clientId]);

        try {
            $payload = $client->verifyIdToken($credential);
        } catch (UnexpectedValueException) {
            throw new AuthenticationException('The Google credential is invalid.');
        }

        if (
            ! is_array($payload)
            || ! isset($payload['sub'], $payload['email'])
            || ! is_string($payload['sub'])
            || ! is_string($payload['email'])
            || filter_var($payload['email'], FILTER_VALIDATE_EMAIL) === false
            || ! in_array($payload['email_verified'] ?? false, [true, 'true', 1, '1'], true)
        ) {
            throw new AuthenticationException('The Google credential is invalid.');
        }

        $name = $payload['name'] ?? strstr($payload['email'], '@', true);

        return new GoogleIdentityDTO(
            googleId: $payload['sub'],
            email: strtolower($payload['email']),
            name: is_string($name) && $name !== ''
                ? $name
                : $payload['email'],
        );
    }
}
