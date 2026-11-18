<?php

namespace App\Repositories\Auth\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function create(array $data): User;
}
