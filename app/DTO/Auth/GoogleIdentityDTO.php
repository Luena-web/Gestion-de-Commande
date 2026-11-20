<?php

namespace App\DTO\Auth;

class GoogleIdentityDTO
{
    public function __construct(
        public readonly string $googleId,
        public readonly string $email,
        public readonly string $name,
    ) {}
}
