<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreVoitureRequest extends FormRequest
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
            'ImageVoitures' => 'required|image|max:2048', // Doit être une image et ne pas dépasser 2MB
            'Descriptions' => 'required|string|max:255', // Doit être une chaîne de caractères et ne pas dépasser 255 caractères
            'NbrPlaces' => 'required|integer|min:1', // Doit être un nombre entier et au moins 1
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
            'ImageVoitures.required' => 'l\'image du vehicule est obligatoire',
            'ImageVoitures.image' => 'Sa doit être une image',
            'ImageVoitures.max' => 'La taille de l\image doit pas depasser 2mo',
            'Descriptions.required' => 'Une description est requis',
            'Descriptions.string' => 'La description doit être une chaîne de  caractére',
            'Descriptions.max' => 'La description ne doit pas depasser 255 caractére',
            'NbrPlaces.required' => 'Veuillez choisir une nombre de place',
            'NbrPlaces.min' => 'Il faut minimum choisir une nombre de place',
            'NbrPlaces.integer' => 'Le nombre de places doit être un chiffre'

        ];
    }
}
