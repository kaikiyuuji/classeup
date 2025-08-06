<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DesvincularAlunoRequest extends FormRequest
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
            'aluno_id' => 'required|exists:alunos,id'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'aluno_id.required' => 'Aluno é obrigatório.',
            'aluno_id.exists' => 'Aluno selecionado não existe.'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateAlunoVinculado($validator);
        });
    }

    /**
     * Valida se o aluno está vinculado à turma.
     */
    private function validateAlunoVinculado($validator): void
    {
        if (!$this->aluno_id) {
            return;
        }

        $turma = $this->route('turma');
        
        if (!$turma->alunos()->where('aluno_id', $this->aluno_id)->exists()) {
            $validator->errors()->add(
                'aluno_id', 
                'Este aluno não está vinculado a esta turma.'
            );
        }
    }
}
