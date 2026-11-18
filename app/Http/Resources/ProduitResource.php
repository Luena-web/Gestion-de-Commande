<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProduitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prix' => $this->prix,
            'stock' => $this->stock,
            'is_active' => $this->is_active,

            'categorie' => [
                'id' => $this->whenLoaded('categorie', function () {
                    return $this->categorie->id;
                }),

                'nom' => $this->whenLoaded('categorie', function () {
                    return $this->categorie->nom;
                }),
            ],

            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
        ];
    }
}
