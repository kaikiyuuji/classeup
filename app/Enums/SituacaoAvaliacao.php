<?php

declare(strict_types=1);

namespace App\Enums;

enum SituacaoAvaliacao: string
{
    case EmAndamento = 'em_andamento';
    case Aprovado = 'aprovado';
    case Reprovado = 'reprovado';

    public const MEDIA_APROVACAO = 6.0;

    public static function partir(float $media, bool $temNotasLancadas): self
    {
        if (! $temNotasLancadas) {
            return self::EmAndamento;
        }

        return $media >= self::MEDIA_APROVACAO ? self::Aprovado : self::Reprovado;
    }

    public function label(): string
    {
        return match ($this) {
            self::EmAndamento => 'Em andamento',
            self::Aprovado => 'Aprovado',
            self::Reprovado => 'Reprovado',
        };
    }
}
