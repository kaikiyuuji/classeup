<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareRouteAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuários de teste para cada tipo
        $this->adminUser = User::factory()->create([
            'tipo_usuario' => 'admin',
            'email' => 'admin@test.com'
        ]);
        
        $this->professorUser = User::factory()->create([
            'tipo_usuario' => 'professor',
            'email' => 'professor@test.com'
        ]);
        
        $this->alunoUser = User::factory()->create([
            'tipo_usuario' => 'aluno',
            'email' => 'aluno@test.com'
        ]);
    }

    /**
     * Testa acesso negado para rotas administrativas
     */
    public function test_admin_routes_deny_non_admin_users(): void
    {
        // Rotas que devem ser acessíveis apenas para admins
        $adminRoutes = [
            '/admin/alunos',
            '/admin/professores',
            '/admin/disciplinas',
            '/admin/turmas',
            '/admin/matriculas',
            '/admin/relatorios'
        ];

        foreach ($adminRoutes as $route) {
            // Professor tentando acessar rota admin
            $response = $this->actingAs($this->professorUser)->get($route);
            $this->assertEquals(302, $response->getStatusCode(), 
                "Professor deveria ser redirecionado ao tentar acessar {$route}");
            $this->assertStringContainsString('/dashboard', $response->headers->get('Location'),
                "Professor deveria ser redirecionado para dashboard ao tentar acessar {$route}");

            // Aluno tentando acessar rota admin
            $response = $this->actingAs($this->alunoUser)->get($route);
            $this->assertEquals(302, $response->getStatusCode(),
                "Aluno deveria ser redirecionado ao tentar acessar {$route}");
            $this->assertStringContainsString('/dashboard', $response->headers->get('Location'),
                "Aluno deveria ser redirecionado para dashboard ao tentar acessar {$route}");

            // Admin deve ter acesso
            $response = $this->actingAs($this->adminUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(),
                "Admin deveria ter acesso a {$route}");
        }
    }

    /**
     * Testa acesso negado para rotas de professor
     */
    public function test_professor_routes_deny_aluno_users(): void
    {
        // Rotas que devem ser acessíveis para professores e admins
        $professorRoutes = [
            '/professor/dashboard',
            '/professor/turmas',
            '/professor/notas',
            '/professor/faltas'
        ];

        foreach ($professorRoutes as $route) {
            // Aluno tentando acessar rota de professor
            $response = $this->actingAs($this->alunoUser)->get($route);
            $this->assertEquals(302, $response->getStatusCode(),
                "Aluno deveria ser redirecionado ao tentar acessar {$route}");
            $this->assertStringContainsString('/dashboard', $response->headers->get('Location'),
                "Aluno deveria ser redirecionado para dashboard ao tentar acessar {$route}");

            // Professor deve ter acesso
            $response = $this->actingAs($this->professorUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(),
                "Professor deveria ter acesso a {$route}");

            // Admin também deve ter acesso
            $response = $this->actingAs($this->adminUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(),
                "Admin deveria ter acesso a {$route}");
        }
    }

    /**
     * Testa acesso negado para rotas de aluno
     */
    public function test_aluno_routes_deny_professor_users(): void
    {
        // Rotas que devem ser acessíveis para alunos e admins
        $alunoRoutes = [
            '/aluno/dashboard',
            '/aluno/boletim',
            '/aluno/faltas',
            '/aluno/perfil'
        ];

        foreach ($alunoRoutes as $route) {
            // Professor tentando acessar rota de aluno
            $response = $this->actingAs($this->professorUser)->get($route);
            $this->assertEquals(302, $response->getStatusCode(),
                "Professor deveria ser redirecionado ao tentar acessar {$route}");
            $this->assertStringContainsString('/dashboard', $response->headers->get('Location'),
                "Professor deveria ser redirecionado para dashboard ao tentar acessar {$route}");

            // Aluno deve ter acesso
            $response = $this->actingAs($this->alunoUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(),
                "Aluno deveria ter acesso a {$route}");

            // Admin também deve ter acesso
            $response = $this->actingAs($this->adminUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(),
                "Admin deveria ter acesso a {$route}");
        }
    }

    /**
     * Testa rotas compartilhadas com múltiplos tipos de usuário
     */
    public function test_shared_routes_access_control(): void
    {
        // Rotas compartilhadas entre professor e aluno
        $sharedProfessorAlunoRoutes = [
            '/biblioteca',
            '/calendario'
        ];

        foreach ($sharedProfessorAlunoRoutes as $route) {
            // Professor deve ter acesso
            $response = $this->actingAs($this->professorUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(),
                "Professor deveria ter acesso a {$route}");

            // Aluno deve ter acesso
            $response = $this->actingAs($this->alunoUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(),
                "Aluno deveria ter acesso a {$route}");

            // Admin não deveria ter acesso direto (não está no middleware)
            $response = $this->actingAs($this->adminUser)->get($route);
            $this->assertEquals(302, $response->getStatusCode(),
                "Admin deveria ser redirecionado ao tentar acessar {$route}");
        }

        // Rotas compartilhadas entre admin e professor
        $sharedAdminProfessorRoutes = [
            '/gestao/estatisticas',
            '/gestao/dashboard-gestao'
        ];

        foreach ($sharedAdminProfessorRoutes as $route) {
            // Admin deve ter acesso
            $response = $this->actingAs($this->adminUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(),
                "Admin deveria ter acesso a {$route}");

            // Professor deve ter acesso
            $response = $this->actingAs($this->professorUser)->get($route);
            $this->assertNotEquals(403, $response->getStatusCode(),
                "Professor deveria ter acesso a {$route}");

            // Aluno não deveria ter acesso
            $response = $this->actingAs($this->alunoUser)->get($route);
            $this->assertEquals(302, $response->getStatusCode(),
                "Aluno deveria ser redirecionado ao tentar acessar {$route}");
        }
    }

    /**
     * Testa redirecionamento para usuários não autenticados
     */
    public function test_unauthenticated_users_redirected_to_login(): void
    {
        $protectedRoutes = [
            '/admin/alunos',
            '/professor/dashboard',
            '/aluno/dashboard',
            '/biblioteca',
            '/gestao/estatisticas'
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $this->assertEquals(302, $response->getStatusCode(),
                "Usuário não autenticado deveria ser redirecionado ao tentar acessar {$route}");
            $this->assertStringContainsString('/login', $response->headers->get('Location'),
                "Usuário não autenticado deveria ser redirecionado para login ao tentar acessar {$route}");
        }
    }

    /**
     * Testa se as mensagens de erro são exibidas corretamente
     */
    public function test_error_messages_are_displayed(): void
    {
        // Professor tentando acessar rota admin
        $response = $this->actingAs($this->professorUser)
            ->followingRedirects()
            ->get('/admin/alunos');
        
        $response->assertSee('Você não tem permissão para acessar esta área');

        // Aluno tentando acessar rota de professor
        $response = $this->actingAs($this->alunoUser)
            ->followingRedirects()
            ->get('/professor/dashboard');
        
        $response->assertSee('Você não tem permissão para acessar esta área');
    }

    /**
     * Testa verificação de propriedade no boletim
     */
    public function test_boletim_ownership_verification(): void
    {
        // Criar dois alunos
        $aluno1 = \App\Models\Aluno::factory()->create();
        $aluno2 = \App\Models\Aluno::factory()->create();
        
        // Criar usuários associados
        $userAluno1 = User::factory()->create([
            'tipo_usuario' => 'aluno',
            'aluno_id' => $aluno1->id
        ]);
        
        $userAluno2 = User::factory()->create([
            'tipo_usuario' => 'aluno',
            'aluno_id' => $aluno2->id
        ]);

        // Aluno 1 tentando acessar boletim do Aluno 2
        $response = $this->actingAs($userAluno1)->get("/aluno/boletim/{$aluno2->id}");
        $this->assertEquals(403, $response->getStatusCode(),
            "Aluno não deveria poder acessar boletim de outro aluno");

        // Aluno 1 acessando seu próprio boletim
        $response = $this->actingAs($userAluno1)->get("/aluno/boletim/{$aluno1->id}");
        $this->assertNotEquals(403, $response->getStatusCode(),
            "Aluno deveria poder acessar seu próprio boletim");

        // Admin acessando qualquer boletim
        $response = $this->actingAs($this->adminUser)->get("/aluno/boletim/{$aluno1->id}");
        $this->assertNotEquals(403, $response->getStatusCode(),
            "Admin deveria poder acessar qualquer boletim");
    }

    /**
     * Testa verificação de propriedade nas faltas
     */
    public function test_falta_ownership_verification(): void
    {
        // Criar dois alunos
        $aluno1 = \App\Models\Aluno::factory()->create();
        $aluno2 = \App\Models\Aluno::factory()->create();
        
        // Criar usuários associados
        $userAluno1 = User::factory()->create([
            'tipo_usuario' => 'aluno',
            'aluno_id' => $aluno1->id
        ]);
        
        $userAluno2 = User::factory()->create([
            'tipo_usuario' => 'aluno',
            'aluno_id' => $aluno2->id
        ]);

        // Criar faltas para cada aluno
         $falta1 = \App\Models\Falta::factory()->create(['matricula' => $aluno1->numero_matricula]);
         $falta2 = \App\Models\Falta::factory()->create(['matricula' => $aluno2->numero_matricula]);

        // Aluno 1 tentando justificar falta do Aluno 2
        $response = $this->actingAs($userAluno1)->get("/aluno/faltas/justificar/{$falta2->id}");
        $this->assertEquals(403, $response->getStatusCode(),
            "Aluno não deveria poder justificar falta de outro aluno");

        // Aluno 1 justificando sua própria falta
        $response = $this->actingAs($userAluno1)->get("/aluno/faltas/justificar/{$falta1->id}");
        $this->assertNotEquals(403, $response->getStatusCode(),
            "Aluno deveria poder justificar sua própria falta");

        // Admin justificando qualquer falta
        $response = $this->actingAs($this->adminUser)->get("/aluno/faltas/justificar/{$falta1->id}");
        $this->assertNotEquals(403, $response->getStatusCode(),
            "Admin deveria poder justificar qualquer falta");
    }
}