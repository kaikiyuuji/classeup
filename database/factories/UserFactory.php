<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'tipo_usuario' => 'admin',
            'professor_id' => null,
            'aluno_id' => null,
        ];
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_usuario' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is a professor.
     */
    public function professor(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_usuario' => 'professor',
        ]);
    }

    /**
     * Indicate that the user is a student.
     */
    public function aluno(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_usuario' => 'aluno',
        ]);
    }
}
