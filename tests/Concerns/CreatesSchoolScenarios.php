<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\User;

trait CreatesSchoolScenarios
{
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function actingAsAdmin(array $attributes = []): User
    {
        $user = $this->createUser($attributes);
        $this->actingAs($user);

        return $user;
    }

    protected function actingAsProfessor(?Professor $professor = null, array $attributes = []): User
    {
        $professor ??= Professor::factory()->create();
        $user = $this->createUser($attributes);
        $this->actingAs($user);

        return $user;
    }

    protected function actingAsAluno(?Aluno $aluno = null, array $attributes = []): User
    {
        $aluno ??= Aluno::factory()->create();
        $user = $this->createUser($attributes);
        $this->actingAs($user);

        return $user;
    }

    /**
     * @return array{turma: Turma, professor: Professor, disciplina: Disciplina}
     */
    protected function createTurmaComProfessorDisciplina(): array
    {
        $turma = Turma::factory()->create();
        $professor = Professor::factory()->create();
        $disciplina = Disciplina::factory()->create();

        $turma->professores()->attach($professor->id, ['disciplina_id' => $disciplina->id]);

        return [
            'turma' => $turma,
            'professor' => $professor,
            'disciplina' => $disciplina,
        ];
    }
}
