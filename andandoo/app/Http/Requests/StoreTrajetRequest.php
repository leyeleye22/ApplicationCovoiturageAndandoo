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
            'LieuDepart' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'LieuArrivee' => ['required', 'string', 'min:3', 'max:255', 'different:LieuDepart', 'regex:/^[a-zA-Z\s]+$/'],
            'DateDepart' => ['required', 'date', 'after_or_equal:today'],
            'HeureD' => ['required', 'date_format:H:i'],
            'Prix' => ['required', 'numeric', 'min:100'],
            'DescriptionTrajet' => ['nullable', 'string'],
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
            'LieuDepart.min' => 'Le lieu de départ doit contenir au moins 3 caractères alphabétiques.',
            'LieuDepart.regex' => 'Le lieu de départ ne doit contenir que des lettres alphabétiques et des espaces.',
            'LieuArrivee.required' => 'Le lieu d\'arrivée est obligatoire.',
            'LieuArrivee.string' => 'Le lieu d\'arrivée doit être une chaîne de caractères.',
            'LieuArrivee.max' => 'Le lieu d\'arrivée ne doit pas dépasser 255 caractères.',
            'LieuArrivee.min' => 'Le lieu d\'arrivée doit contenir au moins 3 caractères alphabétiques.',
            'LieuArrivee.regex' => 'Le lieu d\'arrivée ne doit contenir que des lettres alphabétiques et des espaces.',
            'LieuArrivee.different' => 'Le lieu d\'arrivée ne doit pas être le même que le lieu de départ.',
            'DateDepart.required' => 'La date de départ est obligatoire.',
            'DateDepart.date' => 'La date de départ doit être une date valide.',
            'DateDepart.after_or_equal' => 'La date de départ doit être aujourd\'hui ou dans le futur.',
            'HeureD.required' => 'L\'heure de départ est obligatoire.',
            'HeureD.date_format' => 'L\'heure de départ doit être au format H:i (par exemple 12:30).',
            'Prix.required' => 'Le prix du trajet est obligatoire.',
            'Prix.numeric' => 'Le prix du trajet doit être un nombre.',
            'Prix.min' => 'Le prix du trajet ne peut pas être inférieur à 100.',
            'DescriptionTrajet.string' => 'La description du trajet doit être une chaîne de caractères.',
        ];
    }
}
