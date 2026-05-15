<?php

declare(strict_types=1);

namespace App\Domain\Avaliacao;

use App\Enums\SituacaoAvaliacao;

final readonly class NotaFinal
{
    public function __construct(
        public float $media,
        public SituacaoAvaliacao $situacao,
    ) {}
}
