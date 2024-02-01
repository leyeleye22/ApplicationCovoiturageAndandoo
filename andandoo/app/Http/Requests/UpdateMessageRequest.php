<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateMessageRequest extends FormRequest
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
            'id' => ['required|integer|exists:messages,id'],
            'contenue' => ['required|string|min:2'],
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
            'id.required' => 'l\'identifiant est requis',
            'id.integer' => 'l\'identifiant doit être un nombre entier',
            'id.exists' => 'Cette personne n\'existe pas',
            'contenue.required' => 'Impossible d\'envoyer un message vide',
            'contenue.string' => 'le contenue doit être une chaine de caractere',
            'contenue.min' => ''
        ];
    }
}
