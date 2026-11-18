<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeDetail extends Model
{
    protected $fillable = [
        'commande_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'sous_total',
    ];

    protected function casts(): array
    {
        return [
            'quantite' => 'integer',
            'prix_unitaire' => 'decimal:2',
            'sous_total' => 'decimal:2',
        ];
    }

    /**
     * Le détail appartient à une commande.
     */
    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    /**
     * Le détail concerne un produit.
     */
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }
}
