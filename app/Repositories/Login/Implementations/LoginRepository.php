<?php

namespace App\Repositories\Login\Implementations;

use App\Models\User;
use App\Repositories\Login\Interfaces\LoginRepositoryInterface;

class LoginRepository implements LoginRepositoryInterface
{
    
public function findByEmail(string $email): ?User
{
    return User::where('email', $email)->first();
}

}
