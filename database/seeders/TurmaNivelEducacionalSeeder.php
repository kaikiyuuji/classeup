<?php

namespace Database\Seeders;

use App\Models\Turma;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TurmaNivelEducacionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar turmas de exemplo para cada nível educacional
        $niveisEducacionais = Turma::getNiveisEducacionais();
        $turnos = ['matutino', 'vespertino', 'noturno'];
        
        foreach ($niveisEducacionais as $nivel => $nomeNivel) {
            foreach ($turnos as $turno) {
                Turma::create([
                    'nome' => $nomeNivel . ' ' . ucfirst($turno) . ' - A',
                    'ano_letivo' => 2024,
                    'serie' => $nivel,
                    'turno' => $turno,
                    'capacidade_maxima' => 30,
                    'ativo' => true,
                ]);
            }
        }
        
        // Criar algumas turmas adicionais
        Turma::create([
            'nome' => 'Pré-escola Matutino - B',
            'ano_letivo' => 2024,
            'serie' => Turma::NIVEL_PRE_ESCOLA,
            'turno' => 'matutino',
            'capacidade_maxima' => 25,
            'ativo' => true,
        ]);
        
        Turma::create([
            'nome' => 'Fundamental Vespertino - B',
            'ano_letivo' => 2024,
            'serie' => Turma::NIVEL_FUNDAMENTAL,
            'turno' => 'vespertino',
            'capacidade_maxima' => 35,
            'ativo' => true,
        ]);
        
        Turma::create([
            'nome' => 'Médio Noturno - B',
            'ano_letivo' => 2024,
            'serie' => Turma::NIVEL_MEDIO,
            'turno' => 'noturno',
            'capacidade_maxima' => 40,
            'ativo' => true,
        ]);
    }
}
