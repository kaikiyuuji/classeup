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
        Schema::dropIfExists('faltas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('faltas', function (Blueprint $table) {
            $table->id();
            $table->string('matricula');
            $table->unsignedBigInteger('disciplina_id');
            $table->unsignedBigInteger('professor_id');
            $table->date('data_falta');
            $table->boolean('justificada')->default(false);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('matricula')->references('numero_matricula')->on('alunos')->onDelete('cascade');
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');
            $table->foreign('professor_id')->references('id')->on('professores')->onDelete('cascade');

            $table->index(['matricula', 'data_falta']);
            $table->index(['disciplina_id', 'data_falta']);
            $table->index(['professor_id', 'data_falta']);
            $table->unique(['matricula', 'disciplina_id', 'professor_id', 'data_falta'], 'unique_falta_diaria');
        });
    }
};
