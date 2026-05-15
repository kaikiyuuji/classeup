<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona role + FKs profissionais à tabela users.
 *
 * - role: enum-like string com default 'admin' (todos os usuários atuais
 *   viram admin no backfill).
 * - professor_id: FK opcional para vincular usuário ao perfil de professor.
 * - aluno_id: FK opcional para vincular usuário ao perfil de aluno.
 *
 * Constraints: nullOnDelete em ambas FKs para preservar usuário caso o
 * perfil seja removido.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('admin')->after('password');
                $table->index('role');
            }

            if (! Schema::hasColumn('users', 'professor_id')) {
                $table->foreignId('professor_id')
                    ->nullable()
                    ->after('role')
                    ->constrained('professores')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'aluno_id')) {
                $table->foreignId('aluno_id')
                    ->nullable()
                    ->after('professor_id')
                    ->constrained('alunos')
                    ->nullOnDelete();
            }
        });

        // Backfill: todos os usuários existentes viram admin.
        DB::table('users')->whereNull('role')->update(['role' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'aluno_id')) {
                $table->dropForeign(['aluno_id']);
                $table->dropColumn('aluno_id');
            }
            if (Schema::hasColumn('users', 'professor_id')) {
                $table->dropForeign(['professor_id']);
                $table->dropColumn('professor_id');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropIndex(['role']);
                $table->dropColumn('role');
            }
        });
    }
};
