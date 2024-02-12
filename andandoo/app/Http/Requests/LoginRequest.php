<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|regex:/^.+@.+\..+$/i|exists:utilisateurs',
            'password' => 'required|min:8'
        ];
    }
    public function failedValidation(validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status_code' => 422,
            'error' => true,
            'message' => 'erreur de validation',
            'errorList' => $validator->errors()
        ]));
    }
    public function messages()
    {
        return [
            'email.required' => 'Le champ email est obligatoire.',
            'email.email' => 'Le champ doit être un email.',
            'email.exists' => 'L\'email existe pas.',
            'password.required' => 'Le mot de passe est obliagatoire.',
            'password.min' => 'Le mot de passe doit être superieur ou egal à 8.'
        ];
    }
}
