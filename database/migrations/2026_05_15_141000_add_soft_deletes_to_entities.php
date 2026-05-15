<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona soft delete (coluna deleted_at) nas entidades do domínio.
 *
 * Sistema escolar precisa preservar histórico: aluno deletado é arquivado,
 * não removido. Boletins, faltas e avaliações continuam consultáveis
 * mesmo quando o aluno saiu da escola.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['alunos', 'professores', 'turmas', 'disciplinas', 'avaliacoes', 'faltas'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach (['alunos', 'professores', 'turmas', 'disciplinas', 'avaliacoes', 'faltas'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropSoftDeletes();
            });
        }
    }
};
