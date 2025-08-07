<?php

namespace Database\Seeders;

use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Falta;
use App\Models\Professor;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaltaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter alguns alunos, disciplinas e professores existentes
        $alunos = Aluno::limit(5)->get();
        $disciplinas = Disciplina::limit(3)->get();
        $professores = Professor::limit(3)->get();
        
        if ($alunos->isEmpty() || $disciplinas->isEmpty() || $professores->isEmpty()) {
            $this->command->warn('É necessário ter alunos, disciplinas e professores cadastrados antes de executar este seeder.');
            return;
        }
        
        // Criar faltas dos últimos 30 dias
        $dataInicio = Carbon::now()->subDays(30);
        $dataFim = Carbon::now();
        
        foreach ($alunos as $aluno) {
            // Cada aluno terá entre 2 a 8 faltas no período
            $numeroFaltas = rand(2, 8);
            
            for ($i = 0; $i < $numeroFaltas; $i++) {
                $dataFalta = Carbon::createFromTimestamp(
                    rand($dataInicio->timestamp, $dataFim->timestamp)
                )->startOfDay();
                
                $disciplina = $disciplinas->random();
                $professor = $professores->random();
                
                // 70% das faltas não são justificadas
                $justificada = rand(1, 10) <= 3;
                
                $observacoes = null;
                if ($justificada) {
                    $motivosJustificativa = [
                        'Consulta médica com atestado',
                        'Problema familiar urgente',
                        'Doença com atestado médico',
                        'Compromisso judicial',
                        'Problema de transporte público'
                    ];
                    $observacoes = $motivosJustificativa[array_rand($motivosJustificativa)];
                }
                
                // Verificar se já existe uma falta para este aluno nesta data e disciplina
                $faltaExistente = Falta::where('matricula', $aluno->numero_matricula)
                    ->where('disciplina_id', $disciplina->id)
                    ->where('data_falta', $dataFalta)
                    ->exists();
                
                if (!$faltaExistente) {
                    Falta::create([
                        'matricula' => $aluno->numero_matricula,
                        'disciplina_id' => $disciplina->id,
                        'professor_id' => $professor->id,
                        'data_falta' => $dataFalta,
                        'justificada' => $justificada,
                        'observacoes' => $observacoes
                    ]);
                }
            }
        }
        
        $this->command->info('Faltas de exemplo criadas com sucesso!');
    }
}
