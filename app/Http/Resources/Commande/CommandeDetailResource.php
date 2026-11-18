<?php

namespace App\Http\Resources\Commande;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommandeDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'produit' => [
                'id' => $this->produit->id,
                'nom' => $this->produit->nom,
            ],

            'quantite' => $this->quantite,
            'prix_unitaire' => $this->prix_unitaire,
            'sous_total' => $this->sous_total,
        ];
    }
}