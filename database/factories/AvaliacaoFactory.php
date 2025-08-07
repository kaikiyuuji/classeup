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
            'av1' => $this->faker->randomFloat(2, 0, 10),
            'av2' => $this->faker->randomFloat(2, 0, 10),
            'av3' => $this->faker->randomFloat(2, 0, 10),
            'av4' => $this->faker->randomFloat(2, 0, 10),
            'substitutiva' => $this->faker->optional(0.3)->randomFloat(2, 0, 10),
            'recuperacao_final' => $this->faker->optional(0.2)->randomFloat(2, 0, 10),
            'nota_final' => 0,
            'situacao' => 'em_andamento',
        ];
    }

    /**
     * Estado para aluno aprovado
     */
    public function aprovado(): static
    {
        return $this->state(fn (array $attributes) => [
            'av1' => $this->faker->randomFloat(2, 6, 10),
            'av2' => $this->faker->randomFloat(2, 6, 10),
            'av3' => $this->faker->randomFloat(2, 6, 10),
            'av4' => $this->faker->randomFloat(2, 6, 10),
            'situacao' => 'aprovado',
        ]);
    }

    /**
     * Estado para aluno reprovado
     */
    public function reprovado(): static
    {
        return $this->state(fn (array $attributes) => [
            'av1' => $this->faker->randomFloat(2, 0, 5),
            'av2' => $this->faker->randomFloat(2, 0, 5),
            'av3' => $this->faker->randomFloat(2, 0, 5),
            'av4' => $this->faker->randomFloat(2, 0, 5),
            'situacao' => 'reprovado',
        ]);
    }
}
