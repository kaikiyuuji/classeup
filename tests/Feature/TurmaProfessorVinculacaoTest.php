<?php

namespace Tests\Feature;

use App\Models\Disciplina;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurmaProfessorVinculacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        
        // Autenticar usuário para os testes
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    /** @test */
    public function pode_visualizar_turma_com_sessao_de_professores()
    {
        $turma = Turma::create([
            'nome' => 'Teste Turma',
            'ano_letivo' => 2024,
            'serie' => 'fundamental',
            'turno' => 'matutino',
            'capacidade_maxima' => 30,
            'ativo' => true,
        ]);
        
        // Criar um professor com disciplina para que apareça a seção de vinculação
        $professor = Professor::factory()->create(['ativo' => true]);
        $disciplina = Disciplina::factory()->create();
        $professor->disciplinas()->attach($disciplina->id);
        
        $response = $this->get(route('admin.turmas.show', $turma));
        
        $response->assertStatus(200);
        $response->assertSee('Professores Vinculados');
        $response->assertSee('Vincular Professor à Turma');
    }

    /** @test */
    public function pode_vincular_professor_a_turma()
    {
        $turma = Turma::create([
            'nome' => 'Teste Turma',
            'ano_letivo' => 2024,
            'serie' => 'fundamental',
            'turno' => 'matutino',
            'capacidade_maxima' => 30,
            'ativo' => true,
        ]);
        
        $professor = Professor::factory()->create(['ativo' => true]);
        $disciplina = Disciplina::factory()->create();
        
        // Associar disciplina ao professor
        $professor->disciplinas()->attach($disciplina->id);
        
        $response = $this->post(route('admin.turmas.vincular-professor', $turma), [
            'professor_id' => $professor->id,
            'disciplina_id' => $disciplina->id,
        ]);
        
        $response->assertRedirect(route('admin.turmas.show', $turma));
        $response->assertSessionHas('success', 'Professor vinculado com sucesso!');
        
        // Verificar se a vinculação foi criada
        $this->assertDatabaseHas('professor_disciplina_turma', [
            'professor_id' => $professor->id,
            'disciplina_id' => $disciplina->id,
            'turma_id' => $turma->id,
        ]);
    }

    /** @test */
    public function pode_desvincular_professor_da_turma()
    {
        $turma = Turma::create([
            'nome' => 'Teste Turma',
            'ano_letivo' => 2024,
            'serie' => 'fundamental',
            'turno' => 'matutino',
            'capacidade_maxima' => 30,
            'ativo' => true,
        ]);
        
        $professor = Professor::factory()->create(['ativo' => true]);
        $disciplina = Disciplina::factory()->create();
        
        // Criar vinculação
        $turma->professores()->attach($professor->id, [
            'disciplina_id' => $disciplina->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $response = $this->delete(route('admin.turmas.desvincular-professor', $turma), [
            'professor_id' => $professor->id,
            'disciplina_id' => $disciplina->id,
        ]);
        
        $response->assertRedirect(route('admin.turmas.show', $turma));
        $response->assertSessionHas('success', 'Professor desvinculado com sucesso!');
        
        // Verificar se a vinculação foi removida
        $this->assertDatabaseMissing('professor_disciplina_turma', [
            'professor_id' => $professor->id,
            'disciplina_id' => $disciplina->id,
            'turma_id' => $turma->id,
        ]);
    }
}