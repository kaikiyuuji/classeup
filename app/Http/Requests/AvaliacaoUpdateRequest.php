<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvaliacaoUpdateRequest extends FormRequest
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
            'av1' => 'nullable|numeric|min:0|max:10',
            'av2' => 'nullable|numeric|min:0|max:10',
            'av3' => 'nullable|numeric|min:0|max:10',
            'av4' => 'nullable|numeric|min:0|max:10',
            'substitutiva' => 'nullable|numeric|min:0|max:10',
            'recuperacao_final' => 'nullable|numeric|min:0|max:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'av1.numeric' => 'A nota AV1 deve ser um número.',
            'av1.min' => 'A nota AV1 deve ser no mínimo 0.',
            'av1.max' => 'A nota AV1 deve ser no máximo 10.',
            'av2.numeric' => 'A nota AV2 deve ser um número.',
            'av2.min' => 'A nota AV2 deve ser no mínimo 0.',
            'av2.max' => 'A nota AV2 deve ser no máximo 10.',
            'av3.numeric' => 'A nota AV3 deve ser um número.',
            'av3.min' => 'A nota AV3 deve ser no mínimo 0.',
            'av3.max' => 'A nota AV3 deve ser no máximo 10.',
            'av4.numeric' => 'A nota AV4 deve ser um número.',
            'av4.min' => 'A nota AV4 deve ser no mínimo 0.',
            'av4.max' => 'A nota AV4 deve ser no máximo 10.',
            'substitutiva.numeric' => 'A nota substitutiva deve ser um número.',
            'substitutiva.min' => 'A nota substitutiva deve ser no mínimo 0.',
            'substitutiva.max' => 'A nota substitutiva deve ser no máximo 10.',
            'recuperacao_final.numeric' => 'A nota de recuperação final deve ser um número.',
            'recuperacao_final.min' => 'A nota de recuperação final deve ser no mínimo 0.',
            'recuperacao_final.max' => 'A nota de recuperação final deve ser no máximo 10.',
        ];
    }
}
