<?php

namespace Tests\Feature;

use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\User;
use App\Services\AvaliacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvaliacaoTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Aluno $aluno;
    private Turma $turma;
    private Disciplina $disciplina;
    private Professor $professor;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário para autenticação
        $this->user = User::factory()->create();
        
        // Criar turma
        $this->turma = Turma::factory()->create();
        
        // Criar aluno vinculado à turma
        $this->aluno = Aluno::factory()->create([
            'turma_id' => $this->turma->id
        ]);
        
        // Criar disciplina e professor
        $this->disciplina = Disciplina::factory()->create();
        $this->professor = Professor::factory()->create();
        
        // Vincular professor à disciplina
        $this->professor->disciplinas()->attach($this->disciplina->id);
        
        // Vincular disciplina à turma através do professor
        $this->turma->professores()->attach($this->professor->id, [
            'disciplina_id' => $this->disciplina->id
        ]);
    }

    public function test_pode_acessar_boletim_do_aluno(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('alunos.boletim', $this->aluno));

        $response->assertStatus(200)
            ->assertViewIs('admin.alunos.notas.boletim')
            ->assertViewHas('aluno', $this->aluno)
            ->assertViewHas('avaliacoes');
    }

    public function test_servico_cria_avaliacoes_automaticamente(): void
    {
        $service = new AvaliacaoService();
        
        // Verificar que não há avaliações inicialmente
        $this->assertEquals(0, Avaliacao::count());
        
        // Obter avaliações do aluno (deve criar automaticamente)
        $avaliacoes = $service->obterAvaliacoesDoAluno($this->aluno);
        
        // Verificar que a avaliação foi criada
        $this->assertEquals(1, Avaliacao::count());
        $this->assertCount(1, $avaliacoes);
        
        $avaliacao = $avaliacoes->first();
        $this->assertEquals($this->aluno->id, $avaliacao->aluno_id);
        $this->assertEquals($this->disciplina->id, $avaliacao->disciplina_id);
    }

    public function test_calculo_nota_final_aprovado(): void
    {
        $avaliacao = new Avaliacao([
            'aluno_id' => $this->aluno->id,
            'disciplina_id' => $this->disciplina->id,
            'av1' => 8.0,
            'av2' => 7.5,
            'av3' => 9.0,
            'av4' => 8.5
        ]);
        $avaliacao->save();
        
        $avaliacao->calcularNotaFinal();
        
        $this->assertEquals(8.25, $avaliacao->nota_final);
        $this->assertEquals('aprovado', $avaliacao->situacao);
    }

    public function test_calculo_nota_final_reprovado(): void
    {
        $avaliacao = new Avaliacao([
            'aluno_id' => $this->aluno->id,
            'disciplina_id' => $this->disciplina->id,
            'av1' => 4.0,
            'av2' => 3.5,
            'av3' => 5.0,
            'av4' => 4.5
        ]);
        $avaliacao->save();
        
        $avaliacao->calcularNotaFinal();
        
        $this->assertEquals(4.25, $avaliacao->nota_final);
        $this->assertEquals('reprovado', $avaliacao->situacao);
    }

    public function test_pode_atualizar_notas_da_avaliacao(): void
    {
        $avaliacao = Avaliacao::factory()->create([
            'aluno_id' => $this->aluno->id,
            'disciplina_id' => $this->disciplina->id
        ]);
        
        $dadosAtualizacao = [
            'av1' => 9.0,
            'av2' => 8.5,
            'av3' => 9.5,
            'av4' => 8.0
        ];
        
        $response = $this->actingAs($this->user)
            ->put(route('alunos.avaliacoes.update', [$this->aluno, $avaliacao]), $dadosAtualizacao);
        
        $response->assertRedirect(route('alunos.boletim', $this->aluno))
            ->assertSessionHas('success', 'Notas atualizadas com sucesso!');
        
        $avaliacao->refresh();
        $this->assertEquals(9.0, $avaliacao->av1);
        $this->assertEquals(8.5, $avaliacao->av2);
        $this->assertEquals(9.5, $avaliacao->av3);
        $this->assertEquals(8.0, $avaliacao->av4);
        $this->assertEquals(8.75, $avaliacao->nota_final);
        $this->assertEquals('aprovado', $avaliacao->situacao);
    }

    public function test_validacao_notas_invalidas(): void
    {
        $avaliacao = Avaliacao::factory()->create([
            'aluno_id' => $this->aluno->id,
            'disciplina_id' => $this->disciplina->id
        ]);
        
        $dadosInvalidos = [
            'av1' => 11.0, // Acima do máximo
            'av2' => -1.0, // Abaixo do mínimo
            'av3' => 'abc', // Não numérico
        ];
        
        $response = $this->actingAs($this->user)
            ->put(route('alunos.avaliacoes.update', [$this->aluno, $avaliacao]), $dadosInvalidos);
        
        $response->assertSessionHasErrors(['av1', 'av2', 'av3']);
    }
}
