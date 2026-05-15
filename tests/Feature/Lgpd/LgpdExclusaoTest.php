<?php

declare(strict_types=1);

namespace Tests\Feature\Lgpd;

use App\Models\Aluno;
use App\Models\SolicitacaoExclusao;
use App\Services\LgpdService;
use RuntimeException;
use Tests\TestCase;

class LgpdExclusaoTest extends TestCase
{
    public function test_aluno_pode_solicitar_propria_exclusao(): void
    {
        $aluno = Aluno::factory()->create();
        $user = $this->actingAsAluno($aluno);

        $response = $this->post(route('lgpd.solicitar-exclusao'), [
            'motivo' => 'Não desejo mais usar o sistema',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('solicitacoes_exclusao', [
            'aluno_id' => $aluno->id,
            'solicitante_user_id' => $user->id,
            'status' => 'pendente',
        ]);
    }

    public function test_aprovacao_anonimiza_e_soft_deleta_aluno(): void
    {
        $aluno = Aluno::factory()->create([
            'nome' => 'João Silva',
            'cpf' => '12345678901',
        ]);
        $solicitante = $this->createAlunoUser($aluno);
        $aprovador = $this->createAdmin();

        $service = new LgpdService;
        $solicitacao = $service->solicitarExclusao($aluno, $solicitante);
        $service->efetivarExclusao($solicitacao, $aprovador);

        $alunoComTrashed = Aluno::withTrashed()->find($aluno->id);
        $this->assertNotNull($alunoComTrashed->deleted_at);
        $this->assertSame('Aluno Excluído', $alunoComTrashed->nome);
        $this->assertNotSame('12345678901', $alunoComTrashed->cpf, 'CPF original deve ser substituído por placeholder');
        $this->assertStringStartsWith('deleted_', $alunoComTrashed->email);
    }

    public function test_aprovador_nao_pode_ser_o_solicitante(): void
    {
        $aluno = Aluno::factory()->create();
        $solicitante = $this->createAlunoUser($aluno);

        $service = new LgpdService;
        $solicitacao = $service->solicitarExclusao($aluno, $solicitante);

        $this->expectException(RuntimeException::class);
        $service->efetivarExclusao($solicitacao, $solicitante);
    }

    public function test_solicitacao_duplicada_retorna_a_pendente_existente(): void
    {
        $aluno = Aluno::factory()->create();
        $solicitante = $this->createAlunoUser($aluno);

        $service = new LgpdService;
        $primeira = $service->solicitarExclusao($aluno, $solicitante);
        $segunda = $service->solicitarExclusao($aluno, $solicitante);

        $this->assertSame($primeira->id, $segunda->id);
        $this->assertSame(1, SolicitacaoExclusao::count());
    }

    public function test_admin_lista_solicitacoes_pendentes(): void
    {
        $this->actingAsAdmin();
        $aluno = Aluno::factory()->create();
        $solicitante = $this->createAlunoUser($aluno);
        (new LgpdService)->solicitarExclusao($aluno, $solicitante);

        $this->get(route('admin.lgpd.solicitacoes'))->assertOk();
    }

    public function test_aluno_nao_lista_solicitacoes_admin(): void
    {
        $this->actingAsAluno();
        $this->get(route('admin.lgpd.solicitacoes'))->assertForbidden();
    }
}
