<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Avaliacao;

use App\Domain\Avaliacao\NotaCalculator;
use App\Enums\SituacaoAvaliacao;
use PHPUnit\Framework\TestCase;

/**
 * Edge cases adicionais do NotaCalculator que complementam o
 * NotaCalculatorTest principal.
 */
class NotaCalculatorEdgeCasesTest extends TestCase
{
    private NotaCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new NotaCalculator;
    }

    public function test_todas_notas_dez_sem_substitutiva_e_aprovado_com_media_dez(): void
    {
        $resultado = $this->calculator->calcular(av1: 10, av2: 10, av3: 10, av4: 10);

        $this->assertSame(10.0, $resultado->media);
        $this->assertSame(SituacaoAvaliacao::Aprovado, $resultado->situacao);
    }

    public function test_substitutiva_zero_nao_substitui_nota_zero(): void
    {
        // substitutiva 0 não é > menor (0), logo não substitui
        $resultado = $this->calculator->calcular(
            av1: 0, av2: 6, av3: 7, av4: 8,
            substitutiva: 0,
        );

        $this->assertEqualsWithDelta(5.25, $resultado->media, 0.01);
    }

    public function test_recuperacao_e_substitutiva_ambas_zero_nao_alteram_notas(): void
    {
        $resultado = $this->calculator->calcular(
            av1: 5, av2: 6, av3: 7, av4: 8,
            substitutiva: 0,
            recuperacaoFinal: 0,
        );

        $this->assertEqualsWithDelta(6.5, $resultado->media, 0.01);
    }

    public function test_aceita_valores_string_do_eloquent_decimal_cast(): void
    {
        // Cast decimal:2 retorna string. NotaCalculator aceita string|int|float.
        $resultado = $this->calculator->calcular(
            av1: '8.00',
            av2: '7.50',
            av3: '9.00',
            av4: '8.50',
        );

        $this->assertEqualsWithDelta(8.25, $resultado->media, 0.01);
        $this->assertSame(SituacaoAvaliacao::Aprovado, $resultado->situacao);
    }

    public function test_situacao_em_andamento_quando_sem_notas_mas_com_substitutiva(): void
    {
        // Edge case: substitutiva fornecida com notas zeradas — não é "em andamento"
        $resultado = $this->calculator->calcular(
            av1: 0, av2: 0, av3: 0, av4: 0,
            substitutiva: 5,
        );

        // Substitutiva 5 > 0, substitui primeira nota → [5,0,0,0] → media 1.25
        $this->assertEqualsWithDelta(1.25, $resultado->media, 0.01);
        $this->assertNotSame(SituacaoAvaliacao::EmAndamento, $resultado->situacao);
    }
}
