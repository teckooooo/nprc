<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCredencialesCorporativoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rut_corporativo' => ['required','string'],
            'user_email_1'    => ['required','email'],
            'user_rut_1'      => ['required','string'],
            'user_pass_1'     => ['required','string','min:6'],   // ← mínimo 6

            // segundo par opcional
            'user_email_2'    => ['nullable','email'],
            'user_rut_2'      => ['nullable','string','required_with:user_email_2,user_pass_2'],
            'user_pass_2'     => ['nullable','string','min:6','required_with:user_email_2,user_rut_2'], // ← mínimo 6
        ];
    }

    public function messages(): array
    {
        return [
            'user_pass_1.required' => 'La contraseña es obligatoria.',
            'user_pass_1.min'      => 'La contraseña debe tener al menos 6 caracteres.',
            'user_pass_2.min'      => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
