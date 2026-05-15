<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use App\Models\Aluno;
use Tests\TestCase;

class AlunoPolicyTest extends TestCase
{
    public function test_admin_pode_listar_alunos(): void
    {
        $this->actingAsAdmin();

        $this->get(route('alunos.index'))->assertOk();
    }

    public function test_professor_pode_listar_alunos(): void
    {
        $this->actingAsProfessor();

        $this->get(route('alunos.index'))->assertOk();
    }

    public function test_aluno_nao_pode_listar_alunos(): void
    {
        $this->actingAsAluno();

        $this->get(route('alunos.index'))->assertForbidden();
    }

    public function test_admin_pode_criar_aluno(): void
    {
        $admin = $this->actingAsAdmin();

        $this->assertTrue($admin->can('create', Aluno::class));
    }

    public function test_professor_nao_pode_criar_aluno(): void
    {
        $user = $this->actingAsProfessor();

        $this->assertFalse($user->can('create', Aluno::class));
    }

    public function test_aluno_nao_pode_criar_aluno(): void
    {
        $user = $this->actingAsAluno();

        $this->assertFalse($user->can('create', Aluno::class));
    }

    public function test_admin_pode_atualizar_qualquer_aluno(): void
    {
        $admin = $this->actingAsAdmin();
        $alvo = Aluno::factory()->create();

        $this->assertTrue($admin->can('update', $alvo));
    }

    public function test_aluno_pode_ver_o_proprio_perfil(): void
    {
        $aluno = Aluno::factory()->create();
        $user = $this->actingAsAluno($aluno);

        $this->assertTrue($user->can('view', $aluno));
    }

    public function test_aluno_nao_pode_ver_outro_aluno(): void
    {
        $aluno1 = Aluno::factory()->create();
        $aluno2 = Aluno::factory()->create();
        $user = $this->actingAsAluno($aluno1);

        $this->assertFalse($user->can('view', $aluno2));
    }

    public function test_aluno_nao_pode_atualizar_o_proprio_perfil(): void
    {
        $aluno = Aluno::factory()->create();
        $user = $this->actingAsAluno($aluno);

        $this->assertFalse($user->can('update', $aluno));
    }
}
