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
            'LieuDepart' => 'required|string',
            'LieuArrivee' => 'required|string',
            'DateDepart' => 'required|date',
            'HeureD' => 'required',
            'Prix' => 'required',
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
    public function messages(){
        return [
            'LieuDepart.required'=>'le lieu de depart est obligatoire',
            'LieuDepart.string'=>'le lieu de depart est doit être une chaîne de caractére',
            'LieuArrivee.required'=>'le lieu d\'arrivee est obligatoire',
            'LieuArrivee.string'=>'le lieu d\' est doit être une chaîne de caractére',
            'DateDepart.required'=>'la date de depart est obligatoire',
            'DateDepart.date'=>'la date de depart doit être une date valide',
            'HeureD.required'=>'L\'heure de depart est obligatoire',
            'Prix.required'=>'le prix du trajet est obligatoire',
        ];
    }
}
