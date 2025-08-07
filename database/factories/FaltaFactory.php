<?php

namespace Database\Factories;

use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Professor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Falta>
 */
class FaltaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $justificada = $this->faker->boolean(30); // 30% de chance de ser justificada
        
        return [
            'matricula' => Aluno::factory(),
            'disciplina_id' => Disciplina::factory(),
            'professor_id' => Professor::factory(),
            'data_falta' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'justificada' => $justificada,
            'observacoes' => $justificada ? $this->faker->randomElement([
                'Consulta médica com atestado',
                'Problema familiar urgente',
                'Doença com atestado médico',
                'Compromisso judicial',
                'Problema de transporte público',
                'Participação em evento escolar'
            ]) : null,
        ];
    }
    
    /**
     * Indica que a falta está justificada.
     */
    public function justificada(): static
    {
        return $this->state(fn (array $attributes) => [
            'justificada' => true,
            'observacoes' => $this->faker->randomElement([
                'Consulta médica com atestado',
                'Problema familiar urgente',
                'Doença com atestado médico',
                'Compromisso judicial',
                'Problema de transporte público'
            ])
        ]);
    }
    
    /**
     * Indica que a falta não está justificada.
     */
    public function naoJustificada(): static
    {
        return $this->state(fn (array $attributes) => [
            'justificada' => false,
            'observacoes' => null
        ]);
    }
    
    /**
     * Define uma data específica para a falta.
     */
    public function naData(string $data): static
    {
        return $this->state(fn (array $attributes) => [
            'data_falta' => $data
        ]);
    }
}
