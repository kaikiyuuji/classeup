<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona colunas de auditoria (created_by, updated_by, deleted_by)
 * em todas as entidades de domínio.
 *
 * FK para users.id (nullable, onDelete nullOnDelete). Registros antigos
 * permanecem com NULL — preenchimento começa daqui em diante via
 * App\Observers\AuditObserver.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['alunos', 'professores', 'turmas', 'disciplinas', 'avaliacoes', 'faltas'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->foreignId('created_by')
                    ->nullable()
                    ->after('updated_at')
                    ->constrained('users')
                    ->nullOnDelete();

                $blueprint->foreignId('updated_by')
                    ->nullable()
                    ->after('created_by')
                    ->constrained('users')
                    ->nullOnDelete();

                $blueprint->foreignId('deleted_by')
                    ->nullable()
                    ->after('updated_by')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['alunos', 'professores', 'turmas', 'disciplinas', 'avaliacoes', 'faltas'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropForeign(['created_by']);
                $blueprint->dropColumn('created_by');
                $blueprint->dropForeign(['updated_by']);
                $blueprint->dropColumn('updated_by');
                $blueprint->dropForeign(['deleted_by']);
                $blueprint->dropColumn('deleted_by');
            });
        }
    }
};
