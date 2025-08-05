<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Professor>
 */
class ProfessorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $especialidades = [
            'Matemática',
            'Português',
            'História',
            'Geografia',
            'Ciências',
            'Física',
            'Química',
            'Biologia',
            'Inglês',
            'Educação Física',
            'Artes',
            'Filosofia',
            'Sociologia'
        ];

        $formacoes = [
            'Licenciatura em Matemática - UFMG',
            'Licenciatura em Letras - USP',
            'Licenciatura em História - UFRJ',
            'Licenciatura em Geografia - UFPR',
            'Licenciatura em Ciências Biológicas - UNICAMP',
            'Licenciatura em Física - UFRGS',
            'Licenciatura em Química - UFBA',
            'Licenciatura em Educação Física - UNESP',
            'Licenciatura em Artes Visuais - UFSC',
            'Bacharelado e Licenciatura em Filosofia - PUC-SP'
        ];

        return [
            'nome' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'cpf' => $this->generateCpf(),
            'data_nascimento' => $this->faker->dateTimeBetween('-65 years', '-25 years')->format('Y-m-d'),
            'telefone' => $this->faker->numerify('(##) #####-####'),
            'endereco' => $this->faker->address(),
            'especialidade' => $this->faker->randomElement($especialidades),
            'formacao' => $this->faker->randomElement($formacoes),
            'foto_perfil' => null,
            'ativo' => $this->faker->boolean(85), // 85% chance de estar ativo
        ];
    }

    /**
     * Generate a valid CPF number.
     */
    private function generateCpf(): string
    {
        // Generate 9 random digits
        $cpf = '';
        for ($i = 0; $i < 9; $i++) {
            $cpf .= mt_rand(0, 9);
        }

        // Calculate first verification digit
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;
        $cpf .= $digit1;

        // Calculate second verification digit
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
     * Indicate that the professor is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'ativo' => false,
        ]);
    }
}
