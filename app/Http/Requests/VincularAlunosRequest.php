<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Turma;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class VincularAlunosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $turma = $this->route('turma');

        return $turma !== null && ($this->user()?->can('update', $turma) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'alunos' => 'required|array|min:1',
            'alunos.*' => 'exists:alunos,id',
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
            'alunos.*.exists' => 'Um ou mais alunos selecionados não existem.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator $validator
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
        if (! $this->alunos) {
            return;
        }

        $turma = $this->route('turma');
        $alunosAtivos = $turma->alunos()->count();
        $novosAlunos = count($this->alunos);
        $totalAposVinculacao = $alunosAtivos + $novosAlunos;

        if ($totalAposVinculacao > $turma->capacidade_maxima) {
            $vagasDisponiveis = $turma->capacidade_maxima - $alunosAtivos;
            $validator->errors()->add(
                'capacidade',
                "A turma não tem capacidade suficiente. Vagas disponíveis: {$vagasDisponiveis}. Alunos selecionados: {$novosAlunos}."
            );
        }
    }
}
