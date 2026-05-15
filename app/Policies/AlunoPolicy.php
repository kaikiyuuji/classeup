<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Aluno;
use App\Models\User;

class AlunoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProfessor();
    }

    public function view(User $user, Aluno $aluno): bool
    {
        if ($user->isAdmin() || $user->isProfessor()) {
            return true;
        }

        return $user->isAluno() && $user->aluno_id === $aluno->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Aluno $aluno): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Aluno $aluno): bool
    {
        return $user->isAdmin();
    }
}
