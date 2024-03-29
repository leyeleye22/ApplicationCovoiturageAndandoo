<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReservationRequest extends FormRequest
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
            'NombrePlaces' => 'nullable|integer',
            'trajet_id' => 'required|integer|exists:trajets,id',
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
            'NombrePlaces.integer' => 'Veuillez donnez une nombre de place valide',
            'trajet_id.required' => 'Veuillez choisir un trajet',
            'trajet_id.integer' => 'Choississez un trajet valide',
            'trajet_id.exists' => 'Cette trajet n\'existe pas',
        ];
    }
}
