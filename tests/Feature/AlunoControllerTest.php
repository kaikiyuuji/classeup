<?php

namespace Tests\Feature;

use App\Models\Aluno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AlunoControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_index_displays_alunos_list(): void
    {
        $alunos = Aluno::factory(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('alunos.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.alunos.index')
            ->assertViewHas('alunos');
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('alunos.create'));

        $response->assertStatus(200)
            ->assertViewIs('admin.alunos.create');
    }

    public function test_store_creates_new_aluno(): void
    {
        $alunoData = [
            'nome' => 'João Silva',
            'email' => 'joao@exemplo.com',
            'cpf' => '12345678901',
            'data_nascimento' => '1990-01-01',
            'telefone' => '(11) 99999-9999',
            'endereco' => 'Rua Exemplo, 123',
            'ativo' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('alunos.store'), $alunoData);

        $response->assertRedirect(route('alunos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('alunos', [
            'nome' => 'João Silva',
            'email' => 'joao@exemplo.com',
            'cpf' => '12345678901',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('alunos.store'), []);

        $response->assertSessionHasErrors([
            'nome', 'email', 'cpf', 'data_nascimento'
        ]);
    }

    public function test_show_displays_aluno(): void
    {
        $aluno = Aluno::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('alunos.show', $aluno));

        $response->assertStatus(200)
            ->assertViewIs('admin.alunos.show')
            ->assertViewHas('aluno', $aluno);
    }

    public function test_edit_displays_form_with_aluno(): void
    {
        $aluno = Aluno::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('alunos.edit', $aluno));

        $response->assertStatus(200)
            ->assertViewIs('admin.alunos.edit')
            ->assertViewHas('aluno', $aluno);
    }

    public function test_update_modifies_aluno(): void
    {
        $aluno = Aluno::factory()->create();
        $updateData = [
            'nome' => 'Nome Atualizado',
            'email' => $aluno->email,
            'cpf' => $aluno->cpf,
            'data_nascimento' => $aluno->data_nascimento->format('Y-m-d'),
            'ativo' => false,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('alunos.update', $aluno), $updateData);

        $response->assertRedirect(route('alunos.show', $aluno))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('alunos', [
            'id' => $aluno->id,
            'nome' => 'Nome Atualizado',
            'ativo' => false,
        ]);
    }

    public function test_destroy_deletes_aluno(): void
    {
        $aluno = Aluno::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('alunos.destroy', $aluno));

        $response->assertRedirect(route('alunos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('alunos', [
            'id' => $aluno->id,
        ]);
    }

    public function test_guest_cannot_access_alunos_routes(): void
    {
        $aluno = Aluno::factory()->create();

        $this->get(route('alunos.index'))->assertRedirect(route('login'));
        $this->get(route('alunos.create'))->assertRedirect(route('login'));
        $this->get(route('alunos.show', $aluno))->assertRedirect(route('login'));
        $this->get(route('alunos.edit', $aluno))->assertRedirect(route('login'));
    }
}
