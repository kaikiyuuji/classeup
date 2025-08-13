<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class ValidateTurmaProfessorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->tipo_usuario === 'professor';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'turma_id' => ['required', 'integer', 'exists:turmas,id']
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Se turma_id vier da rota, adicionar aos dados de validação
        if ($this->route('turma_id') && !$this->has('turma_id')) {
            $this->merge([
                'turma_id' => $this->route('turma_id')
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (!$this->turmaPertenceAoProfessor()) {
                $validator->errors()->add(
                    'turma_id', 
                    'Esta turma não está vinculada ao professor logado.'
                );
            }
        });
    }

    /**
     * Verifica se a turma pertence ao professor logado
     */
    private function turmaPertenceAoProfessor(): bool
    {
        $professor = auth()->user()->professor;
        
        if (!$professor) {
            return false;
        }

        // Capturar turma_id da rota ou do input
        $turmaId = $this->route('turma_id') ?? $this->input('turma_id');
        
        return DB::table('professor_disciplina_turma')
            ->where('professor_id', $professor->id)
            ->where('turma_id', $turmaId)
            ->exists();
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'turma_id.required' => 'O ID da turma é obrigatório.',
            'turma_id.integer' => 'O ID da turma deve ser um número inteiro.',
            'turma_id.exists' => 'A turma especificada não existe.'
        ];
    }
}