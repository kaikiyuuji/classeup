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
        Schema::table('alunos', function (Blueprint $table) {
            $table->string('numero_matricula')->unique()->after('id');
            $table->date('data_matricula')->after('numero_matricula');
            $table->enum('status_matricula', ['ativa', 'inativa','cancelada'])->default('ativa')->after('data_matricula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->dropColumn(['numero_matricula', 'data_matricula', 'status_matricula']);
        });
    }
};
