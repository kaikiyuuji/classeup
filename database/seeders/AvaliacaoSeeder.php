<?php

namespace Database\Seeders;

use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Disciplina;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvaliacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar alunos que estão vinculados a turmas
        $alunos = Aluno::whereNotNull('turma_id')->get();
        
        foreach ($alunos as $aluno) {
            // Buscar disciplinas da turma do aluno
            $disciplinas = $aluno->turma->disciplinas();
            
            if ($disciplinas->exists()) {
                foreach ($disciplinas->get() as $disciplina) {
                    // Verificar se já existe avaliação para este aluno e disciplina
                    $avaliacaoExistente = Avaliacao::where('aluno_id', $aluno->id)
                        ->where('disciplina_id', $disciplina->id)
                        ->first();
                    
                    if (!$avaliacaoExistente) {
                        // Criar avaliação com notas aleatórias
                        $avaliacao = Avaliacao::factory()->create([
                            'aluno_id' => $aluno->id,
                            'disciplina_id' => $disciplina->id,
                        ]);
                        
                        // Calcular nota final
                        $avaliacao->calcularNotaFinal();
                    }
                }
            }
        }
        
        $this->command->info('Avaliações criadas com sucesso!');
    }
}
