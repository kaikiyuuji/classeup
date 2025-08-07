<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Professor;
use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;
use App\Http\Middleware\CheckUserType;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckProfessor;
use App\Http\Middleware\CheckAluno;

class MiddlewareAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar turma para associar aos alunos
        Turma::factory()->create([
            'id' => 1,
            'nome' => 'Turma Teste',
            'ano_letivo' => '2024'
        ]);
    }

    public function test_check_user_type_middleware_allows_authorized_types(): void
    {
        $admin = User::factory()->admin()->create();
        $professor = User::factory()->professor()->create();
        $aluno = User::factory()->aluno()->create();

        $middleware = new CheckUserType();
        $request = Request::create('/test', 'GET');

        // Teste com admin autorizado
        $this->actingAs($admin);
        $response = $middleware->handle($request, function () {
            return response('OK');
        }, 'admin', 'professor');
        
        $this->assertEquals('OK', $response->getContent());

        // Teste com professor autorizado
        $this->actingAs($professor);
        $response = $middleware->handle($request, function () {
            return response('OK');
        }, 'admin', 'professor');
        
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_check_user_type_middleware_redirects_unauthorized_types(): void
    {
        $aluno = User::factory()->aluno()->create();
        
        $middleware = new CheckUserType();
        $request = Request::create('/test', 'GET');

        // Teste com aluno não autorizado para área admin
        $this->actingAs($aluno);
        $response = $middleware->handle($request, function () {
            return response('OK');
        }, 'admin', 'professor');
        
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/dashboard', $response->headers->get('Location'));
    }

    public function test_check_user_type_middleware_redirects_unauthenticated(): void
    {
        $middleware = new CheckUserType();
        $request = Request::create('/test', 'GET');

        $response = $middleware->handle($request, function () {
            return response('OK');
        }, 'admin');
        
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->headers->get('Location'));
    }

    public function test_check_admin_middleware_allows_admin(): void
    {
        $admin = User::factory()->admin()->create();
        
        $middleware = new CheckAdmin();
        $request = Request::create('/test', 'GET');

        $this->actingAs($admin);
        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_check_admin_middleware_redirects_non_admin(): void
    {
        $professor = User::factory()->professor()->create();
        $aluno = User::factory()->aluno()->create();
        
        $middleware = new CheckAdmin();
        $request = Request::create('/test', 'GET');

        // Teste com professor
        $this->actingAs($professor);
        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        
        $this->assertEquals(302, $response->getStatusCode());

        // Teste com aluno
        $this->actingAs($aluno);
        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_check_professor_middleware_allows_professor_and_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $professor = User::factory()->professor()->create();
        
        $middleware = new CheckProfessor();
        $request = Request::create('/test', 'GET');

        // Teste com admin
        $this->actingAs($admin);
        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        
        $this->assertEquals('OK', $response->getContent());

        // Teste com professor
        $this->actingAs($professor);
        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_check_professor_middleware_redirects_aluno(): void
    {
        $aluno = User::factory()->aluno()->create();
        
        $middleware = new CheckProfessor();
        $request = Request::create('/test', 'GET');

        $this->actingAs($aluno);
        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_check_aluno_middleware_allows_aluno_and_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $aluno = User::factory()->aluno()->create();
        
        $middleware = new CheckAluno();
        $request = Request::create('/test', 'GET');

        // Teste com admin
        $this->actingAs($admin);
        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        
        $this->assertEquals('OK', $response->getContent());

        // Teste com aluno
        $this->actingAs($aluno);
        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_check_aluno_middleware_redirects_professor(): void
    {
        $professor = User::factory()->professor()->create();
        
        $middleware = new CheckAluno();
        $request = Request::create('/test', 'GET');

        $this->actingAs($professor);
        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_middleware_aliases_are_registered(): void
    {
        // Verificar se as classes de middleware existem
        $this->assertTrue(class_exists(CheckUserType::class));
        $this->assertTrue(class_exists(CheckAdmin::class));
        $this->assertTrue(class_exists(CheckProfessor::class));
        $this->assertTrue(class_exists(CheckAluno::class));
        
        // Verificar se os middlewares podem ser instanciados
        $this->assertInstanceOf(CheckUserType::class, new CheckUserType());
        $this->assertInstanceOf(CheckAdmin::class, new CheckAdmin());
        $this->assertInstanceOf(CheckProfessor::class, new CheckProfessor());
        $this->assertInstanceOf(CheckAluno::class, new CheckAluno());
    }
}
