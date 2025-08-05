<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Professor;
use App\Models\Disciplina;
use App\Models\Turma;
use App\Models\Aluno;
use App\Models\Matricula;
use Illuminate\Support\Facades\DB;

class RelacionamentosTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa o relacionamento Professor-Disciplina-Turma
     */
    public function test_professor_disciplina_turma_relationship(): void
    {
        // Criar dados de teste
        $professor = Professor::factory()->create();
        $disciplina = Disciplina::factory()->create();
        $turma = Turma::factory()->create();

        // Criar associação na tabela pivot
        DB::table('professor_disciplina_turma')->insert([
            'professor_id' => $professor->id,
            'disciplina_id' => $disciplina->id,
            'turma_id' => $turma->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Testar relacionamentos
        $this->assertTrue($professor->disciplinas->contains($disciplina));
        $this->assertTrue($professor->turmas->contains($turma));
        $this->assertTrue($disciplina->professores->contains($professor));
        $this->assertTrue($disciplina->turmas->contains($turma));
        $this->assertTrue($turma->professores->contains($professor));
        $this->assertTrue($turma->disciplinas->contains($disciplina));
    }

    /**
     * Testa o relacionamento Aluno-Turma através de Matricula
     */
    public function test_aluno_turma_matricula_relationship(): void
    {
        // Criar dados de teste
        $aluno = Aluno::factory()->create();
        $turma = Turma::factory()->create();

        // Criar matrícula
        $matricula = Matricula::create([
            'aluno_id' => $aluno->id,
            'turma_id' => $turma->id,
            'data_matricula' => now(),
            'status' => 'ativa',
        ]);

        // Testar relacionamentos
        $this->assertTrue($aluno->turmas->contains($turma));
        $this->assertTrue($turma->alunos->contains($aluno));
        $this->assertTrue($aluno->matriculas->contains($matricula));
        $this->assertTrue($turma->matriculas->contains($matricula));
        
        // Testar dados do pivot
        $alunoTurma = $aluno->turmas->first();
        $this->assertEquals('ativa', $alunoTurma->pivot->status);
        $this->assertNotNull($alunoTurma->pivot->data_matricula);
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
        $this->expectException(\Illuminate\Database\QueryException::class);
        DB::table('professor_disciplina_turma')->insert([
            'professor_id' => $professor->id,
            'disciplina_id' => $disciplina->id,
            'turma_id' => $turma->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Testa a constraint de unicidade na tabela matriculas
     */
    public function test_matricula_unique_constraint(): void
    {
        $aluno = Aluno::factory()->create();
        $turma = Turma::factory()->create();

        // Primeira matrícula deve funcionar
        Matricula::create([
            'aluno_id' => $aluno->id,
            'turma_id' => $turma->id,
            'data_matricula' => now(),
            'status' => 'ativa',
        ]);

        // Segunda matrícula com os mesmos aluno e turma deve falhar
        $this->expectException(\Illuminate\Database\QueryException::class);
        Matricula::create([
            'aluno_id' => $aluno->id,
            'turma_id' => $turma->id,
            'data_matricula' => now(),
            'status' => 'ativa',
        ]);
    }

    /**
     * Testa os diferentes status de matrícula
     */
    public function test_matricula_status_enum(): void
    {
        $aluno = Aluno::factory()->create();
        $turma = Turma::factory()->create();

        $statusValidos = ['ativa', 'inativa', 'transferida', 'cancelada'];

        foreach ($statusValidos as $status) {
            $matricula = Matricula::factory()->create([
                'aluno_id' => $aluno->id,
                'turma_id' => Turma::factory()->create()->id,
                'status' => $status,
            ]);

            $this->assertEquals($status, $matricula->status);
        }
    }
}
