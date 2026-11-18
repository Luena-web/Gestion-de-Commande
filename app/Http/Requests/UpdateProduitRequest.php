<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProduitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['sometimes', 'required', 'string', 'max:255'],

            'prix' => [
                'sometimes',
                'required',
                'numeric',
                'decimal:0,2',
                'gt:0',
                'max:999999.99',
            ],

            'categorie_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:categories,id'
            ],

            'stock' => ['sometimes', 'required', 'integer', 'min:0'],

            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}