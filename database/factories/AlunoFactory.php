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
            'nome' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'cpf' => $this->generateCpf(),
            'data_nascimento' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'telefone' => $this->faker->numerify('(##) #####-####'),
            'endereco' => $this->faker->address(),
            'foto_perfil' => null,
            'ativo' => $this->faker->boolean(85), // 85% chance de estar ativo
        ];
    }

    /**
     * Generate a valid CPF number.
     */
    private function generateCpf(): string
    {
        // Gera os 9 primeiros dígitos
        $cpf = '';
        for ($i = 0; $i < 9; $i++) {
            $cpf .= $this->faker->numberBetween(0, 9);
        }

        // Calcula o primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;
        $cpf .= $digit1;

        // Calcula o segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;
        $cpf .= $digit2;

        return $cpf;
    }

    /**
     * Indicate that the aluno is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'ativo' => false,
        ]);
    }

    /**
     * Indicate that the aluno is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'ativo' => true,
        ]);
    }
}
