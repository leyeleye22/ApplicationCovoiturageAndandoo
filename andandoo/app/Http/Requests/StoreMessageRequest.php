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
            'NomComplet' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[\p{L}\s\'-]+$/u'],
            'Email' => ['required', 'regex:/^\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b$/'],
            'Contenue' => ['required', 'string', 'min:2', 'regex:/^[\p{L}0-9\s\p{P}]+$/u']
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
            'NomComplet.required' => 'Le champ nom complet est obligatoire.',
            'NomComplet.string' => 'Le champ nom complet doit être une chaîne de caractères.',
            'NomComplet.min' => 'Le nom complet doit comporter au moins 3 caractères.',
            'NomComplet.max' => 'Le nom complet ne peut pas dépasser 30 caractères.',
            'NomComplet.regex' => 'Le champ nom complet n\'est pas au bon format. Seules les lettres alphabétiques, les espaces, les apostrophes et les tirets sont autorisés.',

            'Email.required' => 'Le champ email est obligatoire.',
            'Email.regex' => 'L\'adresse email n\'est pas au bon format.',

            'Contenue.required' => 'Le champ contenu est obligatoire.',
            'Contenue.string' => 'Le champ contenu doit être une chaîne de caractères.',
            'Contenue.min' => 'Le contenu doit comporter au moins 2 caractères.',
            'Contenue.regex' => 'Le champ contenu n\'est pas au bon format. Seules les lettres, les chiffres, les espaces et la ponctuation sont autorisés.'
        ];
    }
}
