<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Aluno;
use App\Models\Professor;
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
            'role' => Role::Admin,
            'professor_id' => null,
            'aluno_id' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => Role::Admin,
            'professor_id' => null,
            'aluno_id' => null,
        ]);
    }

    public function professor(?Professor $professor = null): static
    {
        return $this->state(fn () => [
            'role' => Role::Professor,
            'professor_id' => ($professor ?? Professor::factory()->create())->id,
            'aluno_id' => null,
        ]);
    }

    public function aluno(?Aluno $aluno = null): static
    {
        return $this->state(fn () => [
            'role' => Role::Aluno,
            'aluno_id' => ($aluno ?? Aluno::factory()->create())->id,
            'professor_id' => null,
        ]);
    }
}
