<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Turma>
 */
class TurmaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $series = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $turnos = ['matutino', 'vespertino', 'noturno'];
        $serie = $this->faker->randomElement($series);
        $turno = $this->faker->randomElement($turnos);
        
        return [
            'nome' => $serie . 'ª ' . ucfirst($turno) . ' - ' . $this->faker->randomLetter() . $this->faker->numberBetween(1, 3),
            'ano_letivo' => $this->faker->numberBetween(2023, 2025),
            'serie' => $serie,
            'turno' => $turno,
            'capacidade_maxima' => $this->faker->numberBetween(25, 40),
            'ativo' => $this->faker->boolean(85), // 85% de chance de estar ativa
        ];
    }
    
    /**
     * Indica que a turma está ativa.
     */
    public function ativa(): static
    {
        return $this->state(fn (array $attributes) => [
            'ativo' => true,
        ]);
    }
    
    /**
     * Indica que a turma está inativa.
     */
    public function inativa(): static
    {
        return $this->state(fn (array $attributes) => [
            'ativo' => false,
        ]);
    }
    
    /**
     * Define uma turma para uma série específica.
     */
    public function serie(int $serie): static
    {
        return $this->state(fn (array $attributes) => [
            'serie' => $serie,
            'nome' => $serie . 'ª ' . ucfirst($attributes['turno']) . ' - ' . $this->faker->randomLetter() . $this->faker->numberBetween(1, 3),
        ]);
    }
    
    /**
     * Define uma turma para um turno específico.
     */
    public function turno(string $turno): static
    {
        return $this->state(fn (array $attributes) => [
            'turno' => $turno,
            'nome' => $attributes['serie'] . 'ª ' . ucfirst($turno) . ' - ' . $this->faker->randomLetter() . $this->faker->numberBetween(1, 3),
        ]);
    }
}
