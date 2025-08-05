<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Aluno;
use App\Models\Turma;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Matricula>
 */
class MatriculaFactory extends Factory
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
            'turma_id' => Turma::factory(),
            'data_matricula' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->randomElement(['ativa', 'inativa', 'transferida', 'cancelada']),
        ];
    }

    /**
     * Indicate that the matricula is active.
     */
    public function ativa(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ativa',
        ]);
    }

    /**
     * Indicate that the matricula is inactive.
     */
    public function inativa(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inativa',
        ]);
    }

    /**
     * Indicate that the matricula is transferred.
     */
    public function transferida(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'transferida',
        ]);
    }

    /**
     * Indicate that the matricula is cancelled.
     */
    public function cancelada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelada',
        ]);
    }

    /**
     * Indicate that the matricula was created recently.
     */
    public function recente(): static
    {
        return $this->state(fn (array $attributes) => [
            'data_matricula' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }
}
