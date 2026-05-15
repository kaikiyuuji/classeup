<?php

declare(strict_types=1);

namespace Tests\Feature\E2E;

use App\Enums\SituacaoAvaliacao;
use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Models\Turma;
use Tests\TestCase;

/**
 * Fluxo completo de boletim:
 * 1. Admin cria turma, professor, disciplina.
 * 2. Admin vincula professor+disciplina à turma (pivot ternária).
 * 3. Admin matricula aluno na turma.
 * 4. Admin/professor acessa boletim → service garante Avaliacao existente
 *    automaticamente para cada disciplina da turma.
 * 5. Professor lança notas via rota → NotaCalculator computa média e situação.
 * 6. Aluno vê apenas o próprio boletim com a situação correta.
 */
class FluxoBoletimTest extends TestCase
{
    public function test_admin_cria_avaliacao_e_lanca_notas_aluno_ve_boletim(): void
    {
        // 1) Setup como admin
        $admin = $this->actingAsAdmin();

        $turma = Turma::factory()->create();
        $professor = Professor::factory()->create();
        $disciplina = Disciplina::factory()->create();
        $turma->professores()->attach($professor->id, ['disciplina_id' => $disciplina->id]);
        $aluno = Aluno::factory()->create(['turma_id' => $turma->id]);

        // 2) Admin abre o boletim — AvaliacaoService garante a Avaliacao existir
        $this->get(route('alunos.boletim', $aluno))->assertOk();
        $avaliacao = Avaliacao::where('aluno_id', $aluno->id)
            ->where('disciplina_id', $disciplina->id)
            ->firstOrFail();

        // 3) Professor (com vínculo ternária) lança notas
        $this->actingAsProfessor($professor);
        $response = $this->put(route('alunos.avaliacoes.update', [$aluno, $avaliacao]), [
            'av1' => 8,
            'av2' => 7,
            'av3' => 9,
            'av4' => 8,
        ]);
        $response->assertRedirect(route('alunos.boletim', $aluno));

        $avaliacao->refresh();
        $this->assertEqualsWithDelta(8.0, (float) $avaliacao->nota_final, 0.01);
        $this->assertSame(SituacaoAvaliacao::Aprovado, $avaliacao->situacao);

        // 4) Aluno consulta o próprio boletim
        $this->actingAsAluno($aluno);
        $resp = $this->get(route('alunos.boletim', $aluno));
        $resp->assertOk();
        $resp->assertSeeText('Aprovado');
    }

    public function test_substitutiva_recupera_aluno_no_boletim(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $aluno = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);
        $avaliacao = Avaliacao::factory()->create([
            'aluno_id' => $aluno->id,
            'disciplina_id' => $cenario['disciplina']->id,
            'av1' => 8,
            'av2' => 7,
            'av3' => 3,
            'av4' => 7,
        ]);

        $this->actingAsProfessor($cenario['professor']);

        $this->put(route('alunos.avaliacoes.update', [$aluno, $avaliacao]), [
            'av1' => 8,
            'av2' => 7,
            'av3' => 3,
            'av4' => 7,
            'substitutiva' => 9,
        ])->assertRedirect();

        $avaliacao->refresh();
        // [8,7,9,7] → 7.75 → Aprovado
        $this->assertEqualsWithDelta(7.75, (float) $avaliacao->nota_final, 0.01);
        $this->assertSame(SituacaoAvaliacao::Aprovado, $avaliacao->situacao);
    }

    public function test_aluno_nao_acessa_boletim_de_outro_aluno(): void
    {
        $turma = Turma::factory()->create();
        $a1 = Aluno::factory()->create(['turma_id' => $turma->id]);
        $a2 = Aluno::factory()->create(['turma_id' => $turma->id]);

        $this->actingAsAluno($a1);

        $this->get(route('alunos.boletim', $a2))->assertForbidden();
    }

    public function test_professor_de_outra_turma_nao_pode_atualizar_avaliacao(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $outroProfessor = Professor::factory()->create();
        $aluno = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);

        $avaliacao = Avaliacao::factory()->create([
            'aluno_id' => $aluno->id,
            'disciplina_id' => $cenario['disciplina']->id,
        ]);

        $this->actingAsProfessor($outroProfessor);

        $this->put(route('alunos.avaliacoes.update', [$aluno, $avaliacao]), [
            'av1' => 10, 'av2' => 10, 'av3' => 10, 'av4' => 10,
        ])->assertForbidden();

        $avaliacao->refresh();
        $this->assertNotSame(SituacaoAvaliacao::Aprovado, $avaliacao->situacao);
    }
}
