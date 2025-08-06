<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primeiro, vamos mapear os valores existentes para os novos níveis
        DB::table('turmas')->where('serie', '<=', '5')->update(['serie' => 'fundamental']);
        DB::table('turmas')->where('serie', '>', '5')->where('serie', '<=', '9')->update(['serie' => 'médio']);
        
        // Agora alteramos a coluna para enum
        Schema::table('turmas', function (Blueprint $table) {
            $table->enum('serie', ['pré-escola', 'fundamental', 'médio'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para string
        Schema::table('turmas', function (Blueprint $table) {
            $table->string('serie')->change();
        });
        
        // Mapear de volta para números (aproximação)
        DB::table('turmas')->where('serie', 'pré-escola')->update(['serie' => '1']);
        DB::table('turmas')->where('serie', 'fundamental')->update(['serie' => '5']);
        DB::table('turmas')->where('serie', 'médio')->update(['serie' => '9']);
    }
};
