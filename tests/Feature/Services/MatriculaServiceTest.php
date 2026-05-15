<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Aluno;
use App\Services\MatriculaService;
use Tests\TestCase;

class MatriculaServiceTest extends TestCase
{
    private MatriculaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MatriculaService;
    }

    public function test_primeira_matricula_do_ano_e_sequencial_1(): void
    {
        $ano = 2031;

        $numero = $this->service->gerar($ano);

        $this->assertSame('20310001', $numero);
    }

    public function test_segunda_matricula_incrementa(): void
    {
        $ano = 2032;
        Aluno::factory()->create(['numero_matricula' => "{$ano}0001"]);

        $numero = $this->service->gerar($ano);

        $this->assertSame("{$ano}0002", $numero);
    }

    public function test_sem_ano_usa_ano_atual(): void
    {
        $numero = $this->service->gerar();

        $this->assertStringStartsWith((string) date('Y'), $numero);
        $this->assertSame(8, strlen($numero));
    }

    public function test_ano_diferente_nao_interfere_com_sequencial(): void
    {
        Aluno::factory()->create(['numero_matricula' => '20300999']);

        $numero = $this->service->gerar(2031);

        $this->assertSame('20310001', $numero);
    }

    public function test_formato_sequencial_e_padded_em_4_digitos(): void
    {
        $ano = 2033;
        Aluno::factory()->create(['numero_matricula' => "{$ano}0099"]);

        $numero = $this->service->gerar($ano);

        $this->assertSame("{$ano}0100", $numero);
    }
}
