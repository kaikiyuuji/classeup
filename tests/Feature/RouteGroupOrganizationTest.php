<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Aluno;
use App\Models\Professor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

class RouteGroupOrganizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $professorUser;
    protected User $alunoUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuários de teste
        $this->adminUser = User::factory()->create(['tipo_usuario' => 'admin']);
        
        $professor = Professor::factory()->create();
        $this->professorUser = User::factory()->create([
            'tipo_usuario' => 'professor',
            'professor_id' => $professor->id
        ]);
        
        $aluno = Aluno::factory()->create();
        $this->alunoUser = User::factory()->create([
            'tipo_usuario' => 'aluno',
            'aluno_id' => $aluno->id
        ]);
    }

    public function test_admin_routes_are_properly_grouped(): void
    {
        // Testar rotas administrativas
        $adminRoutes = [
            '/admin/alunos',
            '/admin/professores', 
            '/admin/disciplinas',
            '/admin/turmas',
            '/admin/matriculas'
        ];

        foreach ($adminRoutes as $route) {
            // Admin deve ter acesso
            $response = $this->actingAs($this->adminUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(), 
                "Admin deveria ter acesso à rota {$route}");

            // Professor não deve ter acesso
            $response = $this->actingAs($this->professorUser)->get($route);
            $this->assertEquals(302, $response->getStatusCode(), 
                "Professor não deveria ter acesso à rota {$route}");

            // Aluno não deve ter acesso
            $response = $this->actingAs($this->alunoUser)->get($route);
            $this->assertEquals(302, $response->getStatusCode(), 
                "Aluno não deveria ter acesso à rota {$route}");
        }
    }

    public function test_professor_routes_are_properly_grouped(): void
    {
        // Testar rotas de professor
        $professorRoutes = [
            '/professor/dashboard',
            '/professor/turmas',
            '/professor/notas',
            '/professor/faltas'
        ];

        foreach ($professorRoutes as $route) {
            // Professor deve ter acesso
            $response = $this->actingAs($this->professorUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(), 
                "Professor deveria ter acesso à rota {$route}");

            // Admin deve ter acesso (admin tem acesso a tudo)
            $response = $this->actingAs($this->adminUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(), 
                "Admin deveria ter acesso à rota {$route}");

            // Aluno não deve ter acesso
            $response = $this->actingAs($this->alunoUser)->get($route);
            $this->assertEquals(302, $response->getStatusCode(), 
                "Aluno não deveria ter acesso à rota {$route}");
        }
    }

    public function test_aluno_routes_are_properly_grouped(): void
    {
        // Testar rotas de aluno
        $alunoRoutes = [
            '/aluno/dashboard',
            '/aluno/boletim',
            '/aluno/faltas'
        ];

        foreach ($alunoRoutes as $route) {
            // Aluno deve ter acesso
            $response = $this->actingAs($this->alunoUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(), 
                "Aluno deveria ter acesso à rota {$route}");

            // Admin deve ter acesso (admin tem acesso a tudo)
            $response = $this->actingAs($this->adminUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(), 
                "Admin deveria ter acesso à rota {$route}");

            // Professor não deve ter acesso
            $response = $this->actingAs($this->professorUser)->get($route);
            $this->assertEquals(302, $response->getStatusCode(), 
                "Professor não deveria ter acesso à rota {$route}");
        }
    }

    public function test_shared_routes_allow_multiple_user_types(): void
    {
        // Testar rotas compartilhadas (que permitem múltiplos tipos de usuário)
        $sharedRoutes = [
            '/dashboard',
            '/profile'
        ];

        foreach ($sharedRoutes as $route) {
            // Todos os tipos de usuário devem ter acesso
            $response = $this->actingAs($this->adminUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(), 
                "Admin deveria ter acesso à rota compartilhada {$route}");

            $response = $this->actingAs($this->professorUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(), 
                "Professor deveria ter acesso à rota compartilhada {$route}");

            $response = $this->actingAs($this->alunoUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(), 
                "Aluno deveria ter acesso à rota compartilhada {$route}");
        }
    }

    public function test_route_prefixes_are_correctly_applied(): void
    {
        // Verificar se os prefixos estão sendo aplicados corretamente
        $routes = Route::getRoutes();
        
        $adminRoutes = collect($routes)->filter(function ($route) {
            return str_starts_with($route->uri(), 'admin/');
        });
        
        $professorRoutes = collect($routes)->filter(function ($route) {
            return str_starts_with($route->uri(), 'professor/');
        });
        
        $alunoRoutes = collect($routes)->filter(function ($route) {
            return str_starts_with($route->uri(), 'aluno/');
        });

        $this->assertGreaterThan(0, $adminRoutes->count(), 
            'Devem existir rotas com prefixo admin/');
        $this->assertGreaterThan(0, $professorRoutes->count(), 
            'Devem existir rotas com prefixo professor/');
        $this->assertGreaterThan(0, $alunoRoutes->count(), 
            'Devem existir rotas com prefixo aluno/');
    }
}