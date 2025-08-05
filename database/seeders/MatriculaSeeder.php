<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Matricula;
use App\Models\Aluno;
use App\Models\Turma;
use Carbon\Carbon;

class MatriculaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar dados existentes
        $alunos = Aluno::all();
        $turmas = Turma::all();

        if ($alunos->isEmpty() || $turmas->isEmpty()) {
            $this->command->warn('Certifique-se de que existem alunos e turmas no banco de dados.');
            return;
        }

        // Criar matrículas variadas
        $matriculas = [];
        
        // Matricular alguns alunos em diferentes turmas
        foreach ($alunos->take(10) as $index => $aluno) {
            $turma = $turmas->get($index % $turmas->count());
            
            $matriculas[] = [
                'aluno_id' => $aluno->id,
                'turma_id' => $turma->id,
                'data_matricula' => Carbon::now()->subDays(rand(1, 30)),
                'status' => $this->getRandomStatus(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Adicionar algumas matrículas extras se houver dados suficientes
        if ($alunos->count() > 5 && $turmas->count() > 1) {
            // Alguns alunos podem estar em múltiplas turmas
            for ($i = 0; $i < min(5, $alunos->count()); $i++) {
                $aluno = $alunos->random();
                $turma = $turmas->random();
                
                // Verificar se já existe essa combinação
                $exists = collect($matriculas)->contains(function ($matricula) use ($aluno, $turma) {
                    return $matricula['aluno_id'] === $aluno->id && $matricula['turma_id'] === $turma->id;
                });
                
                if (!$exists) {
                    $matriculas[] = [
                        'aluno_id' => $aluno->id,
                        'turma_id' => $turma->id,
                        'data_matricula' => Carbon::now()->subDays(rand(1, 60)),
                        'status' => $this->getRandomStatus(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Inserir as matrículas
        foreach ($matriculas as $matricula) {
            Matricula::updateOrCreate(
                [
                    'aluno_id' => $matricula['aluno_id'],
                    'turma_id' => $matricula['turma_id'],
                ],
                $matricula
            );
        }

        $this->command->info('Matrículas criadas com sucesso! Total: ' . count($matriculas));
    }

    /**
     * Retorna um status aleatório para a matrícula
     */
    private function getRandomStatus(): string
    {
        $statuses = ['ativa', 'inativa', 'transferida', 'cancelada'];
        $weights = [60, 20, 15, 5]; // 60% ativa, 20% inativa, 15% transferida, 5% cancelada
        
        $random = rand(1, 100);
        
        if ($random <= $weights[0]) {
            return $statuses[0];
        } elseif ($random <= $weights[0] + $weights[1]) {
            return $statuses[1];
        } elseif ($random <= $weights[0] + $weights[1] + $weights[2]) {
            return $statuses[2];
        } else {
            return $statuses[3];
        }
    }
}
