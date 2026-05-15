<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Falta;
use App\Models\User;

class FaltaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProfessor() || $user->isAluno();
    }

    public function view(User $user, Falta $falta): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAluno()) {
            return $user->aluno_id !== null && $user->aluno_id === $falta->aluno_id;
        }

        if ($user->isProfessor() && $user->professor_id !== null) {
            return $user->professor_id === $falta->professor_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isProfessor();
    }

    public function update(User $user, Falta $falta): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isProfessor() && $user->professor_id !== null) {
            return $user->professor_id === $falta->professor_id && $falta->podeSerEditada();
        }

        return false;
    }

    public function delete(User $user, Falta $falta): bool
    {
        return $user->isAdmin();
    }

    public function justificar(User $user, Falta $falta): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isProfessor() && $user->professor_id !== null) {
            return $user->professor_id === $falta->professor_id;
        }

        if ($user->isAluno() && $user->aluno_id !== null) {
            return $user->aluno_id === $falta->aluno_id;
        }

        return false;
    }
}
