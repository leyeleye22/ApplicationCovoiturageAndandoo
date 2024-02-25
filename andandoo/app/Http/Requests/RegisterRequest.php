<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            // 'Nom' => 'required|string|min:2|max:255|regex:/^[a-zA-ZÀ-ÿ\s\'-]{2,}$/',,
            'Nom' => 'required|string|min:2|max:255',
            // 'Prenom' => 'required|string|min:3|max:255|regex:/^[a-zA-ZÀ-ÿ\s\'-]{3,}$/',
            'Prenom' => 'required|string|min:3|max:255',
            'Email' => 'required|string|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:utilisateurs',
            'Telephone' => ['required', 'string'],
            'role' => 'required|in:client,chauffeur',
            'zone_id' => 'required|integer|exists:zones,id',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
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
            'Nom.string' => 'Le champ nom doit être une chaîne de caractères.',
            'Nom.min' => 'Le champ nom doit comporter au moins 2 caractères.',
            'Nom.max' => 'Le champ nom ne peut pas dépasser 255 caractères.',
            'Nom.regex' => 'Le champ nom ne doit contenir que des lettres, espaces, apostrophes et tirets.',

            'Prenom.required' => 'Le champ prénom est obligatoire.',
            'Prenom.string' => 'Le champ prénom doit être une chaîne de caractères.',
            'Prenom.min' => 'Le champ prénom doit comporter au moins 3 caractères.',
            'Prenom.max' => 'Le champ prénom ne peut pas dépasser 255 caractères.',
            'Prenom.regex' => 'Le champ prénom ne doit contenir que des lettres, espaces, apostrophes et tirets.',

            'Email.required' => 'Le champ email est obligatoire.',
            'Email.string' => 'Le champ email doit être une chaîne de caractères.',
            'Email.regex' => 'L\'email n\'est pas au bon format.',
            'Email.unique' => 'Cet email existe  dans notre base de données.',

            'Telephone.required' => 'Le champ téléphone est obligatoire.',
            'Telephone.string' => 'Le champ téléphone doit être une chaîne de caractères.',
            'Telephone.regex' => 'Le champ téléphone doit être un numéro de téléphone valide en commençant par l\'un des préfixes 77, 78, 70 ou 75 suivi de 7 chiffres.',

            'role.required' => 'Le champ rôle est obligatoire.',
            'role.in' => 'Le champ rôle doit être soit "client" soit "chauffeur".',

            'zone_id.required' => 'Le champ zone est obligatoire.',
            'zone_id.integer' => 'Le champ zone doit être un entier.',
            'zone_id.exists' => 'La zone sélectionnée n\'existe pas.',

            'password.required' => 'Le champ mot de passe est obligatoire.',
            'password.string' => 'Le champ mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit comporter au moins 8 caractères.',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre minuscule, une lettre majuscule, un chiffre, un caractère spécial et avoir une longueur d\'au moins 8 caractères.'
        ];
    }
}
