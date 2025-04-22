<?php

namespace App\Http\Requests\notes;

use Illuminate\Foundation\Http\FormRequest;

class NoteRequest extends FormRequest
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
            'text_title' => 'required|min:3|max:200',
            'text_note'  => 'required|min:3|max:3000'

        ];

    }

    // public function messages(): array
    // {
    //     return [
    //         'text_title.required' => 'O Note Title é Obrigatório',
    //         'text_note.required' => 'O Note Text é Obrigatório',
    //         'text_title.min' => 'O Note Title deve ter no mínimo :min caracteres',
    //         'text_title.max' => 'O Note Title deve ter no máximo :max caracteres',
    //         'text_note.min' => 'O Note Text deve ter no mínimo :min caracteres',
    //         'text_note.max' => 'O Note Text deve ter no máximo :max caracteres',
    //     ];
    // }

    public function attributes()
    {
        return [
            'text_title' => 'Título',
            'text_note' => 'Note',

        ];
    }

}
