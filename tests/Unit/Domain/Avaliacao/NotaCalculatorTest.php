<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Avaliacao;

use App\Domain\Avaliacao\NotaCalculator;
use App\Domain\Avaliacao\NotaFinal;
use App\Enums\SituacaoAvaliacao;
use PHPUnit\Framework\TestCase;

class NotaCalculatorTest extends TestCase
{
    private NotaCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new NotaCalculator;
    }

    public function test_media_simples_sem_substitutiva_nem_recuperacao(): void
    {
        $resultado = $this->calculator->calcular(av1: 8, av2: 6, av3: 7, av4: 7);

        $this->assertInstanceOf(NotaFinal::class, $resultado);
        $this->assertEqualsWithDelta(7.0, $resultado->media, 0.001);
    }

    public function test_situacao_aprovado_quando_media_igual_a_6(): void
    {
        $resultado = $this->calculator->calcular(av1: 6, av2: 6, av3: 6, av4: 6);

        $this->assertSame(SituacaoAvaliacao::Aprovado, $resultado->situacao);
    }

    public function test_situacao_aprovado_quando_media_acima_de_6(): void
    {
        $resultado = $this->calculator->calcular(av1: 9, av2: 8, av3: 7, av4: 8);

        $this->assertSame(SituacaoAvaliacao::Aprovado, $resultado->situacao);
    }

    public function test_situacao_reprovado_quando_media_abaixo_de_6(): void
    {
        $resultado = $this->calculator->calcular(av1: 5, av2: 4, av3: 3, av4: 6);

        $this->assertSame(SituacaoAvaliacao::Reprovado, $resultado->situacao);
    }

    public function test_situacao_em_andamento_quando_todas_notas_zeradas(): void
    {
        $resultado = $this->calculator->calcular(av1: 0, av2: 0, av3: 0, av4: 0);

        $this->assertSame(SituacaoAvaliacao::EmAndamento, $resultado->situacao);
        $this->assertEqualsWithDelta(0.0, $resultado->media, 0.001);
    }

    public function test_substitutiva_substitui_a_menor_nota(): void
    {
        $resultado = $this->calculator->calcular(
            av1: 8,
            av2: 6,
            av3: 4,
            av4: 7,
            substitutiva: 9,
        );

        // menor (4) substituída por 9, media = (8+6+9+7)/4 = 7.5
        $this->assertEqualsWithDelta(7.5, $resultado->media, 0.001);
    }

    public function test_substitutiva_nao_substitui_se_menor_for_maior_que_substitutiva(): void
    {
        // Regra de negócio: substitutiva só beneficia. Se menor (5) > substitutiva (3),
        // mantém menor.
        $resultado = $this->calculator->calcular(
            av1: 7,
            av2: 6,
            av3: 5,
            av4: 8,
            substitutiva: 3,
        );

        $this->assertEqualsWithDelta(6.5, $resultado->media, 0.001);
    }

    public function test_recuperacao_substitui_menor_apos_substitutiva(): void
    {
        $resultado = $this->calculator->calcular(
            av1: 8,
            av2: 6,
            av3: 4,
            av4: 7,
            substitutiva: 9,
            recuperacaoFinal: 10,
        );

        // Após substitutiva: [8,6,9,7]. menor agora é 6 → vira 10 → [8,10,9,7]
        // media = 8.5
        $this->assertEqualsWithDelta(8.5, $resultado->media, 0.001);
    }

    public function test_recuperacao_aplicada_sozinha_substitui_menor_nota(): void
    {
        $resultado = $this->calculator->calcular(
            av1: 7,
            av2: 4,
            av3: 6,
            av4: 5,
            recuperacaoFinal: 9,
        );

        // menor (4) → 9 ; media = (7+9+6+5)/4 = 6.75
        $this->assertEqualsWithDelta(6.75, $resultado->media, 0.001);
    }

    public function test_notas_iguais_substitutiva_substitui_primeira_ocorrencia(): void
    {
        // Comportamento documentado: array_search retorna primeira ocorrência.
        $resultado = $this->calculator->calcular(
            av1: 5,
            av2: 5,
            av3: 5,
            av4: 5,
            substitutiva: 10,
        );

        // [10,5,5,5] → media = 6.25
        $this->assertEqualsWithDelta(6.25, $resultado->media, 0.001);
    }

    public function test_situacao_em_andamento_apenas_quando_todas_notas_sao_zero(): void
    {
        $resultado = $this->calculator->calcular(av1: 0, av2: 0, av3: 1, av4: 0);

        // Tem ao menos uma nota lançada → não está mais "em andamento"
        $this->assertNotSame(SituacaoAvaliacao::EmAndamento, $resultado->situacao);
    }

    public function test_notas_null_sao_tratadas_como_zero(): void
    {
        $resultado = $this->calculator->calcular(av1: 8, av2: null, av3: 6, av4: null);

        // [8,0,6,0] → media = 3.5 → reprovado
        $this->assertEqualsWithDelta(3.5, $resultado->media, 0.001);
        $this->assertSame(SituacaoAvaliacao::Reprovado, $resultado->situacao);
    }

    public function test_nota_final_retorna_value_object_imutavel(): void
    {
        $resultado = $this->calculator->calcular(av1: 7, av2: 7, av3: 7, av4: 7);

        $this->assertInstanceOf(NotaFinal::class, $resultado);
        $this->assertTrue((new \ReflectionClass(NotaFinal::class))->isReadOnly());
    }
}
