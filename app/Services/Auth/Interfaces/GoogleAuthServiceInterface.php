<?php

namespace App\Services\Auth\Interfaces;

use App\Models\User;

interface GoogleAuthServiceInterface
{
    public function authenticate(string $credential): User;
}
