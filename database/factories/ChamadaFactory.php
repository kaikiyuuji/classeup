<?php

namespace Database\Factories;

use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Professor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chamada>
 */
class ChamadaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['presente', 'falta']);
        $justificada = $status === 'falta' ? $this->faker->boolean(30) : false; // 30% de chance de falta ser justificada
        
        return [
            'matricula' => Aluno::factory(),
            'disciplina_id' => Disciplina::factory(),
            'professor_id' => Professor::factory(),
            'data_chamada' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'status' => $status,
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
     * Indica que o aluno estava presente.
     */
    public function presente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'presente',
            'justificada' => false,
            'observacoes' => null,
        ]);
    }
    
    /**
     * Indica que o aluno faltou.
     */
    public function falta(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'falta',
            'justificada' => false,
            'observacoes' => null,
        ]);
    }
    
    /**
     * Indica que a falta está justificada.
     */
    public function justificada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'falta',
            'justificada' => true,
            'observacoes' => $this->faker->randomElement([
                'Consulta médica com atestado',
                'Problema familiar urgente',
                'Doença com atestado médico',
                'Compromisso judicial',
                'Problema de transporte público',
                'Participação em evento escolar'
            ]),
        ]);
    }
    
    /**
     * Indica que a falta não está justificada.
     */
    public function naoJustificada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'falta',
            'justificada' => false,
            'observacoes' => null,
        ]);
    }
    
    /**
     * Define uma data específica para a chamada.
     */
    public function naData(string $data): static
    {
        return $this->state(fn (array $attributes) => [
            'data_chamada' => $data
        ]);
    }
}
