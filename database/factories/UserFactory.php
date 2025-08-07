<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

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
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'tipo_usuario' => 'admin',
            'professor_id' => null,
            'aluno_id' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_usuario' => 'admin',
            'professor_id' => null,
            'aluno_id' => null,
        ]);
    }

    /**
     * Create a professor user.
     */
    public function professor(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_usuario' => 'professor',
            'aluno_id' => null,
        ]);
    }

    /**
     * Create an aluno user.
     */
    public function aluno(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_usuario' => 'aluno',
            'professor_id' => null,
        ]);
    }
}
