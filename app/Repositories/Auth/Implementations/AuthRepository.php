<?php

namespace App\Repositories\Auth\Implementations;

use App\DTO\Auth\GoogleIdentityDTO;
use App\Models\User;
use App\Repositories\Auth\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Str;

class AuthRepository implements AuthRepositoryInterface
{
    public function create(array $data): User
    {
        $user = User::create($data);

        return $user->refresh();
    }

    public function findByGoogleId(string $googleId): ?User
    {
        return User::query()->where('google_id', $googleId)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->first();
    }

    public function linkGoogleIdentity(User $user, string $googleId): User
    {
        $user->google_id = $googleId;
        $user->email_verified_at ??= now();
        $user->save();

        return $user->refresh();
    }

    public function createGoogleUser(GoogleIdentityDTO $identity): User
    {
        return $this->create([
            'name' => $identity->name,
            'email' => $identity->email,
            'email_verified_at' => now(),
            'google_id' => $identity->googleId,
            'password' => Str::random(64),
        ]);
    }
}
