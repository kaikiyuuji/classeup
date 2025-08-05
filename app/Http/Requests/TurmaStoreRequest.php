<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TurmaStoreRequest extends FormRequest
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
            'nome' => 'required|string|max:255',
            'ano_letivo' => 'required|integer|min:2020|max:2030',
            'serie' => 'required|string|max:255',
            'turno' => 'required|in:matutino,vespertino,noturno',
            'capacidade_maxima' => 'required|integer|min:1|max:50',
            'ativo' => 'boolean',
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
            'nome.required' => 'O nome da turma é obrigatório.',
            'nome.max' => 'O nome da turma não pode ter mais de 255 caracteres.',
            'ano_letivo.required' => 'O ano letivo é obrigatório.',
            'ano_letivo.integer' => 'O ano letivo deve ser um número inteiro.',
            'ano_letivo.min' => 'O ano letivo deve ser no mínimo 2020.',
            'ano_letivo.max' => 'O ano letivo deve ser no máximo 2030.',
            'serie.required' => 'A série é obrigatória.',
            'serie.max' => 'A série não pode ter mais de 255 caracteres.',
            'turno.required' => 'O turno é obrigatório.',
            'turno.in' => 'O turno deve ser matutino, vespertino ou noturno.',
            'capacidade_maxima.required' => 'A capacidade máxima é obrigatória.',
            'capacidade_maxima.integer' => 'A capacidade máxima deve ser um número inteiro.',
            'capacidade_maxima.min' => 'A capacidade máxima deve ser no mínimo 1.',
            'capacidade_maxima.max' => 'A capacidade máxima deve ser no máximo 50.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'ativo' => $this->has('ativo'),
        ]);
    }
}
