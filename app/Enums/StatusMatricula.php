<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusMatricula: string
{
    case Ativa = 'ativa';
    case Inativa = 'inativa';
    case Trancada = 'trancada';
    case Transferida = 'transferida';

    public function label(): string
    {
        return match ($this) {
            self::Ativa => 'Ativa',
            self::Inativa => 'Inativa',
            self::Trancada => 'Trancada',
            self::Transferida => 'Transferida',
        };
    }

    public function ativa(): bool
    {
        return $this === self::Ativa;
    }
}
