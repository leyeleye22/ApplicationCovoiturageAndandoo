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
            'Nom' => 'required|string|min:2|max:255',
            'Prenom' => 'required|string|min:3|max:255',
            // 'Email' => 'required|regex:/^.+@.+\..+$/i|exists:utilisateurs',
            'Telephone' => ['required', 'string', 'regex:/^(77|78|70|75)[0-9]{7}$/'],
            'role' => 'required|in:client,chauffeur',
            'zone_id' => 'required|integer|exists:zones,id',
            'password' => 'required|string|min:8',
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
            'Prenom.min' => 'Le prénom ne peut pas etre inferieur a 3 caractères.',
            'Email.required' => 'Le champ email est obligatoire.',
            'Email.regex' => 'L\'email doit être une adresse email valide.',
            'Email.unique' => 'Cet email est déjà utilisé.',
            'Telephone.required' => 'Le champ téléphone est obligatoire.',
            'Telephone.string' => 'Le téléphone doit être une chaîne de caractères.',
            'Telephone.min' => 'Le téléphone doit comporter au moins 10 caractères.',
            'Telephone.regex' => 'Le téléphone doit commencer 70/77/78/76 suivis de 7 caractere',
            'Telephone.max' => 'Le téléphone ne peut pas dépasser 15 caractères.',
            'role.required' => 'Le champ rôle est obligatoire.',
            'role.in' => 'Le rôle doit être soit client, soit chauffeur.',
            'zone_id.required' => 'Le champ zone_id est obligatoire.',
            'zone_id.integer' => 'Le zone_id doit être un entier.',
            'zone_id.exists' => 'Cette zone n\'existe pas.',
            'password.required' => 'Le champ mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit comporter au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}
