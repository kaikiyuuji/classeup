<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Stub para Fase 2: state será expandido para popular `users.role` e FK `professor_id`/`aluno_id`.
     */
    public function admin(): static
    {
        return $this->state(fn () => []);
    }

    /**
     * Stub para Fase 2.
     */
    public function professor(): static
    {
        return $this->state(fn () => []);
    }

    /**
     * Stub para Fase 2.
     */
    public function aluno(): static
    {
        return $this->state(fn () => []);
    }
}
