<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Turma;
use App\Models\User;

class TurmaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProfessor();
    }

    public function view(User $user, Turma $turma): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isProfessor() && $user->professor_id !== null) {
            return $turma->professores()->where('professores.id', $user->professor_id)->exists();
        }

        if ($user->isAluno() && $user->aluno_id !== null) {
            return $turma->alunos()->where('id', $user->aluno_id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Turma $turma): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Turma $turma): bool
    {
        return $user->isAdmin();
    }
}
