<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Aluno;

class StoreChamadaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Verificar se o usuário é admin ou professor vinculado
        if (auth()->user()->hasRole('admin')) {
            return true;
        }
        
        if (auth()->user()->hasRole('professor')) {
            return $this->professorTemVinculo();
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'turma_id' => 'required|exists:turmas,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'professor_id' => 'required|exists:professores,id',
            'data_chamada' => 'required|date|before_or_equal:today',
            'presencas' => 'array',
            'presencas.*' => 'string|exists:alunos,numero_matricula',
            'confirmar_reenvio' => 'sometimes|boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'turma_id.required' => 'A turma é obrigatória.',
            'turma_id.exists' => 'A turma selecionada não existe.',
            'disciplina_id.required' => 'A disciplina é obrigatória.',
            'disciplina_id.exists' => 'A disciplina selecionada não existe.',
            'professor_id.required' => 'O professor é obrigatório.',
            'professor_id.exists' => 'O professor selecionado não existe.',
            'data_chamada.required' => 'A data da chamada é obrigatória.',
            'data_chamada.date' => 'A data da chamada deve ser uma data válida.',
            'data_chamada.before_or_equal' => 'A data da chamada não pode ser futura.',
            'presencas.array' => 'As presenças devem ser um array.',
            'presencas.*.string' => 'Cada presença deve ser uma string.',
            'presencas.*.exists' => 'Uma ou mais matrículas não existem.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->hasValidationErrors($validator)) {
                return;
            }
            
            $this->validarAlunosPertencemTurma($validator);
            $this->validarVinculoProfessorTurmaDisciplina($validator);
        });
    }

    // Métodos privados seguindo Object Calisthenics
    
    private function professorTemVinculo(): bool
    {
        $professor = auth()->user()->professor;
        
        if (!$professor) {
            return false;
        }
        
        return DB::table('professor_disciplina_turma')
            ->where('professor_id', $professor->id)
            ->where('turma_id', $this->turma_id)
            ->where('disciplina_id', $this->disciplina_id)
            ->exists();
    }

    private function hasValidationErrors($validator): bool
    {
        return !$this->turma_id || !$this->disciplina_id || !$this->professor_id;
    }

    private function validarAlunosPertencemTurma($validator): void
    {
        if (!$this->presencas) {
            return;
        }
        
        $matriculasValidas = Aluno::where('turma_id', $this->turma_id)
            ->where('status_matricula', 'ativa')
            ->pluck('numero_matricula')
            ->toArray();
        
        $matriculasInvalidas = array_diff($this->presencas, $matriculasValidas);
        
        if (!empty($matriculasInvalidas)) {
            $validator->errors()->add(
                'presencas', 
                'Algumas matrículas não pertencem à turma selecionada ou estão inativas.'
            );
        }
    }

    private function validarVinculoProfessorTurmaDisciplina($validator): void
    {
        $vinculoExiste = DB::table('professor_disciplina_turma')
            ->where('professor_id', $this->professor_id)
            ->where('turma_id', $this->turma_id)
            ->where('disciplina_id', $this->disciplina_id)
            ->exists();
        
        if (!$vinculoExiste) {
            $validator->errors()->add(
                'professor_id', 
                'O professor não está vinculado a esta turma e disciplina.'
            );
        }
    }
}