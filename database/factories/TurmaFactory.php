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
        $turnos = ['matutino', 'vespertino', 'noturno'];
        $turno = $this->faker->randomElement($turnos);

        // Ano do ensino médio: 1º, 2º ou 3º
        $ano = $this->faker->numberBetween(1, 3);

        // Letra da turma: A, B ou C
        $letra = chr($this->faker->numberBetween(65, 67));

        return [
            'nome' => "{$ano}º Ano {$letra}", // Exemplo: 1º Ano A, 2º Ano B...
            'ano_letivo' => $this->faker->numberBetween(2023, 2025),
            'serie' => 'médio',
            'turno' => $turno,
            'capacidade_maxima' => $this->faker->numberBetween(25, 40),
            'ativo' => $this->faker->boolean(85),
        ];
    }
}
