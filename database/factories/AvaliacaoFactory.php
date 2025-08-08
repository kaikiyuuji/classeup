<?php

namespace Database\Factories;

use App\Models\Aluno;
use App\Models\Disciplina;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Avaliacao>
 */
class AvaliacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'aluno_id' => Aluno::factory(),
            'disciplina_id' => Disciplina::factory(),
            'av1' => null,
            'av2' => null,
            'av3' => null,
            'av4' => null,
            'substitutiva' => null,
            'recuperacao_final' => null,
            'nota_final' => 0,
            'situacao' => 'em_andamento',
        ];
    }

    /**
     * Indicate that the avaliacao is aprovado.
     */
    public function aprovado(): static
    {
        return $this->state(fn (array $attributes) => [
            'av1' => 8.0,
            'av2' => 7.5,
            'av3' => 8.5,
            'av4' => 9.0,
            'nota_final' => 8.25,
            'situacao' => 'aprovado',
        ]);
    }

    /**
     * Indicate that the avaliacao is reprovado.
     */
    public function reprovado(): static
    {
        return $this->state(fn (array $attributes) => [
            'av1' => 4.0,
            'av2' => 3.5,
            'av3' => 5.0,
            'av4' => 4.5,
            'nota_final' => 4.25,
            'situacao' => 'reprovado',
        ]);
    }

    /**
     * Indicate that the avaliacao belongs to specific models.
     */
    public function paraAluno(Aluno $aluno, Disciplina $disciplina): static
    {
        return $this->state(fn (array $attributes) => [
            'aluno_id' => $aluno->id,
            'disciplina_id' => $disciplina->id,
        ]);
    }
}
