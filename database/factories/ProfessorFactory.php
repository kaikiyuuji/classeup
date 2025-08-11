<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Professor>
 */
class ProfessorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->numerify('###.###.###-##'),
            'data_nascimento' => fake()->date('Y-m-d', '1980-01-01'),
            'telefone' => fake()->phoneNumber(),
            'endereco' => fake()->address(),
            'especialidade' => fake()->randomElement(['Matemática', 'Português', 'História', 'Geografia', 'Ciências']),
            'formacao' => fake()->randomElement(['Licenciatura', 'Bacharelado', 'Mestrado', 'Doutorado']),
            'foto_perfil' => null,
            'ativo' => true,
        ];
    }

    /**
     * Indicate that the professor is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'ativo' => false,
        ]);
    }
}
