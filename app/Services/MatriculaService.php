<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Aluno;
use Illuminate\Support\Facades\DB;

/**
 * Gera números de matrícula sequenciais por ano (formato AAAA####).
 *
 * Usa `lockForUpdate` dentro de uma transação para evitar corrida quando
 * dois admins criam alunos simultaneamente.
 */
class MatriculaService
{
    public function gerar(?int $ano = null): string
    {
        $ano = $ano ?? (int) date('Y');
        $anoStr = (string) $ano;

        return DB::transaction(function () use ($anoStr): string {
            $ultima = Aluno::query()
                ->where('numero_matricula', 'like', $anoStr.'%')
                ->orderByDesc('numero_matricula')
                ->lockForUpdate()
                ->first();

            if ($ultima === null) {
                $proximo = 1;
            } else {
                $proximo = ((int) substr((string) $ultima->numero_matricula, 4)) + 1;
            }

            return $anoStr.str_pad((string) $proximo, 4, '0', STR_PAD_LEFT);
        });
    }
}
