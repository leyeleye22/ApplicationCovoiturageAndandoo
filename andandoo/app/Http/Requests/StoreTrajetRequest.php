<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTrajetRequest extends FormRequest
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
            'LieuDepart' => 'required|string|min:3|max:255',
            'LieuArrivee' => 'required|string|min:3|max:255|different:LieuDepart',
            'DateDepart' => 'required|date|after_or_equal:today',
            'HeureD' => 'required|date_format:H:i',
            'Prix' => 'required|numeric|min:100',
            'DescriptionTrajet' => 'nullable|string',
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
            'LieuDepart.required' => 'Le lieu de départ est obligatoire.',
            'LieuDepart.string' => 'Le lieu de départ doit être une chaîne de caractères.',
            'LieuDepart.max' => 'Le lieu de départ ne doit pas dépasser 255 caractères.',
            'LieuDepart.min' => 'Le lieu de départ ne doit pas etre inferieur a 3 caractères.',
            'LieuArrivee.required' => 'Le lieu d\'arrivée est obligatoire.',
            'LieuArrivee.string' => 'Le lieu d\'arrivée doit être une chaîne de caractères.',
            'LieuArrivee.min' => 'Le lieu de d\'arrivée ne doit pas etre inferieur a 3 caractères.',
            'LieuArrivee.max' => 'Le lieu d\'arrivée ne doit pas dépasser 255 caractères.',
            'LieuArrivee.different' => 'Le lieu d\'arrivée ne doit pas etre le meme que le lieu de depart',
            'DateDepart.required' => 'La date de départ est obligatoire.',
            'DateDepart.date' => 'La date de départ doit être une date valide.',
            'DateDepart.after_or_equal' => 'La date de départ doit être aujourd\'hui ou dans le futur.',
            'HeureD.required' => 'L\'heure de départ est obligatoire.',
            'HeureD.date_format' => 'L\'heure de départ doit être au format H:i(12:30).',
            'Prix.required' => 'Le prix du trajet est obligatoire.',
            'Prix.numeric' => 'Le prix du trajet doit être un nombre.',
            'Prix.min' => 'Le prix du trajet ne peut pas être inferieur a 100.',
            'DescriptionTrajet.string' => 'La description du trajet doit être une chaîne de caractères.',

        ];
    }
}
