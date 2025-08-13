<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\Disciplina;
use App\Models\Aluno;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ProfessorDashboardApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $professorUser;
    private Professor $professor;
    private Turma $turma;
    private Disciplina $disciplina;
    private Aluno $aluno;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar professor
        $this->professor = Professor::factory()->create();
        
        // Criar usuário professor
        $this->professorUser = User::factory()->create([
            'tipo_usuario' => 'professor',
            'professor_id' => $this->professor->id
        ]);
        
        // Criar turma
        $this->turma = Turma::factory()->create();
        
        // Criar disciplina
        $this->disciplina = Disciplina::factory()->create();
        
        // Criar aluno
        $this->aluno = Aluno::factory()->create([
            'turma_id' => $this->turma->id
        ]);
        
        // Vincular professor à disciplina e turma
        DB::table('professor_disciplina_turma')->insert([
            'professor_id' => $this->professor->id,
            'disciplina_id' => $this->disciplina->id,
            'turma_id' => $this->turma->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function test_professor_pode_listar_suas_turmas(): void
    {
        $response = $this->actingAs($this->professorUser)
            ->getJson('/professor/api/turmas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'nome',
                        'serie',
                        'turno',
                        'capacidade_maxima',
                        'total_alunos',
                        'disciplinas' => [
                            '*' => [
                                'id',
                                'nome',
                                'codigo'
                            ]
                        ]
                    ]
                ]
            ])
            ->assertJson([
                'success' => true
            ]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($this->turma->id, $data[0]['id']);
        $this->assertEquals($this->turma->nome, $data[0]['nome']);
        $this->assertEquals(1, $data[0]['total_alunos']);
        $this->assertCount(1, $data[0]['disciplinas']);
        $this->assertEquals($this->disciplina->id, $data[0]['disciplinas'][0]['id']);
    }

    public function test_professor_nao_autenticado_nao_pode_listar_turmas(): void
    {
        $response = $this->getJson('/professor/api/turmas');

        $response->assertStatus(401); // Unauthorized para API
    }

    public function test_usuario_nao_professor_nao_pode_listar_turmas(): void
    {
        $alunoUser = User::factory()->create(['tipo_usuario' => 'aluno']);

        $response = $this->actingAs($alunoUser)
            ->getJson('/professor/api/turmas');

        $response->assertStatus(302); // Redirect devido ao middleware
    }

    public function test_professor_pode_listar_alunos_de_sua_turma(): void
    {
        $response = $this->actingAs($this->professorUser)
            ->getJson("/professor/api/turmas/{$this->turma->id}/alunos");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'turma' => [
                        'id',
                        'nome',
                        'serie',
                        'turno',
                        'capacidade_maxima',
                        'total_alunos'
                    ],
                    'alunos' => [
                        '*' => [
                            'id',
                            'nome',
                            'numero_matricula',
                            'data_nascimento',
                            'idade',
                            'email',
                            'turma',
                            'foto_perfil_url'
                        ]
                    ]
                ]
            ])
            ->assertJson([
                'success' => true
            ]);

        $data = $response->json('data');
        $this->assertEquals($this->turma->id, $data['turma']['id']);
        $this->assertEquals(1, $data['turma']['total_alunos']);
        $this->assertCount(1, $data['alunos']);
        $this->assertEquals($this->aluno->id, $data['alunos'][0]['id']);
        $this->assertEquals($this->aluno->nome, $data['alunos'][0]['nome']);
    }

    public function test_professor_nao_pode_listar_alunos_de_turma_nao_vinculada(): void
    {
        // Criar uma turma não vinculada ao professor
        $outraTurma = Turma::factory()->create();
        
        $response = $this->actingAs($this->professorUser)
            ->getJson("/professor/api/turmas/{$outraTurma->id}/alunos");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['turma_id']);
    }

    public function test_professor_nao_pode_listar_alunos_de_turma_inexistente(): void
    {
        $response = $this->actingAs($this->professorUser)
            ->getJson('/professor/api/turmas/99999/alunos');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['turma_id']);
    }

    public function test_professor_sem_turmas_retorna_lista_vazia(): void
    {
        // Criar um professor sem turmas vinculadas
        $professorSemTurmas = Professor::factory()->create();
        $userSemTurmas = User::factory()->create([
            'tipo_usuario' => 'professor',
            'professor_id' => $professorSemTurmas->id
        ]);
        
        $response = $this->actingAs($userSemTurmas)
            ->getJson('/professor/api/turmas');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);
    }

    public function test_turma_sem_alunos_retorna_lista_vazia(): void
    {
        // Remover o aluno da turma
        $this->aluno->delete();
        
        $response = $this->actingAs($this->professorUser)
            ->getJson("/professor/api/turmas/{$this->turma->id}/alunos");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'turma' => [
                        'id' => $this->turma->id,
                        'total_alunos' => 0
                    ],
                    'alunos' => []
                ]
            ]);
    }

    public function test_validacao_request_turma_id_obrigatorio(): void
    {
        $response = $this->actingAs($this->professorUser)
            ->getJson('/professor/api/turmas/null/alunos'); // URL com turma_id null

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['turma_id']);
    }

    public function test_validacao_request_turma_id_deve_ser_inteiro(): void
    {
        $response = $this->actingAs($this->professorUser)
            ->getJson('/professor/api/turmas/abc/alunos');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['turma_id']);
    }
}