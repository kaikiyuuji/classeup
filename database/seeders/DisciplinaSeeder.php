<?php

namespace Database\Seeders;

use App\Models\Disciplina;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DisciplinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar 15 disciplinas usando o factory
        Disciplina::factory()->count(15)->create();
    }
}
