<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Disciplina;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Avaliacao>
 */
class AvaliacaoFactory extends Factory
{
    protected $model = Avaliacao::class;

    public function definition(): array
    {
        return [
            'aluno_id' => Aluno::factory(),
            'disciplina_id' => Disciplina::factory(),
            'av1' => 0,
            'av2' => 0,
            'av3' => 0,
            'av4' => 0,
            'substitutiva' => null,
            'recuperacao_final' => null,
            'nota_final' => 0,
            'situacao' => 'em_andamento',
        ];
    }

    public function aprovado(): static
    {
        return $this->state(fn () => [
            'av1' => 7,
            'av2' => 8,
            'av3' => 7,
            'av4' => 8,
            'nota_final' => 7.5,
            'situacao' => 'aprovado',
        ]);
    }

    public function reprovado(): static
    {
        return $this->state(fn () => [
            'av1' => 3,
            'av2' => 4,
            'av3' => 5,
            'av4' => 4,
            'nota_final' => 4.0,
            'situacao' => 'reprovado',
        ]);
    }
}
