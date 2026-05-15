<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LGPD: registra pedidos de exclusão de aluno antes da efetivação.
 * Aprovação exige admin distinto do solicitante (4-eyes principle).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes_exclusao', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();
            $table->foreignId('solicitante_user_id')->constrained('users')->restrictOnDelete();
            $table->string('status')->default('pendente'); // pendente | aprovada | rejeitada
            $table->text('motivo')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('aluno_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_exclusao');
    }
};
