<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DisciplinaUpdateRequest extends FormRequest
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
        $disciplinaId = $this->route('disciplina')->id;

        return [
            'nome' => 'required|string|max:255',
            'codigo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('disciplinas', 'codigo')->ignore($disciplinaId),
            ],
            'descricao' => 'nullable|string|max:1000',
            'carga_horaria' => 'required|integer|min:1|max:999',
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
            'nome.required' => 'O nome da disciplina é obrigatório.',
            'nome.max' => 'O nome da disciplina não pode ter mais de 255 caracteres.',
            'codigo.required' => 'O código da disciplina é obrigatório.',
            'codigo.max' => 'O código da disciplina não pode ter mais de 20 caracteres.',
            'codigo.unique' => 'Este código já está sendo usado por outra disciplina.',
            'descricao.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'carga_horaria.required' => 'A carga horária é obrigatória.',
            'carga_horaria.integer' => 'A carga horária deve ser um número inteiro.',
            'carga_horaria.min' => 'A carga horária deve ser de pelo menos 1 hora.',
            'carga_horaria.max' => 'A carga horária não pode ser maior que 999 horas.',
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