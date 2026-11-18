<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategorieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la catégorie est requis.',
            'nom.string' => 'Le nom de la catégorie doit être une chaîne de caractères.',
            'nom.max' => 'Le nom de la catégorie ne peut pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'is_active.boolean' => "Le champ 'is_active' doit être un booléen.",
        ];
    }
}

