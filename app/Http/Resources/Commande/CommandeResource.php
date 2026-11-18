<?php

namespace App\Http\Resources\Commande;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'numero_commande' => $this->numero_commande,

            'montant_total' => $this->montant_total,

            'statut' => $this->statut,

            'details' => CommandeDetailResource::collection(
                $this->whenLoaded('details')
            ),

            'date_commande' => $this->created_at?->format('d/m/Y H:i'),
        ];
    }
}