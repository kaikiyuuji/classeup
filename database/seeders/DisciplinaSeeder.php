<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Disciplina;
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
