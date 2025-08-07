<?php

namespace Tests\Feature;

use App\Models\Turma;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TurmaControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_index_displays_turmas_list(): void
    {
        $turmas = Turma::factory(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('admin.turmas.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.turmas.index')
            ->assertViewHas('turmas');
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.turmas.create'));

        $response->assertStatus(200)
            ->assertViewIs('admin.turmas.create');
    }

    public function test_store_creates_new_turma(): void
    {
        $turmaData = [
            'nome' => 'Fundamental Matutino - A',
            'ano_letivo' => 2024,
            'serie' => 'fundamental',
            'turno' => 'matutino',
            'capacidade_maxima' => 30,
            'ativo' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.turmas.store'), $turmaData);

        $response->assertRedirect(route('admin.turmas.index'))
            ->assertSessionHas('success', 'Turma criada com sucesso!');

        $this->assertDatabaseHas('turmas', $turmaData);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.turmas.store'), []);

        $response->assertSessionHasErrors([
            'nome',
            'ano_letivo',
            'serie',
            'turno',
            'capacidade_maxima'
        ]);
    }

    public function test_show_displays_turma(): void
    {
        $turma = Turma::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('admin.turmas.show', $turma));

        $response->assertStatus(200)
            ->assertViewIs('admin.turmas.show')
            ->assertViewHas('turma', $turma);
    }

    public function test_edit_displays_form_with_turma(): void
    {
        $turma = Turma::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('admin.turmas.edit', $turma));

        $response->assertStatus(200)
            ->assertViewIs('admin.turmas.edit')
            ->assertViewHas('turma', $turma);
    }

    public function test_update_modifies_turma(): void
    {
        $turma = Turma::factory()->create();
        $updateData = [
            'nome' => '2ª Vespertino - B',
            'ano_letivo' => 2024,
            'serie' => 'médio',
            'turno' => 'vespertino',
            'capacidade_maxima' => 35,
            'ativo' => '0', // Enviando como string para simular formulário
        ];

        $response = $this->actingAs($this->user)
            ->put(route('admin.turmas.update', $turma), $updateData);

        $response->assertRedirect(route('admin.turmas.show', $turma))
            ->assertSessionHas('success', 'Turma atualizada com sucesso!');

        $this->assertDatabaseHas('turmas', [
            'id' => $turma->id,
            'nome' => '2ª Vespertino - B',
            'ano_letivo' => 2024,
            'serie' => 'médio',
            'turno' => 'vespertino',
            'capacidade_maxima' => 35,
        ]);
        
        // Verificar separadamente que a turma foi atualizada
        $turma->refresh();
        $this->assertEquals('2ª Vespertino - B', $turma->nome);
        $this->assertEquals(2024, $turma->ano_letivo);
        $this->assertEquals('médio', $turma->serie);
        $this->assertEquals('vespertino', $turma->turno);
        $this->assertEquals(35, $turma->capacidade_maxima);
    }

    public function test_destroy_deletes_turma(): void
    {
        $turma = Turma::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('admin.turmas.destroy', $turma));

        $response->assertRedirect(route('admin.turmas.index'))
            ->assertSessionHas('success', 'Turma excluída com sucesso!');

        $this->assertDatabaseMissing('turmas', ['id' => $turma->id]);
    }

    public function test_store_validates_serie_range(): void
    {
        $turmaData = [
            'nome' => 'Turma Teste',
            'ano_letivo' => 2024,
            'serie' => 'inválido', // Nível educacional inválido
            'turno' => 'matutino',
            'capacidade_maxima' => 30,
            'ativo' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.turmas.store'), $turmaData);

        $response->assertSessionHasErrors(['serie']);
    }

    public function test_store_validates_turno_options(): void
    {
        $turmaData = [
            'nome' => 'Turma Teste',
            'ano_letivo' => 2024,
            'serie' => 'fundamental',
            'turno' => 'invalido', // Turno inválido
            'capacidade_maxima' => 30,
            'ativo' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.turmas.store'), $turmaData);

        $response->assertSessionHasErrors(['turno']);
    }

    public function test_store_validates_capacidade_maxima_range(): void
    {
        $turmaData = [
            'nome' => 'Turma Teste',
            'ano_letivo' => 2024,
            'serie' => 'fundamental',
            'turno' => 'matutino',
            'capacidade_maxima' => 100, // Capacidade muito alta
            'ativo' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.turmas.store'), $turmaData);

        $response->assertSessionHasErrors(['capacidade_maxima']);
    }

    public function test_guest_cannot_access_turmas(): void
    {
        $turma = Turma::factory()->create();

        $this->get(route('admin.turmas.index'))->assertRedirect(route('login'));
        $this->get(route('admin.turmas.create'))->assertRedirect(route('login'));
        $this->get(route('admin.turmas.show', $turma))->assertRedirect(route('login'));
        $this->get(route('admin.turmas.edit', $turma))->assertRedirect(route('login'));
        $this->post(route('admin.turmas.store'))->assertRedirect(route('login'));
        $this->put(route('admin.turmas.update', $turma))->assertRedirect(route('login'));
        $this->delete(route('admin.turmas.destroy', $turma))->assertRedirect(route('login'));
    }
}
