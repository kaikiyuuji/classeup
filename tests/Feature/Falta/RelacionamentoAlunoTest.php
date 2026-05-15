<?php

declare(strict_types=1);

namespace Tests\Feature\Falta;

use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Falta;
use App\Models\Professor;
use Tests\TestCase;

class RelacionamentoAlunoTest extends TestCase
{
    public function test_falta_tem_coluna_aluno_id(): void
    {
        $this->assertTrue(
            \Schema::hasColumn('faltas', 'aluno_id'),
            'A tabela faltas deve ter a coluna aluno_id (FK para alunos.id)'
        );
    }

    public function test_falta_resolve_aluno_via_fk_aluno_id(): void
    {
        $aluno = Aluno::factory()->create();
        $disciplina = Disciplina::factory()->create();
        $professor = Professor::factory()->create();

        $falta = Falta::create([
            'aluno_id' => $aluno->id,
            'matricula' => $aluno->numero_matricula,
            'disciplina_id' => $disciplina->id,
            'professor_id' => $professor->id,
            'data_falta' => now()->toDateString(),
            'justificada' => false,
        ]);

        $this->assertNotNull($falta->aluno);
        $this->assertSame($aluno->id, $falta->aluno->id);
    }

    public function test_falta_existente_sem_aluno_id_e_populada_via_matricula(): void
    {
        $aluno = Aluno::factory()->create();
        $disciplina = Disciplina::factory()->create();
        $professor = Professor::factory()->create();

        // Cria diretamente via DB para simular linha legada (sem aluno_id)
        \DB::table('faltas')->insert([
            'matricula' => $aluno->numero_matricula,
            'aluno_id' => null,
            'disciplina_id' => $disciplina->id,
            'professor_id' => $professor->id,
            'data_falta' => now()->toDateString(),
            'justificada' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $falta = Falta::where('matricula', $aluno->numero_matricula)->firstOrFail();

        // FK direto retorna null, mas o accessor aluno_resolvido faz fallback via matrícula
        $this->assertNull($falta->aluno);
        $this->assertNotNull($falta->aluno_resolvido);
        $this->assertSame($aluno->id, $falta->aluno_resolvido->id);
    }
}
