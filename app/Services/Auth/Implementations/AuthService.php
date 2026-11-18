<?php

namespace App\Services\Auth\Implementations;

use App\DTO\Auth\RegisterUserDTO;
use App\Models\User;
use App\Repositories\Auth\Interfaces\AuthRepositoryInterface;
use App\Services\Auth\Interfaces\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly AuthRepositoryInterface $authRepository
    ) {}

    public function register(RegisterUserDTO $dto): User
    {
        return $this->authRepository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'telephone' => $dto->telephone,
            'password' => $dto->password,
        ]);
    }
}
