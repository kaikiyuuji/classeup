<?php

declare(strict_types=1);

namespace Tests\Feature\Lgpd;

use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Disciplina;
use App\Models\Falta;
use App\Models\Professor;
use App\Services\LgpdService;
use Tests\TestCase;

class LgpdExportTest extends TestCase
{
    public function test_aluno_baixa_proprios_dados(): void
    {
        $aluno = Aluno::factory()->create();
        $this->actingAsAluno($aluno);

        $response = $this->get(route('lgpd.meus-dados'));

        $response->assertOk();
        $data = $response->json();
        $this->assertSame($aluno->id, $data['aluno']['id']);
        $this->assertSame($aluno->numero_matricula, $data['aluno']['numero_matricula']);
    }

    public function test_admin_pode_exportar_dados_de_qualquer_aluno(): void
    {
        $aluno = Aluno::factory()->create();
        $this->actingAsAdmin();

        $response = $this->get(route('lgpd.aluno', $aluno));

        $response->assertOk();
        $this->assertSame($aluno->id, $response->json('aluno.id'));
    }

    public function test_aluno_nao_pode_exportar_dados_de_outro(): void
    {
        $alvo = Aluno::factory()->create();
        $outro = Aluno::factory()->create();
        $this->actingAsAluno($outro);

        $this->get(route('lgpd.aluno', $alvo))->assertForbidden();
    }

    public function test_exportacao_inclui_avaliacoes_e_faltas(): void
    {
        $aluno = Aluno::factory()->create();
        $disciplina = Disciplina::factory()->create();
        $professor = Professor::factory()->create();

        Avaliacao::factory()->create([
            'aluno_id' => $aluno->id,
            'disciplina_id' => $disciplina->id,
            'av1' => 8,
        ]);
        Falta::factory()->create([
            'aluno_id' => $aluno->id,
            'matricula' => $aluno->numero_matricula,
            'disciplina_id' => $disciplina->id,
            'professor_id' => $professor->id,
        ]);

        $service = new LgpdService;
        $dados = $service->exportarDadosDoAluno($aluno);

        $this->assertNotEmpty($dados['avaliacoes']);
        $this->assertNotEmpty($dados['faltas']);
    }
}
