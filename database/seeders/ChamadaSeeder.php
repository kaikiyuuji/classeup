<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chamada;
use App\Models\Professor;
use App\Models\Disciplina;
use App\Models\Aluno;
use Carbon\Carbon;

class ChamadaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar dados existentes
        $professor = Professor::first();
        $disciplina = Disciplina::first();
        $alunos = Aluno::take(5)->get();
        
        if (!$professor || !$disciplina || $alunos->isEmpty()) {
            $this->command->warn('Certifique-se de que existem professores, disciplinas e alunos no banco de dados.');
            return;
        }
        
        // Criar chamadas dos últimos 7 dias
        for ($i = 0; $i < 7; $i++) {
            $data = Carbon::today()->subDays($i);
            
            foreach ($alunos as $aluno) {
                // 80% de chance de presença
                $status = rand(1, 100) <= 80 ? 'presente' : 'falta';
                
                Chamada::create([
                    'matricula' => $aluno->numero_matricula,
                    'disciplina_id' => $disciplina->id,
                    'professor_id' => $professor->id,
                    'data_chamada' => $data,
                    'status' => $status,
                    'justificada' => $status === 'falta' ? (rand(1, 100) <= 30) : false,
                    'observacoes' => $status === 'falta' && rand(1, 100) <= 50 ? 'Falta registrada automaticamente' : null
                ]);
            }
        }
        
        $this->command->info('Chamadas de teste criadas com sucesso!');
    }
}
