<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
            $table->foreignId('disciplina_id')->constrained('disciplinas')->onDelete('cascade');
            $table->decimal('av1', 4, 2)->nullable()->default(0);
            $table->decimal('av2', 4, 2)->nullable()->default(0);
            $table->decimal('av3', 4, 2)->nullable()->default(0);
            $table->decimal('av4', 4, 2)->nullable()->default(0);
            $table->decimal('substitutiva', 4, 2)->nullable();
            $table->decimal('recuperacao_final', 4, 2)->nullable();
            $table->decimal('nota_final', 4, 2)->nullable()->default(0);
            $table->enum('situacao', ['aprovado', 'reprovado', 'em_andamento'])->default('em_andamento');
            $table->timestamps();
            
            // Índice único para evitar duplicatas de avaliação por aluno/disciplina
            $table->unique(['aluno_id', 'disciplina_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacoes');
    }
};
