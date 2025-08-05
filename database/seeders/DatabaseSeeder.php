<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário de teste
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Executar seeders específicos
        $this->call([
            AlunoSeeder::class,
            ProfessorSeeder::class,
            DisciplinaSeeder::class,
            TurmaSeeder::class,
        ]);
    }
}
