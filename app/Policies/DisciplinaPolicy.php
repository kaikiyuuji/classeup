<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Disciplina;
use App\Models\User;

class DisciplinaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProfessor();
    }

    public function view(User $user, Disciplina $disciplina): bool
    {
        return $user->isAdmin() || $user->isProfessor();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Disciplina $disciplina): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Disciplina $disciplina): bool
    {
        return $user->isAdmin();
    }
}
