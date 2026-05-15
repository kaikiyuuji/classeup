<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Professor;
use App\Models\User;

class ProfessorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProfessor();
    }

    public function view(User $user, Professor $professor): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isProfessor() && $user->professor_id === $professor->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Professor $professor): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Professor $professor): bool
    {
        return $user->isAdmin();
    }
}
