<?php

declare(strict_types=1);

namespace Tests\Feature\Persistence;

use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Disciplina;
use App\Models\Falta;
use App\Models\Professor;
use App\Models\Turma;
use Tests\TestCase;

class SoftDeleteTest extends TestCase
{
    public function test_aluno_deletado_nao_aparece_em_consultas_padrao(): void
    {
        $aluno = Aluno::factory()->create();
        $aluno->delete();

        $this->assertNull(Aluno::find($aluno->id));
        $this->assertNotNull(Aluno::withTrashed()->find($aluno->id));
    }

    public function test_aluno_pode_ser_restaurado(): void
    {
        $aluno = Aluno::factory()->create();
        $aluno->delete();

        Aluno::withTrashed()->find($aluno->id)?->restore();

        $this->assertNotNull(Aluno::find($aluno->id));
    }

    public function test_soft_delete_funciona_em_todas_entidades_de_dominio(): void
    {
        $entidades = [
            Aluno::factory()->create(),
            Professor::factory()->create(),
            Turma::factory()->create(),
            Disciplina::factory()->create(),
        ];

        foreach ($entidades as $entidade) {
            $entidade->delete();
            $class = $entidade::class;
            $this->assertNull($class::find($entidade->id), "{$class} deveria estar soft-deleted");
            $this->assertNotNull($class::withTrashed()->find($entidade->id));
        }
    }

    public function test_avaliacao_e_falta_tem_soft_delete(): void
    {
        $aluno = Aluno::factory()->create();
        $disciplina = Disciplina::factory()->create();
        $professor = Professor::factory()->create();

        $avaliacao = Avaliacao::factory()->create([
            'aluno_id' => $aluno->id,
            'disciplina_id' => $disciplina->id,
        ]);
        $falta = Falta::factory()->create([
            'aluno_id' => $aluno->id,
            'matricula' => $aluno->numero_matricula,
            'disciplina_id' => $disciplina->id,
            'professor_id' => $professor->id,
        ]);

        $avaliacao->delete();
        $falta->delete();

        $this->assertNull(Avaliacao::find($avaliacao->id));
        $this->assertNull(Falta::find($falta->id));
        $this->assertNotNull(Avaliacao::withTrashed()->find($avaliacao->id));
        $this->assertNotNull(Falta::withTrashed()->find($falta->id));
    }
}
