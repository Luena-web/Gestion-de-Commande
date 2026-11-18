<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProduitRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur peut effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation des données.
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],

            'prix' => ['required', 'numeric', 'decimal:0,2', 'gt:0', 'max:999999.99'],

            'categorie_id' => ['required', 'integer', 'exists:categories,id'],

            'stock' => ['sometimes', 'integer', 'min:0'],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}