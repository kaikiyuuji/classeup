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
        // Criar turmas específicas para cada série e turno
        $series = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $turnos = ['matutino', 'vespertino', 'noturno'];
        $anoAtual = date('Y');
        
        foreach ($series as $serie) {
            foreach ($turnos as $turno) {
                // Criar 2 turmas por série/turno
                for ($i = 1; $i <= 2; $i++) {
                    Turma::create([
                        'nome' => $serie . 'ª ' . ucfirst($turno) . ' - ' . chr(64 + $i), // A, B, C...
                        'ano_letivo' => $anoAtual,
                        'serie' => $serie,
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
