<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Status genérico ativo/inativo para entidades que não precisam de mais granularidade
 * (Professor, Turma, Disciplina). Aluno usa StatusMatricula porque tem mais estados.
 */
enum Status: string
{
    case Ativo = 'ativo';
    case Inativo = 'inativo';

    public function label(): string
    {
        return $this === self::Ativo ? 'Ativo' : 'Inativo';
    }

    public function ativo(): bool
    {
        return $this === self::Ativo;
    }

    public static function fromBoolean(bool $ativo): self
    {
        return $ativo ? self::Ativo : self::Inativo;
    }
}
