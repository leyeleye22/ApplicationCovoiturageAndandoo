<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreMessageRequest extends FormRequest
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
            'NomComplet' => ['required', 'string', 'min:3', 'max:30'],
            'Email' => ['required', 'regex:/^.+@.+\..+$/i'],
            'Contenue' => ['required', 'string', 'min:2']
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
            'NomComplet.required' => 'Le champ Nom complet est requis.',
            'NomComplet.string' => 'Le champ Nom complet doit être une chaîne de caractères.',
            'NomComplet.min' => 'Le champ Nom complet doit avoir au moins :min caractères.',
            'NomComplet.max' => 'Le champ Nom complet ne doit pas dépasser :max caractères.',
            'Email.required' => 'Le champ Email est requis.',
            'Email.string' => 'Le champ Email doit être une chaîne de caractères.',
            'Email.regex' => 'Le champ Email doit être une adresse e-mail valide.',
            'Contenue.required' => 'Le champ Contenu est requis.',
            'Contenue.string' => 'Le champ Contenu doit être une chaîne de caractères.',
            'Contenue.min' => 'Le champ Contenu doit avoir au moins :min caractères.'
        ];
    }
}
