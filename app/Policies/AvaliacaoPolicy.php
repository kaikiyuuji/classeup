<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Avaliacao;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AvaliacaoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProfessor() || $user->isAluno();
    }

    public function view(User $user, Avaliacao $avaliacao): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAluno()) {
            return $user->aluno_id === $avaliacao->aluno_id;
        }

        if ($user->isProfessor() && $user->professor_id !== null) {
            return $this->professorLecionaDisciplinaParaAluno(
                $user->professor_id,
                $avaliacao->aluno_id,
                $avaliacao->disciplina_id,
            );
        }

        return false;
    }

    public function update(User $user, Avaliacao $avaliacao): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isProfessor() && $user->professor_id !== null) {
            return $this->professorLecionaDisciplinaParaAluno(
                $user->professor_id,
                $avaliacao->aluno_id,
                $avaliacao->disciplina_id,
            );
        }

        return false;
    }

    private function professorLecionaDisciplinaParaAluno(int $professorId, int $alunoId, int $disciplinaId): bool
    {
        $turmaId = DB::table('alunos')->where('id', $alunoId)->value('turma_id');

        if ($turmaId === null) {
            return false;
        }

        return DB::table('professor_disciplina_turma')
            ->where('professor_id', $professorId)
            ->where('disciplina_id', $disciplinaId)
            ->where('turma_id', $turmaId)
            ->exists();
    }
}
