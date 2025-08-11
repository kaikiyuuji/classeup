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
        Schema::create('chamadas', function (Blueprint $table) {
            $table->id();
            $table->string('matricula');
            $table->foreignId('disciplina_id')->constrained('disciplinas')->onDelete('cascade');
            $table->foreignId('professor_id')->constrained('professores')->onDelete('cascade');
            $table->date('data_chamada');
            $table->enum('status', ['presente', 'falta'])->default('presente');
            $table->boolean('justificada')->default(false);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Índices para otimizar consultas
            $table->index(['matricula', 'data_chamada']);
            $table->index(['disciplina_id', 'data_chamada']);
            $table->index(['professor_id', 'data_chamada']);
            $table->index(['status', 'data_chamada']);
            
            // Constraint para evitar duplicatas
            $table->unique(['matricula', 'disciplina_id', 'professor_id', 'data_chamada'], 'unique_chamada_diaria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chamadas');
    }
};
