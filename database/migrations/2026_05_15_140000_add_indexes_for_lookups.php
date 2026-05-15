<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona índices em colunas frequentemente consultadas mas sem índice
 * (lookups por CPF, número de matrícula, código de disciplina, data de
 * falta, FK aluno_id em faltas).
 *
 * Não adiciona índice em `users.email` (já é unique) ou em PKs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alunos', function (Blueprint $table): void {
            $table->index('cpf');
            $table->index('numero_matricula');
        });

        Schema::table('professores', function (Blueprint $table): void {
            $table->index('cpf');
        });

        Schema::table('disciplinas', function (Blueprint $table): void {
            $table->index('codigo');
        });

        Schema::table('faltas', function (Blueprint $table): void {
            $table->index('data_falta');
        });
    }

    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table): void {
            $table->dropIndex(['cpf']);
            $table->dropIndex(['numero_matricula']);
        });

        Schema::table('professores', function (Blueprint $table): void {
            $table->dropIndex(['cpf']);
        });

        Schema::table('disciplinas', function (Blueprint $table): void {
            $table->dropIndex(['codigo']);
        });

        Schema::table('faltas', function (Blueprint $table): void {
            $table->dropIndex(['data_falta']);
        });
    }
};
