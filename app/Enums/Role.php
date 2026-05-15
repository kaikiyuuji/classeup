<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Professor = 'professor';
    case Aluno = 'aluno';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Professor => 'Professor',
            self::Aluno => 'Aluno',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function isProfessor(): bool
    {
        return $this === self::Professor;
    }

    public function isAluno(): bool
    {
        return $this === self::Aluno;
    }
}
