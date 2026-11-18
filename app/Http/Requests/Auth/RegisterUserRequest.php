<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'telephone' => [
                'required',
                'string',
                'max:20',
                'unique:users,telephone',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'password_confirmation' => [
                'required',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
        ];
    }
}
