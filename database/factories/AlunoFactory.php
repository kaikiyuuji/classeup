<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aluno>
 */
class AlunoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_matricula' => fake()->unique()->numberBetween(2024001, 2024999),
            'data_matricula' => fake()->date('Y-m-d', 'now'),
            'status_matricula' => fake()->randomElement(['ativo', 'inativo', 'transferido']),
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->numerify('###.###.###-##'),
            'data_nascimento' => fake()->date('Y-m-d', '2010-01-01'),
            'telefone' => fake()->phoneNumber(),
            'endereco' => fake()->address(),
            'foto_perfil' => null,
            'turma_id' => null, // Será definido quando necessário
        ];
    }

    /**
     * Indicate that the student is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_matricula' => 'inativo',
        ]);
    }

    /**
     * Indicate that the student is transferred.
     */
    public function transferred(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_matricula' => 'transferido',
        ]);
    }
}
