<?php

namespace Database\Seeders;

use App\Models\Turma;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TurmaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar turmas específicas para cada nível educacional e turno
        $niveisEducacionais = [
            'pré-escola' => 'Pré-Escola',
            'fundamental' => 'Fundamental', 
            'médio' => 'Médio'
        ];
        $turnos = ['matutino', 'vespertino', 'noturno'];
        $anoAtual = date('Y');
        
        foreach ($niveisEducacionais as $nivel => $nomeNivel) {
            foreach ($turnos as $turno) {
                // Criar 3 turmas por nível/turno
                for ($i = 1; $i <= 3; $i++) {
                    Turma::create([
                        'nome' => $nomeNivel . ' ' . ucfirst($turno) . ' - ' . chr(64 + $i), // A, B, C...
                        'ano_letivo' => $anoAtual,
                        'serie' => $nivel,
                        'turno' => $turno,
                        'capacidade_maxima' => rand(25, 35),
                        'ativo' => true,
                    ]);
                }
            }
        }
        
        // Criar algumas turmas do ano anterior (inativas)
        Turma::factory()
            ->count(10)
            ->state([
                'ano_letivo' => $anoAtual - 1,
                'ativo' => false,
            ])
            ->create();
        
        // Criar algumas turmas aleatórias adicionais
        Turma::factory()
            ->count(15)
            ->create();
    }
}
