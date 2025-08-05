<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Disciplina>
 */
class DisciplinaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $disciplinas = [
            ['nome' => 'Matemática', 'codigo' => 'MAT'],
            ['nome' => 'Português', 'codigo' => 'POR'],
            ['nome' => 'História', 'codigo' => 'HIS'],
            ['nome' => 'Geografia', 'codigo' => 'GEO'],
            ['nome' => 'Ciências', 'codigo' => 'CIE'],
            ['nome' => 'Física', 'codigo' => 'FIS'],
            ['nome' => 'Química', 'codigo' => 'QUI'],
            ['nome' => 'Biologia', 'codigo' => 'BIO'],
            ['nome' => 'Inglês', 'codigo' => 'ING'],
            ['nome' => 'Educação Física', 'codigo' => 'EDF'],
            ['nome' => 'Artes', 'codigo' => 'ART'],
            ['nome' => 'Filosofia', 'codigo' => 'FIL'],
            ['nome' => 'Sociologia', 'codigo' => 'SOC'],
            ['nome' => 'Literatura', 'codigo' => 'LIT'],
            ['nome' => 'Redação', 'codigo' => 'RED'],
        ];

        $disciplina = $this->faker->randomElement($disciplinas);
        $codigo = $disciplina['codigo'] . $this->faker->numberBetween(100, 999);

        return [
            'nome' => $disciplina['nome'],
            'codigo' => $codigo,
            'descricao' => $this->faker->optional(0.7)->paragraph(),
            'carga_horaria' => $this->faker->randomElement([40, 60, 80, 120, 160]),
            'ativo' => $this->faker->boolean(85), // 85% chance de estar ativo
        ];
    }
}
