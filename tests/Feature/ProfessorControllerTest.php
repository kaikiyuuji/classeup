<?php

namespace Tests\Feature;

use App\Models\Professor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfessorControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_index_displays_professores_list(): void
    {
        $professores = Professor::factory(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('admin.professores.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.professores.index')
            ->assertViewHas('professores');
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.professores.create'));

        $response->assertStatus(200)
            ->assertViewIs('admin.professores.create');
    }

    public function test_store_creates_new_professor(): void
    {
        $professorData = [
            'nome' => 'Maria Silva',
            'email' => 'maria@exemplo.com',
            'cpf' => '12345678901',
            'data_nascimento' => '1985-01-01',
            'telefone' => '(11) 99999-9999',
            'endereco' => 'Rua Exemplo, 123',
            'especialidade' => 'Matemática',
            'formacao' => 'Licenciatura em Matemática',
            'ativo' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.professores.store'), $professorData);

        $response->assertRedirect(route('admin.professores.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('professores', [
            'nome' => 'Maria Silva',
            'email' => 'maria@exemplo.com',
            'cpf' => '12345678901',
            'especialidade' => 'Matemática',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.professores.store'), []);

        $response->assertSessionHasErrors([
            'nome', 'email', 'cpf', 'data_nascimento', 'especialidade', 'formacao'
        ]);
    }

    public function test_show_displays_professor(): void
    {
        $professor = Professor::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('admin.professores.show', $professor));

        $response->assertStatus(200)
            ->assertViewIs('admin.professores.show')
            ->assertViewHas('professor', $professor);
    }

    public function test_edit_displays_form_with_professor(): void
    {
        $professor = Professor::factory()->create();
        
        $response = $this->actingAs($this->user)
            ->get(route('admin.professores.edit', $professor));

        $response->assertStatus(200)
            ->assertViewIs('admin.professores.edit')
            ->assertViewHas('professor', $professor);
    }

    public function test_update_modifies_professor(): void
    {
        $professor = Professor::factory()->create();
        $updateData = [
            'nome' => 'Nome Atualizado',
            'email' => 'novo_email@teste.com',
            'cpf' => '98765432100',
            'data_nascimento' => $professor->data_nascimento->format('Y-m-d'),
            'especialidade' => 'Física',
            'formacao' => $professor->formacao,
            // Não incluir 'ativo' para simular checkbox desmarcado
        ];

        $response = $this->actingAs($this->user)
            ->put(route('admin.professores.update', $professor), $updateData);

        $response->assertRedirect(route('admin.professores.show', $professor))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('professores', [
            'id' => $professor->id,
            'nome' => 'Nome Atualizado',
            'email' => 'novo_email@teste.com',
            'cpf' => '98765432100',
            'especialidade' => 'Física',
            'ativo' => 0,
        ]);
    }

    public function test_destroy_deletes_professor(): void
    {
        $professor = Professor::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('admin.professores.destroy', $professor));

        $response->assertRedirect(route('admin.professores.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('professores', [
            'id' => $professor->id,
        ]);
    }

    public function test_guest_cannot_access_professores_routes(): void
    {
        $professor = Professor::factory()->create();

        $this->get(route('admin.professores.index'))->assertRedirect(route('login'));
        $this->get(route('admin.professores.create'))->assertRedirect(route('login'));
        $this->get(route('admin.professores.show', $professor))->assertRedirect(route('login'));
        $this->get(route('admin.professores.edit', $professor))->assertRedirect(route('login'));
    }
}