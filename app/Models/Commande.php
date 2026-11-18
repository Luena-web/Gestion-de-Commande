<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commande extends Model
{
    protected $fillable = [
        'numero_commande',
        'user_id',
        'montant_total',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'montant_total' => 'decimal:2',
        ];
    }

    /**
     * Une commande appartient à un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une commande possède plusieurs détails.
     */
    public function details(): HasMany
    {
        return $this->hasMany(CommandeDetail::class);
    }
}
