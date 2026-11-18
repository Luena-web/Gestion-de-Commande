<?php

namespace App\Services\Login\Interfaces;

use App\DTO\Auth\LoginDTO;
use App\Models\User;

interface LoginServiceInterface
{
    public function login(LoginDTO $dto): User;
}
