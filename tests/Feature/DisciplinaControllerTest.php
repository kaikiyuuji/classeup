<?php

namespace Tests\Feature;

use App\Models\Disciplina;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DisciplinaControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_index_displays_disciplinas_list(): void
    {
        $disciplinas = Disciplina::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('disciplinas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.disciplinas.index');
        $response->assertViewHas('disciplinas');
        foreach ($disciplinas as $disciplina) {
            $response->assertSee($disciplina->nome);
            $response->assertSee($disciplina->codigo);
        }
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('disciplinas.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.disciplinas.create');
    }

    public function test_store_creates_new_disciplina(): void
    {
        $disciplinaData = [
            'nome' => 'Matemática Avançada',
            'codigo' => 'MAT001',
            'descricao' => 'Disciplina de matemática para ensino médio',
            'carga_horaria' => 80,
            'ativo' => '1',
        ];

        $response = $this->actingAs($this->user)->post(route('disciplinas.store'), $disciplinaData);

        $response->assertRedirect(route('disciplinas.index'));
        $response->assertSessionHas('success', 'Disciplina criada com sucesso!');
        
        $this->assertDatabaseHas('disciplinas', [
            'nome' => 'Matemática Avançada',
            'codigo' => 'MAT001',
            'descricao' => 'Disciplina de matemática para ensino médio',
            'carga_horaria' => 80,
            'ativo' => true,
        ]);
    }

    public function test_show_displays_disciplina(): void
    {
        $disciplina = Disciplina::factory()->create();

        $response = $this->actingAs($this->user)->get(route('disciplinas.show', $disciplina));

        $response->assertStatus(200);
        $response->assertViewIs('admin.disciplinas.show');
        $response->assertViewHas('disciplina', $disciplina);
        $response->assertSee($disciplina->nome);
        $response->assertSee($disciplina->codigo);
    }

    public function test_edit_displays_form_with_disciplina(): void
    {
        $disciplina = Disciplina::factory()->create();

        $response = $this->actingAs($this->user)->get(route('disciplinas.edit', $disciplina));

        $response->assertStatus(200);
        $response->assertViewIs('admin.disciplinas.edit');
        $response->assertViewHas('disciplina', $disciplina);
        $response->assertSee($disciplina->nome);
        $response->assertSee($disciplina->codigo);
    }

    public function test_update_modifies_disciplina(): void
    {
        $disciplina = Disciplina::factory()->create([
            'nome' => 'Nome Original',
            'codigo' => 'ORIG001',
            'ativo' => true,
        ]);

        $updateData = [
            'nome' => 'Nome Atualizado',
            'codigo' => 'UPD001',
            'descricao' => 'Descrição atualizada',
            'carga_horaria' => 120,
            // Não incluir 'ativo' simula checkbox desmarcado
        ];

        $response = $this->actingAs($this->user)->put(route('disciplinas.update', $disciplina), $updateData);

        $response->assertRedirect(route('disciplinas.show', $disciplina));
        $response->assertSessionHas('success', 'Disciplina atualizada com sucesso!');
        
        $disciplina->refresh();
        $this->assertEquals('Nome Atualizado', $disciplina->nome);
        $this->assertEquals('UPD001', $disciplina->codigo);
        $this->assertEquals('Descrição atualizada', $disciplina->descricao);
        $this->assertEquals(120, $disciplina->carga_horaria);
        $this->assertEquals(0, $disciplina->ativo); // Checkbox desmarcado = false
    }

    public function test_destroy_deletes_disciplina(): void
    {
        $disciplina = Disciplina::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('disciplinas.destroy', $disciplina));

        $response->assertRedirect(route('disciplinas.index'));
        $response->assertSessionHas('success', 'Disciplina excluída com sucesso!');
        $this->assertDatabaseMissing('disciplinas', ['id' => $disciplina->id]);
    }

    public function test_guest_cannot_access_disciplinas_routes(): void
    {
        $disciplina = Disciplina::factory()->create();

        $this->get(route('disciplinas.index'))->assertRedirect(route('login'));
        $this->get(route('disciplinas.create'))->assertRedirect(route('login'));
        $this->post(route('disciplinas.store'))->assertRedirect(route('login'));
        $this->get(route('disciplinas.show', $disciplina))->assertRedirect(route('login'));
        $this->get(route('disciplinas.edit', $disciplina))->assertRedirect(route('login'));
        $this->put(route('disciplinas.update', $disciplina))->assertRedirect(route('login'));
        $this->delete(route('disciplinas.destroy', $disciplina))->assertRedirect(route('login'));
    }
}
