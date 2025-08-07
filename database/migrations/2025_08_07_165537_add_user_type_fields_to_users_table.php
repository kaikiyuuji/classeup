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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('tipo_usuario', ['admin', 'professor', 'aluno'])->default('admin');
            $table->unsignedBigInteger('professor_id')->nullable();
            $table->unsignedBigInteger('aluno_id')->nullable();
            
            // Foreign keys
            $table->foreign('professor_id')->references('id')->on('professores')->onDelete('cascade');
            $table->foreign('aluno_id')->references('id')->on('alunos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['professor_id']);
            $table->dropForeign(['aluno_id']);
            $table->dropColumn(['tipo_usuario', 'professor_id', 'aluno_id']);
        });
    }
};
