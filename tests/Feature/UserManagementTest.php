<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Professor;
use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    private User $admin;
    private Professor $professor;
    private Aluno $aluno;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->admin()->create();
        $this->professor = Professor::factory()->create();
        $this->aluno = Aluno::factory()->create();
    }
    
    public function test_admin_pode_acessar_lista_de_professores(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.usuarios.professores'));
            
        $response->assertStatus(200)
            ->assertViewIs('admin.usuarios.professores')
            ->assertViewHas('professores');
    }
    
    public function test_admin_pode_acessar_lista_de_alunos(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.usuarios.alunos'));
            
        $response->assertStatus(200)
            ->assertViewIs('admin.usuarios.alunos')
            ->assertViewHas('alunos');
    }
    
    public function test_admin_pode_criar_usuario_para_professor(): void
    {
        $this->assertNull($this->professor->user);
        
        $response = $this->actingAs($this->admin)
            ->post(route('admin.usuarios.vincular-professor', $this->professor->id));
            
        $response->assertRedirect()
            ->assertSessionHas('success');
            
        $this->professor->refresh();
        $this->assertNotNull($this->professor->user);
        
        $user = $this->professor->user;
        $this->assertEquals('professor', $user->tipo_usuario);
        $this->assertEquals($this->professor->id, $user->professor_id);
        $this->assertEquals($this->professor->nome, $user->name);
        $this->assertNotNull($user->email_verified_at);
        
        // Verifica formato do email
        $nomeFormatado = strtolower(
            str_replace(' ', '.', 
                trim(
                    preg_replace('/[^a-zA-Z\s]/', '', $this->professor->nome)
                )
            )
        );
        $expectedEmail = "{$nomeFormatado}.professor@classeup.edu.br";
        $this->assertEquals($expectedEmail, $user->email);
    }
    
    public function test_admin_pode_criar_usuario_para_aluno(): void
    {
        $this->assertNull($this->aluno->user);
        
        $response = $this->actingAs($this->admin)
            ->post(route('admin.usuarios.vincular-aluno', $this->aluno->id));
            
        $response->assertRedirect()
            ->assertSessionHas('success');
            
        $this->aluno->refresh();
        $this->assertNotNull($this->aluno->user);
        
        $user = $this->aluno->user;
        $this->assertEquals('aluno', $user->tipo_usuario);
        $this->assertEquals($this->aluno->id, $user->aluno_id);
        $this->assertEquals($this->aluno->nome, $user->name);
        $this->assertNotNull($user->email_verified_at);
        
        // Verifica formato do email
        $expectedEmail = "{$this->aluno->numero_matricula}@aluno.classeup.edu.br";
        $this->assertEquals($expectedEmail, $user->email);
    }
    
    public function test_nao_pode_criar_usuario_duplicado_para_professor(): void
    {
        // Cria usuário primeiro
        $user = User::factory()->create([
            'tipo_usuario' => 'professor',
            'professor_id' => $this->professor->id
        ]);
        
        $response = $this->actingAs($this->admin)
            ->post(route('admin.usuarios.vincular-professor', $this->professor->id));
            
        $response->assertRedirect()
            ->assertSessionHas('error');
    }
    
    public function test_nao_pode_criar_usuario_duplicado_para_aluno(): void
    {
        // Cria usuário primeiro
        $user = User::factory()->create([
            'tipo_usuario' => 'aluno',
            'aluno_id' => $this->aluno->id
        ]);
        
        $response = $this->actingAs($this->admin)
            ->post(route('admin.usuarios.vincular-aluno', $this->aluno->id));
            
        $response->assertRedirect()
            ->assertSessionHas('error');
    }
    
    public function test_admin_pode_ativar_usuario(): void
    {
        $user = User::factory()->create();
        $user->update(['email_verified_at' => null]);
        
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.usuarios.ativar', $user->id));
            
        $response->assertRedirect()
            ->assertSessionHas('success');
            
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }
    
    public function test_admin_pode_desativar_usuario(): void
    {
        $user = User::factory()->create();
        // Garante que o usuário está ativo
        $user->email_verified_at = now();
        $user->save();
        
        $this->assertNotNull($user->fresh()->email_verified_at);
        
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.usuarios.desativar', $user->id));
            
        $response->assertRedirect()
            ->assertSessionHas('success');
            
        // Verifica diretamente no banco
        $updatedUser = User::find($user->id);
        $this->assertNull($updatedUser->email_verified_at);
    }
    
    public function test_senha_padrao_e_baseada_no_cpf(): void
    {
        $cpfComMascara = '123.456.789-00';
        $professor = Professor::factory()->create(['cpf' => $cpfComMascara]);
        
        $response = $this->actingAs($this->admin)
            ->post(route('admin.usuarios.vincular-professor', $professor->id));
            
        $response->assertRedirect();
        
        $professor->refresh();
        $user = $professor->user;
        
        // Verifica se a senha é o CPF sem formatação
        $this->assertTrue(
            \Illuminate\Support\Facades\Hash::check('12345678900', $user->password)
        );
    }
    
    public function test_professor_nao_pode_acessar_gerenciamento_usuarios(): void
    {
        $professorUser = User::factory()->professor()->create();
        
        $response = $this->actingAs($professorUser)
            ->get(route('admin.usuarios.professores'));
            
        $response->assertRedirect(route('dashboard'));
    }
    
    public function test_aluno_nao_pode_acessar_gerenciamento_usuarios(): void
    {
        $alunoUser = User::factory()->aluno()->create();
        
        $response = $this->actingAs($alunoUser)
            ->get(route('admin.usuarios.alunos'));
            
        $response->assertRedirect(route('dashboard'));
    }
    
    public function test_usuario_nao_autenticado_nao_pode_acessar(): void
    {
        $response = $this->get(route('admin.usuarios.professores'));
        $response->assertRedirect(route('login'));
        
        $response = $this->get(route('admin.usuarios.alunos'));
        $response->assertRedirect(route('login'));
    }
}