<?php

declare(strict_types=1);

namespace App\Enums;

enum NivelEducacional: string
{
    case PreEscola = 'pré-escola';
    case Fundamental = 'fundamental';
    case Medio = 'médio';

    public function label(): string
    {
        return match ($this) {
            self::PreEscola => 'Pré-escola',
            self::Fundamental => 'Fundamental',
            self::Medio => 'Médio',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
