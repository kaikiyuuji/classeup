<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Professor;
use App\Models\Disciplina;
use App\Models\Turma;

class ProfessorDisciplinaTurmaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar dados existentes
        $professores = Professor::all();
        $disciplinas = Disciplina::all();
        $turmas = Turma::all();

        if ($professores->isEmpty() || $disciplinas->isEmpty() || $turmas->isEmpty()) {
            $this->command->warn('Certifique-se de que existem professores, disciplinas e turmas no banco de dados.');
            return;
        }

        // Criar associações variadas
        $associacoes = [
            // Professor 1 ensina Matemática na Turma A
            [
                'professor_id' => $professores->first()->id,
                'disciplina_id' => $disciplinas->where('nome', 'Matemática')->first()?->id ?? $disciplinas->first()->id,
                'turma_id' => $turmas->where('nome', 'Turma A')->first()?->id ?? $turmas->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Professor 2 ensina Português na Turma B
            [
                'professor_id' => $professores->skip(1)->first()?->id ?? $professores->first()->id,
                'disciplina_id' => $disciplinas->where('nome', 'Português')->first()?->id ?? $disciplinas->skip(1)->first()?->id ?? $disciplinas->first()->id,
                'turma_id' => $turmas->skip(1)->first()?->id ?? $turmas->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Professor 1 também ensina História na Turma B (mesmo professor, disciplina diferente)
            [
                'professor_id' => $professores->first()->id,
                'disciplina_id' => $disciplinas->where('nome', 'História')->first()?->id ?? $disciplinas->skip(2)->first()?->id ?? $disciplinas->first()->id,
                'turma_id' => $turmas->skip(1)->first()?->id ?? $turmas->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Adicionar mais associações se houver mais dados
        if ($professores->count() >= 3 && $disciplinas->count() >= 3 && $turmas->count() >= 3) {
            $associacoes[] = [
                'professor_id' => $professores->skip(2)->first()->id,
                'disciplina_id' => $disciplinas->skip(2)->first()->id,
                'turma_id' => $turmas->skip(2)->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Inserir as associações, evitando duplicatas
        foreach ($associacoes as $associacao) {
            DB::table('professor_disciplina_turma')->updateOrInsert(
                [
                    'professor_id' => $associacao['professor_id'],
                    'disciplina_id' => $associacao['disciplina_id'],
                    'turma_id' => $associacao['turma_id'],
                ],
                $associacao
            );
        }

        $this->command->info('Associações Professor-Disciplina-Turma criadas com sucesso!');
    }
}
