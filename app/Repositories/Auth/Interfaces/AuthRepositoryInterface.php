<?php

namespace App\Repositories\Auth\Interfaces;

use App\DTO\Auth\GoogleIdentityDTO;
use App\Models\User;

interface AuthRepositoryInterface
{
    public function create(array $data): User;

    public function findByGoogleId(string $googleId): ?User;

    public function findByEmail(string $email): ?User;

    public function linkGoogleIdentity(User $user, string $googleId): User;

    public function createGoogleUser(GoogleIdentityDTO $identity): User;
}
