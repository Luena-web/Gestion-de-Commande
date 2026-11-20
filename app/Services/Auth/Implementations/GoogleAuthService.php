<?php

namespace App\Services\Auth\Implementations;

use App\Models\User;
use App\Repositories\Auth\Interfaces\AuthRepositoryInterface;
use App\Services\Auth\Interfaces\GoogleAuthServiceInterface;
use App\Services\Auth\Interfaces\GoogleIdentityVerifierInterface;
use Illuminate\Auth\AuthenticationException;

class GoogleAuthService implements GoogleAuthServiceInterface
{
    public function __construct(
        private readonly GoogleIdentityVerifierInterface $identityVerifier,
        private readonly AuthRepositoryInterface $authRepository,
    ) {}

    public function authenticate(string $credential): User
    {
        $identity = $this->identityVerifier->verify($credential);

        $user = $this->authRepository->findByGoogleId($identity->googleId);

        if ($user) {
            return $user;
        }

        $user = $this->authRepository->findByEmail($identity->email);

        if ($user) {
            if ($user->google_id !== null) {
                throw new AuthenticationException(
                    'This account is linked to a different Google identity.'
                );
            }

            return $this->authRepository->linkGoogleIdentity(
                $user,
                $identity->googleId
            );
        }

        return $this->authRepository->createGoogleUser($identity);
    }
}
