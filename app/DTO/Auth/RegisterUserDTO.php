<?php

namespace App\DTO\Auth;

class RegisterUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $telephone,
        public readonly string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            telephone: $data['telephone'] ,
            password: $data['password'],
        );
    }
}
