<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Disciplina;
use App\Models\Falta;
use App\Models\Professor;
use App\Models\Turma;
use Tests\TestCase;

class PoliciesMatrixTest extends TestCase
{
    public function test_admin_pode_tudo_em_professor(): void
    {
        $admin = $this->actingAsAdmin();
        $professor = Professor::factory()->create();

        $this->assertTrue($admin->can('viewAny', Professor::class));
        $this->assertTrue($admin->can('view', $professor));
        $this->assertTrue($admin->can('create', Professor::class));
        $this->assertTrue($admin->can('update', $professor));
        $this->assertTrue($admin->can('delete', $professor));
    }

    public function test_professor_so_ve_proprio_perfil_de_professor(): void
    {
        $professor = Professor::factory()->create();
        $outro = Professor::factory()->create();
        $user = $this->actingAsProfessor($professor);

        $this->assertTrue($user->can('view', $professor));
        $this->assertFalse($user->can('view', $outro));
        $this->assertFalse($user->can('update', $professor));
        $this->assertFalse($user->can('delete', $professor));
    }

    public function test_aluno_nao_acessa_professor(): void
    {
        $user = $this->actingAsAluno();
        $professor = Professor::factory()->create();

        $this->assertFalse($user->can('viewAny', Professor::class));
        $this->assertFalse($user->can('view', $professor));
    }

    public function test_disciplina_apenas_admin_muta(): void
    {
        $disciplina = Disciplina::factory()->create();

        $admin = $this->actingAsAdmin();
        $this->assertTrue($admin->can('update', $disciplina));
        $this->assertTrue($admin->can('delete', $disciplina));

        $prof = $this->actingAsProfessor();
        $this->assertTrue($prof->can('view', $disciplina));
        $this->assertFalse($prof->can('update', $disciplina));

        $aluno = $this->actingAsAluno();
        $this->assertFalse($aluno->can('view', $disciplina));
    }

    public function test_turma_aluno_so_ve_a_propria(): void
    {
        $turma1 = Turma::factory()->create();
        $turma2 = Turma::factory()->create();
        $aluno = Aluno::factory()->create(['turma_id' => $turma1->id]);
        $user = $this->actingAsAluno($aluno);

        $this->assertTrue($user->can('view', $turma1));
        $this->assertFalse($user->can('view', $turma2));
    }

    public function test_professor_pode_atualizar_avaliacao_de_sua_turma_disciplina(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $aluno = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);
        $avaliacao = Avaliacao::factory()->create([
            'aluno_id' => $aluno->id,
            'disciplina_id' => $cenario['disciplina']->id,
        ]);

        $user = $this->actingAsProfessor($cenario['professor']);

        $this->assertTrue($user->can('update', $avaliacao));
    }

    public function test_professor_nao_pode_atualizar_avaliacao_de_outra_turma(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();
        $outroProfessor = Professor::factory()->create();
        $aluno = Aluno::factory()->create(['turma_id' => $cenario['turma']->id]);
        $avaliacao = Avaliacao::factory()->create([
            'aluno_id' => $aluno->id,
            'disciplina_id' => $cenario['disciplina']->id,
        ]);

        $user = $this->actingAsProfessor($outroProfessor);

        $this->assertFalse($user->can('update', $avaliacao));
    }

    public function test_aluno_so_ve_propria_avaliacao(): void
    {
        $aluno1 = Aluno::factory()->create();
        $aluno2 = Aluno::factory()->create();
        $disciplina = Disciplina::factory()->create();
        $av1 = Avaliacao::factory()->create(['aluno_id' => $aluno1->id, 'disciplina_id' => $disciplina->id]);
        $av2 = Avaliacao::factory()->create(['aluno_id' => $aluno2->id, 'disciplina_id' => $disciplina->id]);

        $user = $this->actingAsAluno($aluno1);

        $this->assertTrue($user->can('view', $av1));
        $this->assertFalse($user->can('view', $av2));
        $this->assertFalse($user->can('update', $av1));
    }

    public function test_falta_aluno_so_ve_propria(): void
    {
        $aluno1 = Aluno::factory()->create();
        $aluno2 = Aluno::factory()->create();
        $professor = Professor::factory()->create();
        $disciplina = Disciplina::factory()->create();

        $f1 = Falta::factory()->create([
            'aluno_id' => $aluno1->id,
            'matricula' => $aluno1->numero_matricula,
            'disciplina_id' => $disciplina->id,
            'professor_id' => $professor->id,
        ]);
        $f2 = Falta::factory()->create([
            'aluno_id' => $aluno2->id,
            'matricula' => $aluno2->numero_matricula,
            'disciplina_id' => $disciplina->id,
            'professor_id' => $professor->id,
        ]);

        $user = $this->actingAsAluno($aluno1);

        $this->assertTrue($user->can('view', $f1));
        $this->assertFalse($user->can('view', $f2));
    }
}
