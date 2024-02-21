<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AvertissementRequest extends FormRequest
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
            'email' => 'required|email|regex:/^[^.@]+(\.[^.@]+)*@[^.@]+\.[^@]+$/|exists:utilisateurs,email',
            'contenue' => 'required|string|regex:/^[a-zA-Z\s]+$/',
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
            'email.required' => 'L\'adresse e-mail est requise.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.regex' => 'L\'adresse e-mail n\'est pas dans un format valide.',
            'email.exists' => 'L\'adresse e-mail n\'existe pas.',
            'contenue.required' => 'Le contenu du message est requis.',
            'contenue.string' => 'Le contenu du message doit être une chaîne de caractères.',
            'contenue.regex' => 'Le contenu du message ne doit contenir que des lettres alphabétiques et des espaces.',
        ];
    }
}
