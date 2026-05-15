<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona aluno_id (FK) à tabela faltas e popula a partir da coluna legacy
 * `matricula` (string que referenciava `alunos.numero_matricula`).
 *
 * A coluna `matricula` é mantida soft-deprecated nesta fase para não quebrar
 * código que ainda usa. Drop só na Fase 5, após todas as faltas estarem
 * populadas com aluno_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faltas', function (Blueprint $table): void {
            if (! Schema::hasColumn('faltas', 'aluno_id')) {
                $table->foreignId('aluno_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('alunos')
                    ->nullOnDelete();
            }
        });

        // Popula aluno_id a partir de matricula → numero_matricula
        $updated = DB::statement(<<<'SQL'
            UPDATE faltas
            SET aluno_id = (
                SELECT alunos.id
                FROM alunos
                WHERE alunos.numero_matricula = faltas.matricula
                LIMIT 1
            )
            WHERE aluno_id IS NULL
        SQL);

        // Log órfãos para diagnóstico
        $orfaos = DB::table('faltas')->whereNull('aluno_id')->count();
        if ($orfaos > 0) {
            Log::warning(
                "Falta migration: {$orfaos} registros sem aluno_id após backfill (matrícula órfã ou aluno deletado)."
            );
        }
    }

    public function down(): void
    {
        Schema::table('faltas', function (Blueprint $table): void {
            $table->dropForeign(['aluno_id']);
            $table->dropColumn('aluno_id');
        });
    }
};
