<?php

namespace Database\Seeders;

use App\Models\Aluno;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlunoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria 20 alunos aleatórios
        Aluno::factory(20)->create();
        
        // Cria alguns alunos específicos para demonstração
        Aluno::factory()->create([
            'nome' => 'João Silva',
            'email' => 'joao.silva@exemplo.com',
            'cpf' => '12345678901',
        ]);
        
        Aluno::factory()->create([
            'nome' => 'Maria Santos',
            'email' => 'maria.santos@exemplo.com',
            'cpf' => '98765432109',
        ]);
        
        // Cria alguns alunos inativos
        Aluno::factory(3)->inactive()->create();
    }
}
