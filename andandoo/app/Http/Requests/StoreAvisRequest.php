<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreAvisRequest extends FormRequest
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
            'Contenue' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\'-]*$/',
            'Notation' => 'required|integer|min:1|max:5',
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
            'Contenue.required' => 'Le champ contenu est obligatoire.',
            'Contenue.string' => 'Le champ contenu doit être une chaîne de caractères.',
            'Contenue.max' => 'Le contenu ne peut pas dépasser 255 caractères.',
            'Contenue.regex' => 'Le champ contenu n\'est pas au bon format. Il ne doit contenir que des lettres, des chiffres, des espaces, des apostrophes et des tirets.',
            'Notation.required' => 'Le champ notation est obligatoire.',
            'Notation.integer' => 'Le champ notation doit être un nombre entier.',
            'Notation.min' => 'La notation doit être d\'au moins 1.',
            'Notation.max' => 'La notation ne peut pas dépasser 5.'
        ];
    }
}
