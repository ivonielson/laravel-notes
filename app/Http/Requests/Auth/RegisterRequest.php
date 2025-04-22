<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

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
    public function rules(): array
    {
        return [
            'text_username' => 'required|email|min:3|max:60|unique:users,username',
            'text_password' => 'required|min:6|max:16',
            'text_password_confirmation' => 'required|same:text_password'
        ];

    }

    // public function messages(): array
    // {
    //     return [
    //         'text_username.required' => 'O email é obrigatório >> validação pelo request',
    //         'text_username.email' => 'Deve ser um email válido >> validação pelo request',
    //         'text_username.min' => 'O email deve ter no mínimo :min caracteres >> validação pelo request',
    //         'text_username.max' => 'O email deve ter no máximo :max caracteres >> validação pelo request',
    //         'text_username.unique' => 'Este email já está em uso >> validação pelo request',
    //         'text_password.required' => 'A senha é obrigatória >> validação pelo request',
    //         'text_password.min' => 'A senha deve ter no mínimo :min caracteres >> validação pelo request',
    //         'text_password.max' => 'A senha deve ter no máximo :max caracteres >> validação pelo request',
    //         'text_password_confirmation.required' => 'A confirmação de senha é obrigatória >> validação pelo request',
    //         'text_password_confirmation.same' => 'As senhas não coincidem >> validação pelo request'
    //     ];
    // }

}
