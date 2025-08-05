<?php

namespace Tests\Feature;

use App\Models\Aluno;
use App\Models\Matricula;
use App\Models\Turma;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MatriculaControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private Aluno $aluno;
    private Turma $turma;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->aluno = Aluno::factory()->create();
        $this->turma = Turma::factory()->create(['capacidade_maxima' => 30]);
    }

    public function test_index_displays_matriculas(): void
    {
        $matriculas = Matricula::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('matriculas.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.matriculas.index')
            ->assertViewHas('matriculas');
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('matriculas.create'));

        $response->assertStatus(200)
            ->assertViewIs('admin.matriculas.create')
            ->assertViewHas(['alunos', 'turmas']);
    }

    public function test_store_creates_matricula_successfully(): void
    {
        $data = [
            'aluno_id' => $this->aluno->id,
            'turma_id' => $this->turma->id,
            'data_matricula' => now()->format('Y-m-d'),
            'status' => 'ativa'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('matriculas.store'), $data);

        $response->assertRedirect(route('matriculas.index'))
            ->assertSessionHas('success', 'Matrícula realizada com sucesso!');

        $this->assertDatabaseHas('matriculas', [
            'aluno_id' => $this->aluno->id,
            'turma_id' => $this->turma->id,
            'status' => 'ativa'
        ]);
    }

    public function test_store_prevents_duplicate_matricula(): void
    {
        // Criar uma matrícula existente
        Matricula::factory()->create([
            'aluno_id' => $this->aluno->id,
            'turma_id' => $this->turma->id
        ]);

        $data = [
            'aluno_id' => $this->aluno->id,
            'turma_id' => $this->turma->id,
            'data_matricula' => now()->format('Y-m-d'),
            'status' => 'ativa'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('matriculas.store'), $data);

        $response->assertStatus(422);
    }

    public function test_store_prevents_exceeding_turma_capacity(): void
    {
        $turmaLotada = Turma::factory()->create(['capacidade_maxima' => 1]);
        
        // Criar uma matrícula ativa para lotar a turma
        Matricula::factory()->create([
            'turma_id' => $turmaLotada->id,
            'status' => 'ativa'
        ]);

        $data = [
            'aluno_id' => $this->aluno->id,
            'turma_id' => $turmaLotada->id,
            'data_matricula' => now()->format('Y-m-d'),
            'status' => 'ativa'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('matriculas.store'), $data);

        $response->assertStatus(422);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('matriculas.store'), []);

        $response->assertSessionHasErrors([
            'aluno_id',
            'turma_id',
            'data_matricula',
            'status'
        ]);
    }

    public function test_store_validates_status_enum(): void
    {
        $data = [
            'aluno_id' => $this->aluno->id,
            'turma_id' => $this->turma->id,
            'data_matricula' => now()->format('Y-m-d'),
            'status' => 'status_invalido'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('matriculas.store'), $data);

        $response->assertSessionHasErrors(['status']);
    }

    public function test_show_displays_matricula(): void
    {
        $matricula = Matricula::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('matriculas.show', $matricula));

        $response->assertStatus(200)
            ->assertViewIs('admin.matriculas.show')
            ->assertViewHas('matricula');
    }

    public function test_edit_displays_form(): void
    {
        $matricula = Matricula::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('matriculas.edit', $matricula));

        $response->assertStatus(200)
            ->assertViewIs('admin.matriculas.edit')
            ->assertViewHas(['matricula', 'alunos', 'turmas']);
    }

    public function test_update_modifies_matricula_successfully(): void
    {
        $matricula = Matricula::factory()->create();
        
        $data = [
            'aluno_id' => $matricula->aluno_id,
            'turma_id' => $matricula->turma_id,
            'data_matricula' => now()->subDays(5)->format('Y-m-d'),
            'status' => 'inativa'
        ];

        $response = $this->actingAs($this->user)
            ->put(route('matriculas.update', $matricula), $data);

        $response->assertRedirect(route('matriculas.index'))
            ->assertSessionHas('success', 'Matrícula atualizada com sucesso!');

        $this->assertDatabaseHas('matriculas', [
            'id' => $matricula->id,
            'status' => 'inativa'
        ]);
    }

    public function test_destroy_removes_matricula(): void
    {
        $matricula = Matricula::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('matriculas.destroy', $matricula));

        $response->assertRedirect(route('matriculas.index'))
            ->assertSessionHas('success', 'Matrícula removida com sucesso!');

        $this->assertDatabaseMissing('matriculas', [
            'id' => $matricula->id
        ]);
    }

    public function test_por_turma_displays_matriculas_by_turma(): void
    {
        $matriculas = Matricula::factory()->count(3)->create([
            'turma_id' => $this->turma->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('matriculas.por-turma', $this->turma));

        $response->assertStatus(200)
            ->assertViewIs('admin.matriculas.por-turma')
            ->assertViewHas(['turma', 'matriculas']);
    }

    public function test_por_aluno_displays_matriculas_by_aluno(): void
    {
        $matriculas = Matricula::factory()->count(2)->create([
            'aluno_id' => $this->aluno->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('matriculas.por-aluno', $this->aluno));

        $response->assertStatus(200)
            ->assertViewIs('admin.matriculas.por-aluno')
            ->assertViewHas(['aluno', 'matriculas']);
    }

    public function test_guest_cannot_access_matriculas(): void
    {
        $matricula = Matricula::factory()->create();

        $this->get(route('matriculas.index'))->assertRedirect(route('login'));
        $this->get(route('matriculas.create'))->assertRedirect(route('login'));
        $this->post(route('matriculas.store'))->assertRedirect(route('login'));
        $this->get(route('matriculas.show', $matricula))->assertRedirect(route('login'));
        $this->get(route('matriculas.edit', $matricula))->assertRedirect(route('login'));
        $this->put(route('matriculas.update', $matricula))->assertRedirect(route('login'));
        $this->delete(route('matriculas.destroy', $matricula))->assertRedirect(route('login'));
    }

    public function test_capacity_check_ignores_inactive_matriculas(): void
    {
        $turmaLimitada = Turma::factory()->create(['capacidade_maxima' => 2]);
        
        // Criar matrículas inativas (não devem contar para capacidade)
        Matricula::factory()->count(2)->create([
            'turma_id' => $turmaLimitada->id,
            'status' => 'inativa'
        ]);

        $data = [
            'aluno_id' => $this->aluno->id,
            'turma_id' => $turmaLimitada->id,
            'data_matricula' => now()->format('Y-m-d'),
            'status' => 'ativa'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('matriculas.store'), $data);

        $response->assertRedirect(route('matriculas.index'))
            ->assertSessionHas('success');
    }
}
