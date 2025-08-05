<?php

namespace Database\Seeders;

use App\Models\Professor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfessorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar 15 professores usando a factory
        Professor::factory(15)->create();

        // Criar alguns professores específicos para demonstração
        Professor::factory()->create([
            'nome' => 'Dr. João Silva',
            'email' => 'joao.silva@classeup.com',
            'cpf' => '12345678901',
            'telefone' => '(11) 99999-1234',
            'especialidade' => 'Matemática',
            'formacao' => 'Doutorado em Matemática - USP, Mestrado em Educação Matemática - UNICAMP',
            'ativo' => true,
        ]);

        Professor::factory()->create([
            'nome' => 'Profa. Maria Santos',
            'email' => 'maria.santos@classeup.com',
            'cpf' => '98765432109',
            'telefone' => '(11) 88888-5678',
            'especialidade' => 'Português',
            'formacao' => 'Mestrado em Letras - USP, Especialização em Literatura Brasileira - PUC-SP',
            'ativo' => true,
        ]);

        Professor::factory()->create([
            'nome' => 'Prof. Carlos Oliveira',
            'email' => 'carlos.oliveira@classeup.com',
            'cpf' => '11122233344',
            'telefone' => '(11) 77777-9012',
            'especialidade' => 'História',
            'formacao' => 'Licenciatura em História - UFRJ, Especialização em História do Brasil - FGV',
            'ativo' => false, // Professor inativo para demonstrar o filtro
        ]);
    }
}
