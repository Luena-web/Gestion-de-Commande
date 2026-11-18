<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'details' => [
                'required',
                'array',
                'min:1',
            ],

            'details.*.produit_id' => [
                'required',
                'integer',
                'exists:produits,id',
                'distinct',
            ],

            'details.*.quantite' => [
                'required',
                'integer',
                'min:1',
                'max:4294967295',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'details.required' => 'Vous devez ajouter au moins un produit.',
            'details.array' => 'Les détails doivent être transmis sous forme de tableau.',
            'details.min' => 'Une commande doit contenir au moins un produit.',

            'details.*.produit_id.required' => 'Le produit est obligatoire.',
            'details.*.produit_id.exists' => 'Le produit sélectionné n’existe pas.',
            'details.*.produit_id.distinct' => 'Un produit ne peut apparaître qu’une seule fois.',

            'details.*.quantite.required' => 'La quantité est obligatoire.',
            'details.*.quantite.integer' => 'La quantité doit être un nombre entier.',
            'details.*.quantite.min' => 'La quantité minimale est de 1.',
            'details.*.quantite.max' => 'La quantité demandée dépasse la limite autorisée.',
        ];
    }
}
