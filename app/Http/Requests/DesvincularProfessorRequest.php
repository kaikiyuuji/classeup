<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class DesvincularProfessorRequest extends FormRequest
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
            'professor_id' => 'required|exists:professores,id',
            'disciplina_id' => 'required|exists:disciplinas,id'
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
            'professor_id.required' => 'Professor é obrigatório.',
            'professor_id.exists' => 'Professor selecionado não existe.',
            'disciplina_id.required' => 'Disciplina é obrigatória.',
            'disciplina_id.exists' => 'Disciplina selecionada não existe.'
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
            $this->validateVinculoExiste($validator);
        });
    }

    /**
     * Valida se o vínculo existe para poder desvincular.
     */
    private function validateVinculoExiste($validator): void
    {
        if (!$this->professor_id || !$this->disciplina_id) {
            return;
        }

        $turma = $this->route('turma');
        
        $vinculoExiste = $turma->professores()
            ->where('professor_id', $this->professor_id)
            ->wherePivot('disciplina_id', $this->disciplina_id)
            ->exists();
            
        if (!$vinculoExiste) {
            $validator->errors()->add(
                'erro', 
                'Este vínculo não existe.'
            );
        }
    }
}
