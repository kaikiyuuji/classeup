<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Professor;
use App\Models\Aluno;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de usuário admin
     */
    public function test_admin_user_creation(): void
    {
        $user = User::factory()->create([
            'tipo_usuario' => 'admin',
            'professor_id' => null,
            'aluno_id' => null,
        ]);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isProfessor());
        $this->assertFalse($user->isAluno());
        $this->assertEquals('admin', $user->tipo_usuario);
    }

    /**
     * Testa a criação de usuário professor
     */
    public function test_professor_user_creation(): void
    {
        $professor = Professor::factory()->create();
        $user = User::factory()->create([
            'tipo_usuario' => 'professor',
            'professor_id' => $professor->id,
            'aluno_id' => null,
        ]);

        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isProfessor());
        $this->assertFalse($user->isAluno());
        $this->assertEquals('professor', $user->tipo_usuario);
        $this->assertEquals($professor->id, $user->professor_id);
    }

    /**
     * Testa a criação de usuário aluno
     */
    public function test_aluno_user_creation(): void
    {
        $aluno = Aluno::factory()->create();
        $user = User::factory()->create([
            'tipo_usuario' => 'aluno',
            'professor_id' => null,
            'aluno_id' => $aluno->id,
        ]);

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isProfessor());
        $this->assertTrue($user->isAluno());
        $this->assertEquals('aluno', $user->tipo_usuario);
        $this->assertEquals($aluno->id, $user->aluno_id);
    }

    /**
     * Testa relacionamentos entre User e Professor
     */
    public function test_user_professor_relationship(): void
    {
        $professor = Professor::factory()->create();
        $user = User::factory()->create([
            'tipo_usuario' => 'professor',
            'professor_id' => $professor->id,
        ]);

        $this->assertInstanceOf(Professor::class, $user->professor);
        $this->assertEquals($professor->id, $user->professor->id);
        $this->assertInstanceOf(User::class, $professor->user);
        $this->assertEquals($user->id, $professor->user->id);
    }

    /**
     * Testa relacionamentos entre User e Aluno
     */
    public function test_user_aluno_relationship(): void
    {
        $aluno = Aluno::factory()->create();
        $user = User::factory()->create([
            'tipo_usuario' => 'aluno',
            'aluno_id' => $aluno->id,
        ]);

        $this->assertInstanceOf(Aluno::class, $user->aluno);
        $this->assertEquals($aluno->id, $user->aluno->id);
        $this->assertInstanceOf(User::class, $aluno->user);
        $this->assertEquals($user->id, $aluno->user->id);
    }

    /**
     * Testa se o usuário admin criado pelo seeder existe
     */
    public function test_admin_seeder_user_exists(): void
    {
        $this->artisan('db:seed', ['--class' => 'AdminUserSeeder']);
        
        $adminUser = User::where('email', 'admin@classeup.com')->first();
        
        $this->assertNotNull($adminUser);
        $this->assertEquals('Administrador', $adminUser->name);
        $this->assertEquals('admin', $adminUser->tipo_usuario);
        $this->assertTrue($adminUser->isAdmin());
        $this->assertNull($adminUser->professor_id);
        $this->assertNull($adminUser->aluno_id);
    }
}
