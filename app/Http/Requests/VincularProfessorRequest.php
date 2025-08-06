<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Professor;

class VincularProfessorRequest extends FormRequest
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
            'professor_id.required' => 'Selecione um professor.',
            'professor_id.exists' => 'Professor selecionado não existe.',
            'disciplina_id.required' => 'Selecione uma disciplina.',
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
            $this->validateProfessorHasDisciplina($validator);
            $this->validateVinculoNaoExiste($validator);
        });
    }

    /**
     * Valida se o professor possui a disciplina selecionada.
     */
    private function validateProfessorHasDisciplina($validator): void
    {
        if (!$this->professor_id || !$this->disciplina_id) {
            return;
        }

        $professor = Professor::with('disciplinas')->find($this->professor_id);
        
        if ($professor && !$professor->disciplinas->contains($this->disciplina_id)) {
            $validator->errors()->add(
                'disciplina_id', 
                'O professor selecionado não possui a disciplina escolhida.'
            );
        }
    }

    /**
     * Valida se o vínculo já existe.
     */
    private function validateVinculoNaoExiste($validator): void
    {
        if (!$this->professor_id || !$this->disciplina_id) {
            return;
        }

        $turma = $this->route('turma');
        
        $existeVinculo = $turma->professores()
            ->where('professor_id', $this->professor_id)
            ->wherePivot('disciplina_id', $this->disciplina_id)
            ->exists();
            
        if ($existeVinculo) {
            $validator->errors()->add(
                'vinculo_existente', 
                'Este professor já está vinculado a esta disciplina nesta turma.'
            );
        }
    }
}
