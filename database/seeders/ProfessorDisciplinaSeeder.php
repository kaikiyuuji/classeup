<?php

namespace Database\Seeders;

use App\Models\Disciplina;
use App\Models\Professor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfessorDisciplinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $professores = Professor::all();
        $disciplinas = Disciplina::all();
        
        if ($professores->isEmpty() || $disciplinas->isEmpty()) {
            $this->command->warn('Não há professores ou disciplinas suficientes para criar associações.');
            return;
        }
        
        // Associar cada professor a 1-3 disciplinas aleatórias
        foreach ($professores as $professor) {
            $quantidadeDisciplinas = rand(1, min(3, $disciplinas->count()));
            $disciplinasAleatorias = $disciplinas->random($quantidadeDisciplinas);
            
            foreach ($disciplinasAleatorias as $disciplina) {
                // Verificar se a associação já existe
                if (!$professor->disciplinas()->where('disciplina_id', $disciplina->id)->exists()) {
                    $professor->disciplinas()->attach($disciplina->id);
                }
            }
        }
        
        $this->command->info('Associações Professor-Disciplina criadas com sucesso!');
    }
}
