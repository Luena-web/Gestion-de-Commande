<?php

namespace App\Repositories\Auth\Implementations;

use App\Models\User;
use App\Repositories\Auth\Interfaces\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    public function create(array $data): User
    {
       $user = User::create($data);
       return $user->refresh();
    }
}
