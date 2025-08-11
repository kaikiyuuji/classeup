<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Aluno;
use App\Models\Professor;
use App\Models\Disciplina;
use App\Models\Chamada;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DashboardChamadasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário admin
        $this->admin = User::factory()->create([
            'tipo_usuario' => 'admin',
            'email' => 'admin@test.com'
        ]);
        
        // Criar professor
        $this->professor = Professor::factory()->create();
        
        // Criar disciplinas
        $this->disciplina1 = Disciplina::factory()->create(['nome' => 'Matemática']);
        $this->disciplina2 = Disciplina::factory()->create(['nome' => 'Português']);
        
        // Criar alunos
        $this->aluno1 = Aluno::factory()->create(['numero_matricula' => '2024001']);
        $this->aluno2 = Aluno::factory()->create(['numero_matricula' => '2024002']);
    }

    /** @test */
    public function dashboard_conta_chamadas_unicas_por_disciplina_e_data()
    {
        $hoje = Carbon::today();
        $ontem = Carbon::yesterday();
        
        // Criar chamadas para o mesmo professor, mesma disciplina, mesmo dia (deve contar como 1 chamada)
        Chamada::factory()->create([
            'professor_id' => $this->professor->id,
            'disciplina_id' => $this->disciplina1->id,
            'matricula' => $this->aluno1->numero_matricula,
            'data_chamada' => $hoje,
            'status' => 'presente'
        ]);
        
        Chamada::factory()->create([
            'professor_id' => $this->professor->id,
            'disciplina_id' => $this->disciplina1->id,
            'matricula' => $this->aluno2->numero_matricula,
            'data_chamada' => $hoje,
            'status' => 'falta'
        ]);
        
        // Criar chamada para outra disciplina no mesmo dia (deve contar como outra chamada)
        Chamada::factory()->create([
            'professor_id' => $this->professor->id,
            'disciplina_id' => $this->disciplina2->id,
            'matricula' => $this->aluno1->numero_matricula,
            'data_chamada' => $hoje,
            'status' => 'presente'
        ]);
        
        // Criar chamada para mesma disciplina em outro dia (deve contar como outra chamada)
        Chamada::factory()->create([
            'professor_id' => $this->professor->id,
            'disciplina_id' => $this->disciplina1->id,
            'matricula' => $this->aluno1->numero_matricula,
            'data_chamada' => $ontem,
            'status' => 'presente'
        ]);
        
        // Fazer login como admin e acessar dashboard
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        
        $response->assertStatus(200);
        
        // Verificar se as variáveis estão sendo passadas corretamente
        $response->assertViewHas('totalChamadas');
        $response->assertViewHas('professoresMaisAtivos');
        $response->assertViewHas('atividadesRecentes');
        
        // Verificar se o total de chamadas únicas está correto (3 chamadas únicas)
        $totalChamadas = $response->viewData('totalChamadas');
        $this->assertEquals(3, $totalChamadas);
        
        // Verificar se o professor aparece com 3 chamadas únicas
        $professoresMaisAtivos = $response->viewData('professoresMaisAtivos');
        $this->assertCount(1, $professoresMaisAtivos);
        $this->assertEquals($this->professor->nome, $professoresMaisAtivos[0]['nome']);
        $this->assertEquals(3, $professoresMaisAtivos[0]['total_chamadas']);
    }

    /** @test */
    public function dashboard_calcula_estatisticas_frequencia_corretamente()
    {
        $hoje = Carbon::today();
        
        // Criar chamadas com diferentes status
        Chamada::factory()->create([
            'professor_id' => $this->professor->id,
            'disciplina_id' => $this->disciplina1->id,
            'matricula' => $this->aluno1->numero_matricula,
            'data_chamada' => $hoje,
            'status' => 'presente'
        ]);
        
        Chamada::factory()->create([
            'professor_id' => $this->professor->id,
            'disciplina_id' => $this->disciplina1->id,
            'matricula' => $this->aluno2->numero_matricula,
            'data_chamada' => $hoje,
            'status' => 'falta'
        ]);
        
        // Fazer login como admin e acessar dashboard
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        
        $response->assertStatus(200);
        
        // Verificar estatísticas
        $totalPresencas = $response->viewData('totalPresencas');
        $totalFaltas = $response->viewData('totalFaltas');
        $percentualFrequencia = $response->viewData('percentualFrequencia');
        
        $this->assertEquals(1, $totalPresencas);
        $this->assertEquals(1, $totalFaltas);
        $this->assertEquals(50.0, $percentualFrequencia); // 1 presença de 2 registros = 50%
    }
}