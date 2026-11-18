<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'telephone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @use HasFactory<UserFactory> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function commandes(): HasMany
{
    return $this->hasMany(Commande::class);
}
}
