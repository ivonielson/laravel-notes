<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'text_username' => 'required|email',
            'text_password' => 'required|min:6|max:16'
        ];


    }

    // public function messages(): array
    // {
    //     return [
    //         'text_username.required' => 'O username é Obrigatório >> validação pelo request',
    //         'text_username.email' => 'Username deve ser um email válido >> validação pelo request',
    //         'text_password.required' => 'O Password é Obrigatório >> validação pelo request',
    //         'text_password.min' => 'O Password deve ter no mínimo :min caracteres >> validação pelo request',
    //         'text_password.max' => 'O Password deve ter no máximo :max caracteres >> validação pelo request'
    //     ];
    // }
}
