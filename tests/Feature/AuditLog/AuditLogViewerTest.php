<?php

declare(strict_types=1);

namespace Tests\Feature\AuditLog;

use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Turma;
use Tests\TestCase;

class AuditLogViewerTest extends TestCase
{
    public function test_admin_acessa_audit_log(): void
    {
        $admin = $this->actingAsAdmin();

        // Cria algumas entidades com audit columns populadas pelo AuditObserver
        Aluno::factory()->create();
        Turma::factory()->create();
        Disciplina::factory()->create();

        $response = $this->get(route('admin.audit-log.index'));

        $response->assertOk()
            ->assertViewIs('admin.audit-log.index')
            ->assertViewHas('entradas');
    }

    public function test_professor_nao_acessa_audit_log(): void
    {
        $this->actingAsProfessor();

        $this->get(route('admin.audit-log.index'))->assertForbidden();
    }

    public function test_aluno_nao_acessa_audit_log(): void
    {
        $this->actingAsAluno();

        $this->get(route('admin.audit-log.index'))->assertForbidden();
    }

    public function test_guest_e_redirecionado_para_login(): void
    {
        $this->get(route('admin.audit-log.index'))->assertRedirect(route('login'));
    }

    public function test_filtro_por_resource_retorna_apenas_recurso_pedido(): void
    {
        $admin = $this->actingAsAdmin();
        Aluno::factory()->count(2)->create();
        Turma::factory()->count(3)->create();

        $response = $this->get(route('admin.audit-log.index', ['resource' => 'Aluno']));

        $response->assertOk();
        $entradas = $response->viewData('entradas');

        foreach ($entradas as $entrada) {
            $this->assertSame('Aluno', $entrada['resource']);
        }
    }

    public function test_filtro_por_user_id_funciona(): void
    {
        $admin1 = $this->actingAsAdmin();
        Aluno::factory()->create();

        $admin2 = $this->actingAsAdmin();
        Turma::factory()->create();

        $response = $this->get(route('admin.audit-log.index', ['user' => $admin1->id]));

        $entradas = $response->viewData('entradas');
        foreach ($entradas as $entrada) {
            $this->assertSame($admin1->id, $entrada['user_id']);
        }
    }
}
