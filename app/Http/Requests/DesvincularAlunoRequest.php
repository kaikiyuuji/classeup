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
            // Não há campos no request, validação é feita via route model binding
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
            // Mensagens customizadas para validação via route model binding
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
        $turma = $this->route('turma');
        $aluno = $this->route('aluno');
        
        if (!$aluno || !$turma) {
            return;
        }
        
        if ($aluno->turma_id !== $turma->id) {
            $validator->errors()->add(
                'aluno', 
                'Este aluno não está vinculado a esta turma.'
            );
        }
    }
}
