<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Data\RegistrarChamadaData;
use App\Models\Aluno;
use App\Models\Falta;
use App\Services\ChamadaService;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class ChamadaServiceTest extends TestCase
{
    private ChamadaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ChamadaService;
    }

    public function test_registrar_cria_faltas_para_cada_matricula_ausente(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $aluno1 = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);
        $aluno2 = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);

        $data = new RegistrarChamadaData(
            turmaId: $cenario['turma']->id,
            disciplinaId: $cenario['disciplina']->id,
            professorId: $cenario['professor']->id,
            dataFalta: CarbonImmutable::parse('2026-05-01'),
            matriculasAusentes: [$aluno1->numero_matricula, $aluno2->numero_matricula],
            confirmarReenvio: false,
        );

        $this->service->registrar($data);

        $this->assertSame(2, Falta::count());
        $this->assertSame($aluno1->id, Falta::where('matricula', $aluno1->numero_matricula)->value('aluno_id'));
    }

    public function test_registrar_substitui_chamada_anterior_do_mesmo_dia(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $aluno1 = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);
        $aluno2 = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);

        $dataInicial = new RegistrarChamadaData(
            turmaId: $cenario['turma']->id,
            disciplinaId: $cenario['disciplina']->id,
            professorId: $cenario['professor']->id,
            dataFalta: CarbonImmutable::parse('2026-05-01'),
            matriculasAusentes: [$aluno1->numero_matricula],
            confirmarReenvio: false,
        );

        $this->service->registrar($dataInicial);
        $this->assertSame(1, Falta::count());

        $reenvio = new RegistrarChamadaData(
            turmaId: $cenario['turma']->id,
            disciplinaId: $cenario['disciplina']->id,
            professorId: $cenario['professor']->id,
            dataFalta: CarbonImmutable::parse('2026-05-01'),
            matriculasAusentes: [$aluno2->numero_matricula],
            confirmarReenvio: true,
        );

        $this->service->registrar($reenvio);

        $this->assertSame(1, Falta::count());
        $this->assertSame($aluno2->numero_matricula, Falta::first()?->matricula);
    }

    public function test_ja_existe_chamada_retorna_true_apos_registro(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $aluno = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);

        $data = new RegistrarChamadaData(
            turmaId: $cenario['turma']->id,
            disciplinaId: $cenario['disciplina']->id,
            professorId: $cenario['professor']->id,
            dataFalta: CarbonImmutable::parse('2026-05-01'),
            matriculasAusentes: [$aluno->numero_matricula],
            confirmarReenvio: false,
        );

        $this->service->registrar($data);

        $this->assertTrue($this->service->jaExisteChamada(
            $cenario['disciplina']->id,
            $cenario['professor']->id,
            '2026-05-01',
        ));
    }

    public function test_alunos_da_turma_filtra_por_turma_id(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $a1 = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);
        $a2 = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);
        Aluno::factory()->create(); // outra turma

        $resultado = $this->service->alunosDaTurma($cenario['turma']->id);

        $this->assertCount(2, $resultado);
        $this->assertEqualsCanonicalizing([$a1->id, $a2->id], $resultado->pluck('id')->all());
    }

    public function test_relatorio_filtra_por_periodo_e_matricula(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $aluno = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);

        Falta::factory()->create([
            'aluno_id' => $aluno->id,
            'matricula' => $aluno->numero_matricula,
            'disciplina_id' => $cenario['disciplina']->id,
            'professor_id' => $cenario['professor']->id,
            'data_falta' => '2026-04-15',
        ]);
        Falta::factory()->create([
            'aluno_id' => $aluno->id,
            'matricula' => $aluno->numero_matricula,
            'disciplina_id' => $cenario['disciplina']->id,
            'professor_id' => $cenario['professor']->id,
            'data_falta' => '2026-06-10',
        ]);

        $resultado = $this->service->relatorioPorMatricula(
            $aluno->numero_matricula,
            CarbonImmutable::parse('2026-05-01'),
            CarbonImmutable::parse('2026-07-01'),
        );

        $this->assertCount(1, $resultado);
        $this->assertSame('2026-06-10', $resultado->first()?->data_falta->toDateString());
    }
}
