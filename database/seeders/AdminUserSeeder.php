<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se já existe um usuário admin
        if (!User::where('tipo_usuario', 'admin')->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@classeup.com',
                'password' => Hash::make('admin123'),
                'tipo_usuario' => 'admin',
                'professor_id' => null,
                'aluno_id' => null,
                'email_verified_at' => now(),
            ]);

            $this->command->info('Usuário administrador criado com sucesso!');
            $this->command->info('Email: admin@classeup.com');
            $this->command->info('Senha: admin123');
        } else {
            $this->command->info('Usuário administrador já existe.');
        }
    }
}
