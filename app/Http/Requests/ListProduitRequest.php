<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ListProduitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:255'],
            'categorie_id' => ['sometimes', 'integer', 'min:1', 'exists:categories,id'],
            'prix_min' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0', 'max:999999.99'],
            'prix_max' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0', 'max:999999.99'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $minimum = $this->input('prix_min');
                $maximum = $this->input('prix_max');

                if (
                    is_numeric($minimum)
                    && is_numeric($maximum)
                    && (float) $minimum > (float) $maximum
                ) {
                    $validator->errors()->add(
                        'prix_max',
                        'Le prix maximal doit être supérieur ou égal au prix minimal.'
                    );
                }
            },
        ];
    }
}
