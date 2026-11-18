<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Produit extends Model
{
    protected $fillable = [
        'nom',
        'prix',
        'categorie_id',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'prix' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

   public function commandeDetails(): HasMany
  {
    return $this->hasMany(CommandeDetail::class);
  }
}