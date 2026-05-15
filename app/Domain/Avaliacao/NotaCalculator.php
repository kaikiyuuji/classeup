<?php

declare(strict_types=1);

namespace App\Domain\Avaliacao;

use App\Enums\SituacaoAvaliacao;

/**
 * Calcula a nota final de uma avaliação aplicando regras de substitutiva
 * e recuperação final.
 *
 * Regras:
 * - 4 notas periódicas (av1..av4); valores null são tratados como 0.
 * - Substitutiva, se fornecida e maior que a menor nota, substitui a menor.
 * - Recuperação final, se fornecida e maior que a menor nota (após substitutiva),
 *   substitui a menor nota.
 * - Situação "em_andamento" só vale quando nenhuma nota foi lançada
 *   (todas as notas periódicas são exatamente 0 ou null, e não houve
 *   substitutiva nem recuperação).
 */
final class NotaCalculator
{
    public function calcular(
        float|int|string|null $av1,
        float|int|string|null $av2,
        float|int|string|null $av3,
        float|int|string|null $av4,
        float|int|string|null $substitutiva = null,
        float|int|string|null $recuperacaoFinal = null,
    ): NotaFinal {
        $notas = [
            (float) ($av1 ?? 0),
            (float) ($av2 ?? 0),
            (float) ($av3 ?? 0),
            (float) ($av4 ?? 0),
        ];

        $temNotasLancadas = $this->algumaNotaLancada($notas, $substitutiva, $recuperacaoFinal);

        if ($substitutiva !== null) {
            $notas = $this->aplicarSubstituicaoSeBeneficiar($notas, (float) $substitutiva);
        }

        if ($recuperacaoFinal !== null) {
            $notas = $this->aplicarSubstituicaoSeBeneficiar($notas, (float) $recuperacaoFinal);
        }

        $media = array_sum($notas) / count($notas);
        $situacao = SituacaoAvaliacao::partir($media, $temNotasLancadas);

        return new NotaFinal(media: $media, situacao: $situacao);
    }

    /**
     * @param array<int, float> $notas
     */
    private function algumaNotaLancada(array $notas, float|int|string|null $substitutiva, float|int|string|null $recuperacao): bool
    {
        foreach ($notas as $nota) {
            if ($nota > 0) {
                return true;
            }
        }

        return $substitutiva !== null || $recuperacao !== null;
    }

    /**
     * @param  array<int, float> $notas
     * @return array<int, float>
     */
    private function aplicarSubstituicaoSeBeneficiar(array $notas, float $valor): array
    {
        $indiceMenor = $this->indiceDaMenorNota($notas);

        if ($valor > $notas[$indiceMenor]) {
            $notas[$indiceMenor] = $valor;
        }

        return $notas;
    }

    /**
     * @param array<int, float> $notas
     */
    private function indiceDaMenorNota(array $notas): int
    {
        $indice = array_search(min($notas), $notas, true);

        return is_int($indice) ? $indice : 0;
    }
}
