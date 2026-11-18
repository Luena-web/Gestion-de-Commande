<?php

namespace App\Services\Auth\Interfaces;

use App\DTO\Auth\RegisterUserDTO;
use App\Models\User;

interface AuthServiceInterface
{
    public function register(RegisterUserDTO $dto): User;
}
