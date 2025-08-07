<?php

namespace Tests\Feature;

use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Falta;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FaltaControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private Aluno $aluno;
    private Professor $professor;
    private Disciplina $disciplina;
    private Turma $turma;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário para autenticação
        $this->user = User::factory()->create();
        
        // Criar dados de teste
        $this->turma = Turma::factory()->create();
        $this->aluno = Aluno::factory()->create(['turma_id' => $this->turma->id]);
        $this->professor = Professor::factory()->create();
        $this->disciplina = Disciplina::factory()->create();
        
        // Vincular professor à disciplina e turma
        $this->professor->disciplinas()->attach($this->disciplina->id);
        \DB::table('professor_disciplina_turma')->insert([
            'professor_id' => $this->professor->id,
            'disciplina_id' => $this->disciplina->id,
            'turma_id' => $this->turma->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function test_pode_acessar_lista_de_faltas(): void
    {
        $response = $this->actingAs($this->user)->get(route('faltas.index'));
        
        $response->assertStatus(200)
                 ->assertViewIs('admin.faltas.index')
                 ->assertViewHas('turmasComVinculo');
    }

    public function test_pode_acessar_interface_de_chamada(): void
    {
        // Primeiro, vamos vincular o professor à turma e disciplina
        $this->turma->professores()->syncWithoutDetaching([
            $this->professor->id => ['disciplina_id' => $this->disciplina->id]
        ]);

        $response = $this->actingAs($this->user)
                         ->get(route('faltas.chamada', [
                             'turma' => $this->turma->id,
                             'disciplina' => $this->disciplina->id
                         ]));

        $response->assertStatus(200)
                 ->assertViewIs('admin.faltas.chamada')
                 ->assertViewHas(['turma', 'disciplina', 'alunos', 'data']);
    }

    public function test_pode_registrar_faltas(): void
    {
        // Vincular o professor à turma e disciplina
        $this->turma->professores()->syncWithoutDetaching([
            $this->professor->id => ['disciplina_id' => $this->disciplina->id]
        ]);
        
        $dataFalta = '2025-08-07';
        
        $response = $this->actingAs($this->user)
                         ->post(route('faltas.store'), [
                             'turma_id' => $this->turma->id,
                             'disciplina_id' => $this->disciplina->id,
                             'professor_id' => $this->professor->id,
                             'data_falta' => $dataFalta,
                             'faltas' => [$this->aluno->numero_matricula]
                         ]);

        $response->assertRedirect()
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('faltas', [
            'matricula' => $this->aluno->numero_matricula,
            'disciplina_id' => $this->disciplina->id,
            'professor_id' => $this->professor->id,
            'data_falta' => $dataFalta . ' 00:00:00',
            'justificada' => 0,
        ]);
    }

    public function test_pode_acessar_relatorio_de_aluno(): void
    {
        $response = $this->actingAs($this->user)
                         ->get(route('faltas.relatorio-aluno', [
                             'matricula' => $this->aluno->numero_matricula
                         ]));
        
        $response->assertStatus(200)
                 ->assertViewIs('admin.faltas.relatorio-aluno');
    }

    public function test_pode_justificar_falta(): void
    {
        $falta = Falta::factory()->create([
            'matricula' => $this->aluno->numero_matricula,
            'disciplina_id' => $this->disciplina->id,
            'professor_id' => $this->professor->id,
            'justificada' => false
        ]);
        
        $response = $this->actingAs($this->user)
                         ->get(route('faltas.justificar', $falta));
        
        $response->assertStatus(200)
                 ->assertViewIs('admin.faltas.justificar')
                 ->assertViewHas('falta');
    }

    public function test_pode_processar_justificativa(): void
    {
        $falta = Falta::factory()->create([
            'matricula' => $this->aluno->numero_matricula,
            'disciplina_id' => $this->disciplina->id,
            'professor_id' => $this->professor->id,
            'justificada' => false
        ]);
        
        $observacao = 'Consulta médica com atestado';
        
        $response = $this->actingAs($this->user)
                         ->post(route('faltas.processar-justificativa', $falta), [
                             'observacoes' => $observacao
                         ]);
        
        $response->assertRedirect()
                 ->assertSessionHas('success');
        
        $falta->refresh();
        $this->assertTrue($falta->justificada);
        $this->assertEquals($observacao, $falta->observacoes);
    }

    public function test_pode_remover_justificativa(): void
    {
        $falta = Falta::factory()->create([
            'matricula' => $this->aluno->numero_matricula,
            'disciplina_id' => $this->disciplina->id,
            'professor_id' => $this->professor->id,
            'justificada' => true,
            'observacoes' => 'Justificativa anterior'
        ]);
        
        $response = $this->actingAs($this->user)
                         ->delete(route('faltas.remover-justificativa', $falta));
        
        $response->assertRedirect()
                 ->assertSessionHas('success');
        
        $falta->refresh();
        $this->assertFalse($falta->justificada);
        $this->assertNull($falta->observacoes);
    }

    public function test_validacao_de_dados_obrigatorios_no_registro(): void
    {
        $response = $this->actingAs($this->user)
                         ->post(route('faltas.store'), []);
        
        $response->assertSessionHasErrors([
            'turma_id',
            'disciplina_id', 
            'professor_id',
            'data_falta'
        ]);
    }

    public function test_nao_permite_acesso_sem_autenticacao(): void
    {
        $response = $this->get(route('faltas.index'));
        $response->assertRedirect(route('login'));
        
        $response = $this->get(route('faltas.chamada', [
            'turma' => $this->turma->id,
            'disciplina' => $this->disciplina->id
        ]));
        $response->assertRedirect(route('login'));
    }
}
