<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUtilisateurRequest extends FormRequest
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
    public function rules()
    {


        return [
            'Nom' => 'required|string|min:2|max:255',
            'Prenom' => 'required|string|max:255',
            'Telephone' => ['required', 'string', 'regex:/^(77|78|70|75)[0-9]{7}$/'],
            'NomZ' => 'required|string|exists:zones,NomZ',
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
            'Nom.required' => 'Le champ nom est obligatoire.',
            'Nom.string' => 'Le nom doit être une chaîne de caractères.',
            'Nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'Nom.min' => 'Le nom doit être au minimum  2 caractères.',
            'Prenom.required' => 'Le champ prénom est obligatoire.',
            'Prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'Prenom.max' => 'Le prénom ne peut pas dépasser 255 caractères.',
            'Telephone.required' => 'Le champ téléphone est obligatoire.',
            'Telephone.string' => 'Le téléphone doit être une chaîne de caractères.',
            'Telephone.min' => 'Le téléphone doit comporter au moins 10 caractères.',
            'Telephone.regex' => 'Le téléphone doit commencer 70/77/78/76 suivis de 7 caractere',
            'Telephone.max' => 'Le téléphone ne peut pas dépasser 15 caractères.',
            'NomZ.required' => 'Le champ NomZ est obligatoire.',
            'NomZ.integer' => 'Le NomZ doit être un string.',
            'NomZ.exists' => 'Cette zone n\'existe pas.',
        ];
    }
}
