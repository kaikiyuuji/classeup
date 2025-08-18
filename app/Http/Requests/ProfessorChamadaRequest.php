<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Professor;

class ProfessorChamadaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->validarVinculoProfessorTurmaDisciplina();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'turma_id' => 'required|exists:turmas,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'data_chamada' => 'required|date|before_or_equal:today',
            'presencas' => 'array',
            'presencas.*' => 'string',
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
            'data_chamada.required' => 'A data da chamada é obrigatória.',
            'data_chamada.date' => 'A data da chamada deve ser uma data válida.',
            'data_chamada.before_or_equal' => 'A data da chamada não pode ser futura.',
        ];
    }

    /**
     * Validar se o professor está vinculado à turma e disciplina
     */
    private function validarVinculoProfessorTurmaDisciplina(): bool
    {
        $professor = $this->obterProfessorAutenticado();
        
        if (!$professor) {
            return false;
        }
        
        return $this->verificarVinculoExiste($professor);
    }
    
    /**
     * Obter professor autenticado
     */
    private function obterProfessorAutenticado(): ?Professor
    {
        $user = auth()->user();
        
        return $user?->professor;
    }
    
    /**
     * Verificar se o vínculo existe usando relacionamentos Eloquent
     */
    private function verificarVinculoExiste(Professor $professor): bool
    {
        return $professor->disciplinasComTurma()
            ->where('professor_disciplina_turma.turma_id', $this->input('turma_id'))
            ->where('disciplinas.id', $this->input('disciplina_id'))
            ->exists();
    }
    
    /**
     * Get the error messages for the defined validation rules.
     */
    public function failedAuthorization(): void
    {
        abort(403, 'Você não tem permissão para fazer chamada nesta turma/disciplina.');
    }
}