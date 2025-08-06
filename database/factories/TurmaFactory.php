<?php

namespace Database\Factories;

use App\Models\Turma;
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
        $niveisEducacionais = array_keys(Turma::getNiveisEducacionais());
        $turnos = ['matutino', 'vespertino', 'noturno'];
        $nivel = $this->faker->randomElement($niveisEducacionais);
        $turno = $this->faker->randomElement($turnos);
        
        $nomeNivel = Turma::getNiveisEducacionais()[$nivel];
        
        return [
            'nome' => $nomeNivel . ' ' . ucfirst($turno) . ' - ' . $this->faker->randomLetter() . $this->faker->numberBetween(1, 3),
            'ano_letivo' => $this->faker->numberBetween(2023, 2025),
            'serie' => $nivel,
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
     * Define uma turma para um nível educacional específico.
     */
    public function nivel(string $nivel): static
    {
        $nomeNivel = Turma::getNiveisEducacionais()[$nivel] ?? $nivel;
        return $this->state(fn (array $attributes) => [
            'serie' => $nivel,
            'nome' => $nomeNivel . ' ' . ucfirst($attributes['turno']) . ' - ' . $this->faker->randomLetter() . $this->faker->numberBetween(1, 3),
        ]);
    }
    
    /**
     * Define uma turma para um turno específico.
     */
    public function turno(string $turno): static
    {
        return $this->state(fn (array $attributes) => [
            'turno' => $turno,
            'nome' => Turma::getNiveisEducacionais()[$attributes['serie']] . ' ' . ucfirst($turno) . ' - ' . $this->faker->randomLetter() . $this->faker->numberBetween(1, 3),
        ]);
    }
}
