<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\StatusMatricula;
use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Models\Turma;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RelacionamentosTest extends TestCase
{
    /**
     * Testa o relacionamento Professor-Disciplina-Turma
     */
    public function test_professor_disciplina_turma_relationship(): void
    {
        // Criar dados de teste
        $professor = Professor::factory()->create();
        $disciplina = Disciplina::factory()->create();
        $turma = Turma::factory()->create();

        // Criar associação na tabela pivot direta professor-disciplina
        DB::table('professor_disciplina')->insert([
            'professor_id' => $professor->id,
            'disciplina_id' => $disciplina->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar associação na tabela pivot tripla professor-disciplina-turma
        DB::table('professor_disciplina_turma')->insert([
            'professor_id' => $professor->id,
            'disciplina_id' => $disciplina->id,
            'turma_id' => $turma->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Testar relacionamentos diretos (professor_disciplina)
        $this->assertTrue($professor->disciplinas->contains($disciplina));
        $this->assertTrue($disciplina->professores->contains($professor));

        // Testar relacionamentos através da tabela tripla (professor_disciplina_turma)
        $this->assertTrue($professor->turmas->contains($turma));
        $this->assertTrue($disciplina->turmas->contains($turma));
        $this->assertTrue($turma->professores->contains($professor));
        $this->assertTrue($turma->disciplinas->contains($disciplina));
    }

    /**
     * Testa o relacionamento Aluno-Turma (one-to-many)
     */
    public function test_aluno_turma_relationship(): void
    {
        // Criar dados de teste
        $turma = Turma::factory()->create();
        $aluno = Aluno::factory()->create(['turma_id' => $turma->id]);

        // Testar relacionamentos
        $this->assertEquals($turma->id, $aluno->turma_id);
        $this->assertEquals($turma->id, $aluno->turma->id);
        $this->assertTrue($turma->alunos->contains($aluno));
    }

    /**
     * Testa a constraint de unicidade na tabela professor_disciplina_turma
     */
    public function test_professor_disciplina_turma_unique_constraint(): void
    {
        $professor = Professor::factory()->create();
        $disciplina = Disciplina::factory()->create();
        $turma = Turma::factory()->create();

        // Primeira inserção deve funcionar
        DB::table('professor_disciplina_turma')->insert([
            'professor_id' => $professor->id,
            'disciplina_id' => $disciplina->id,
            'turma_id' => $turma->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Segunda inserção com os mesmos dados deve falhar
        $this->expectException(QueryException::class);
        DB::table('professor_disciplina_turma')->insert([
            'professor_id' => $professor->id,
            'disciplina_id' => $disciplina->id,
            'turma_id' => $turma->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Testa os métodos de matrícula do modelo Aluno
     */
    public function test_aluno_matricula_methods(): void
    {
        $aluno = Aluno::factory()->matriculaAtiva()->create();

        // Testar se a matrícula está ativa
        $this->assertTrue($aluno->isMatriculaAtiva());

        // Inativar matrícula
        $aluno->inativarMatricula();
        $aluno->refresh();

        $this->assertFalse($aluno->isMatriculaAtiva());
        $this->assertSame(StatusMatricula::Inativa, $aluno->status_matricula);
        $this->assertFalse($aluno->isAtivo());

        // Ativar matrícula novamente
        $aluno->ativarMatricula();
        $aluno->refresh();

        $this->assertTrue($aluno->isMatriculaAtiva());
        $this->assertSame(StatusMatricula::Ativa, $aluno->status_matricula);
        $this->assertTrue($aluno->isAtivo());
    }

    /**
     * Testa a geração de número de matrícula
     */
    public function test_gerar_numero_matricula(): void
    {
        $ano = date('Y');

        // Primeiro número de matrícula do ano
        $numeroMatricula1 = Aluno::gerarNumeroMatricula($ano);
        $this->assertEquals($ano.'0001', $numeroMatricula1);

        // Criar um aluno com esse número
        Aluno::factory()->create(['numero_matricula' => $numeroMatricula1]);

        // Próximo número deve ser incrementado
        $numeroMatricula2 = Aluno::gerarNumeroMatricula($ano);
        $this->assertEquals($ano.'0002', $numeroMatricula2);
    }
}
