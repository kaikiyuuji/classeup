<?php

declare(strict_types=1);

namespace Tests\Feature\E2E;

use App\Models\Aluno;
use App\Models\Falta;
use Tests\TestCase;

/**
 * Fluxo completo de chamada:
 * 1. Admin/professor registra chamada → faltas criadas.
 * 2. Aluno consulta próprias faltas via matrícula.
 * 3. Admin/professor justifica falta.
 * 4. Admin/professor remove justificativa.
 * 5. Re-submeter chamada do mesmo dia substitui as faltas anteriores (idempotência).
 */
class FluxoChamadaTest extends TestCase
{
    public function test_admin_registra_chamada_e_falta_e_persistida_com_aluno_id(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $a1 = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);
        $a2 = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);

        $this->actingAsAdmin();

        $response = $this->post(route('faltas.store'), [
            'turma_id' => $cenario['turma']->id,
            'disciplina_id' => $cenario['disciplina']->id,
            'professor_id' => $cenario['professor']->id,
            'data_falta' => '2026-05-15',
            'faltas' => [$a1->numero_matricula, $a2->numero_matricula],
        ]);

        $response->assertRedirect(route('faltas.index'));
        $response->assertSessionHas('success');

        $this->assertSame(2, Falta::count());
        $faltaA1 = Falta::where('matricula', $a1->numero_matricula)->firstOrFail();
        $this->assertSame($a1->id, $faltaA1->aluno_id, 'aluno_id FK deve ser populada');
        $this->assertSame($cenario['disciplina']->id, $faltaA1->disciplina_id);
    }

    public function test_reenvio_substitui_chamada_quando_confirmado(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $a1 = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);
        $a2 = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);

        $this->actingAsAdmin();

        $payload = [
            'turma_id' => $cenario['turma']->id,
            'disciplina_id' => $cenario['disciplina']->id,
            'professor_id' => $cenario['professor']->id,
            'data_falta' => '2026-05-15',
        ];

        $this->post(route('faltas.store'), $payload + ['faltas' => [$a1->numero_matricula]]);
        $this->assertSame(1, Falta::count());

        // Reenvio sem confirmação → mostra warning
        $aviso = $this->post(route('faltas.store'), $payload + [
            'faltas' => [$a2->numero_matricula],
        ]);
        $aviso->assertSessionHas('warning');
        $this->assertSame(1, Falta::count(), 'sem confirmação, mantém estado anterior');

        // Reenvio confirmado → substitui
        $this->post(route('faltas.store'), $payload + [
            'faltas' => [$a2->numero_matricula],
            'confirmar_reenvio' => '1',
        ])->assertRedirect();

        $this->assertSame(1, Falta::count());
        $this->assertSame($a2->numero_matricula, Falta::first()?->matricula);
    }

    public function test_justificativa_e_remocao_via_rotas(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $aluno = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);
        $falta = Falta::factory()->create([
            'aluno_id' => $aluno->id,
            'matricula' => $aluno->numero_matricula,
            'disciplina_id' => $cenario['disciplina']->id,
            'professor_id' => $cenario['professor']->id,
            'justificada' => false,
        ]);

        $this->actingAsAdmin();

        // Justificar
        $this->post(route('faltas.processar-justificativa', $falta), [
            'observacoes' => 'Atestado médico anexo',
        ])->assertRedirect();

        $falta->refresh();
        $this->assertTrue($falta->estaJustificada());
        $this->assertSame('Atestado médico anexo', $falta->observacoes);

        // Remover justificativa
        $this->delete(route('faltas.remover-justificativa', $falta))->assertRedirect();

        $falta->refresh();
        $this->assertFalse($falta->estaJustificada());
        $this->assertNull($falta->observacoes);
    }

    public function test_aluno_nao_pode_registrar_chamada(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $aluno = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);
        $this->actingAsAluno($aluno);

        $this->post(route('faltas.store'), [
            'turma_id' => $cenario['turma']->id,
            'disciplina_id' => $cenario['disciplina']->id,
            'professor_id' => $cenario['professor']->id,
            'data_falta' => '2026-05-15',
            'faltas' => [$aluno->numero_matricula],
        ])->assertForbidden();

        $this->assertSame(0, Falta::count());
    }
}
