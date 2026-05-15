<?php

declare(strict_types=1);

namespace Tests\Feature\Persistence;

use App\Models\Aluno;
use App\Models\Turma;
use Tests\TestCase;

class AuditObserverTest extends TestCase
{
    public function test_created_by_e_updated_by_sao_preenchidos_no_create(): void
    {
        $admin = $this->actingAsAdmin();
        $turma = Turma::factory()->create();

        $aluno = Aluno::factory()->create(['turma_id' => $turma->id]);

        $this->assertSame($admin->id, $aluno->created_by);
        $this->assertSame($admin->id, $aluno->updated_by);
    }

    public function test_updated_by_e_atualizado_no_update(): void
    {
        $criador = $this->actingAsAdmin();
        $aluno = Aluno::factory()->create();

        $this->assertSame($criador->id, $aluno->created_by);

        $editor = $this->actingAsAdmin();
        $aluno->update(['nome' => 'Novo Nome']);
        $aluno->refresh();

        $this->assertSame($criador->id, $aluno->created_by, 'created_by deve permanecer inalterado');
        $this->assertSame($editor->id, $aluno->updated_by);
    }

    public function test_audit_columns_ficam_null_quando_nao_ha_user_autenticado(): void
    {
        $aluno = Aluno::factory()->create();

        $this->assertNull($aluno->created_by);
        $this->assertNull($aluno->updated_by);
    }
}
