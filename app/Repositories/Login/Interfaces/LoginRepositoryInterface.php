<?php

namespace App\Repositories\Login\Interfaces;


use App\Models\User;

interface LoginRepositoryInterface
{
   public function findByEmail(string $email): ?User;
}
