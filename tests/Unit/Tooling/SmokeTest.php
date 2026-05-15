<?php

declare(strict_types=1);

namespace Tests\Unit\Tooling;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_acting_as_admin_returns_authenticated_user(): void
    {
        $user = $this->actingAsAdmin();

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->id);
    }

    public function test_acting_as_professor_returns_authenticated_user(): void
    {
        $user = $this->actingAsProfessor();

        $this->assertAuthenticatedAs($user);
    }

    public function test_acting_as_aluno_returns_authenticated_user(): void
    {
        $user = $this->actingAsAluno();

        $this->assertAuthenticatedAs($user);
    }

    public function test_create_turma_com_professor_disciplina_returns_linked_entities(): void
    {
        $cenario = $this->createTurmaComProfessorDisciplina();

        $this->assertArrayHasKey('turma', $cenario);
        $this->assertArrayHasKey('professor', $cenario);
        $this->assertArrayHasKey('disciplina', $cenario);
        $this->assertTrue(
            $cenario['turma']->professores()
                ->wherePivot('disciplina_id', $cenario['disciplina']->id)
                ->where('professores.id', $cenario['professor']->id)
                ->exists()
        );
    }
}
