<?php

declare(strict_types=1);

namespace App\Enums;

enum Turno: string
{
    case Matutino = 'matutino';
    case Vespertino = 'vespertino';
    case Noturno = 'noturno';
    case Integral = 'integral';

    public function label(): string
    {
        return match ($this) {
            self::Matutino => 'Matutino',
            self::Vespertino => 'Vespertino',
            self::Noturno => 'Noturno',
            self::Integral => 'Integral',
        };
    }
}
