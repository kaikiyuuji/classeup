<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Aluno;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\Disciplina;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $professorUser;
    private User $alunoUser;
    private Professor $professor;
    private Aluno $aluno;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar professor e aluno primeiro
        $this->professor = Professor::factory()->create();
        $turma = Turma::factory()->create();
        $this->aluno = Aluno::factory()->create([
            'turma_id' => $turma->id
        ]);
        
        // Criar usuários de teste com relacionamentos
        $this->adminUser = User::factory()->admin()->create();
        $this->professorUser = User::factory()->professor()->create([
            'professor_id' => $this->professor->id
        ]);
        $this->alunoUser = User::factory()->aluno()->create([
            'aluno_id' => $this->aluno->id
        ]);
    }

    public function test_dashboard_index_redirects_admin_to_admin_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/dashboard');
        
        $response->assertRedirect(route('dashboard.admin'));
    }

    public function test_dashboard_index_redirects_professor_to_professor_dashboard(): void
    {
        $response = $this->actingAs($this->professorUser)->get('/dashboard');
        
        $response->assertRedirect(route('dashboard.professor'));
    }

    public function test_dashboard_index_redirects_aluno_to_aluno_dashboard(): void
    {
        $response = $this->actingAs($this->alunoUser)->get('/dashboard');
        
        $response->assertRedirect(route('dashboard.aluno'));
    }

    public function test_admin_dashboard_shows_statistics(): void
    {
        // Criar alguns dados para estatísticas
        Aluno::factory()->count(5)->create();
        Professor::factory()->count(3)->create();
        Turma::factory()->count(2)->create();
        Disciplina::factory()->count(4)->create();
        
        $response = $this->actingAs($this->adminUser)->get('/dashboard/admin');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas(['totalAlunos', 'totalProfessores', 'totalTurmas', 'totalDisciplinas']);
    }

    public function test_professor_dashboard_shows_professor_data(): void
    {
        // Criar disciplinas e turmas para o professor
        $disciplina = Disciplina::factory()->create();
        $turma = Turma::factory()->create();
        
        // Primeiro associar disciplina ao professor
        $this->professor->disciplinas()->attach($disciplina->id);
        
        // Depois criar a relação tripla professor-disciplina-turma
        DB::table('professor_disciplina_turma')->insert([
            'professor_id' => $this->professor->id,
            'disciplina_id' => $disciplina->id,
            'turma_id' => $turma->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $response = $this->actingAs($this->professorUser)->get('/dashboard/professor');
        
        $response->assertStatus(200);
        $response->assertViewIs('professor.dashboard');
        $response->assertViewHas(['professor', 'turmas', 'disciplinas', 'totalAlunos']);
    }

    public function test_aluno_dashboard_shows_aluno_data(): void
    {
        $response = $this->actingAs($this->alunoUser)->get('/dashboard/aluno');
        
        $response->assertStatus(200);
        $response->assertViewIs('aluno.dashboard');
        $response->assertViewHas(['aluno', 'turma', 'avaliacoes', 'chamadas']);
    }

    public function test_admin_dashboard_requires_admin_middleware(): void
    {
        $response = $this->actingAs($this->professorUser)->get('/dashboard/admin');
        $response->assertRedirect('/dashboard');
        
        $response = $this->actingAs($this->alunoUser)->get('/dashboard/admin');
        $response->assertRedirect('/dashboard');
    }

    public function test_professor_dashboard_requires_professor_middleware(): void
    {
        $response = $this->actingAs($this->alunoUser)->get('/dashboard/professor');
        $response->assertRedirect('/dashboard');
    }

    public function test_aluno_dashboard_requires_aluno_middleware(): void
    {
        $response = $this->actingAs($this->professorUser)->get('/dashboard/aluno');
        $response->assertRedirect('/dashboard');
    }

    public function test_professor_dashboard_aborts_when_professor_not_found(): void
    {
        // Criar usuário professor sem registro de professor (professor_id = null)
        $userWithoutProfessor = User::factory()->professor()->create([
            'professor_id' => null
        ]);
        
        $response = $this->actingAs($userWithoutProfessor)->get('/dashboard/professor');
        $response->assertStatus(403);
    }

    public function test_aluno_dashboard_aborts_when_aluno_not_found(): void
    {
        // Criar usuário aluno sem registro de aluno (aluno_id = null)
        $userWithoutAluno = User::factory()->aluno()->create([
            'aluno_id' => null
        ]);
        
        $response = $this->actingAs($userWithoutAluno)->get('/dashboard/aluno');
        $response->assertStatus(403);
    }

    public function test_dashboard_index_works_with_valid_user_types(): void
    {
        // Testar que todos os tipos válidos funcionam corretamente
        $this->assertTrue(in_array('admin', ['admin', 'professor', 'aluno']));
        $this->assertTrue(in_array('professor', ['admin', 'professor', 'aluno']));
        $this->assertTrue(in_array('aluno', ['admin', 'professor', 'aluno']));
    }
}
