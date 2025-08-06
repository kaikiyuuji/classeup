<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Turma;

class VincularAlunosRequest extends FormRequest
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
            'alunos' => 'required|array|min:1',
            'alunos.*' => 'exists:alunos,id'
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
            'alunos.required' => 'Selecione pelo menos um aluno.',
            'alunos.array' => 'Formato inválido para alunos.',
            'alunos.min' => 'Selecione pelo menos um aluno.',
            'alunos.*.exists' => 'Um ou mais alunos selecionados não existem.'
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
            $this->validateCapacidadeTurma($validator);
        });
    }

    /**
     * Valida se a turma tem capacidade para os novos alunos.
     */
    private function validateCapacidadeTurma($validator): void
    {
        if (!$this->alunos) {
            return;
        }

        $turma = $this->route('turma');
        $alunosAtivos = $turma->alunos()->count();
        $novosAlunos = count($this->alunos);
        $totalAposVinculacao = $alunosAtivos + $novosAlunos;
        
        if ($totalAposVinculacao > $turma->capacidade) {
            $vagasDisponiveis = $turma->capacidade - $alunosAtivos;
            $validator->errors()->add(
                'capacidade', 
                "A turma não tem capacidade suficiente. Vagas disponíveis: {$vagasDisponiveis}. Alunos selecionados: {$novosAlunos}."
            );
        }
    }
}
