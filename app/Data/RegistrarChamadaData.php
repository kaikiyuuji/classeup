<?php

declare(strict_types=1);

namespace App\Data;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

/**
 * Payload imutável para registro de chamada.
 *
 * @property-read int[] $matriculasAusentes Lista de números de matrícula
 *   dos alunos ausentes (vem do checkbox da view).
 */
final readonly class RegistrarChamadaData
{
    /**
     * @param list<string> $matriculasAusentes
     */
    public function __construct(
        public int $turmaId,
        public int $disciplinaId,
        public int $professorId,
        public CarbonImmutable $dataFalta,
        public array $matriculasAusentes,
        public bool $confirmarReenvio,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            turmaId: (int) $request->input('turma_id'),
            disciplinaId: (int) $request->input('disciplina_id'),
            professorId: (int) $request->input('professor_id'),
            dataFalta: CarbonImmutable::parse((string) $request->input('data_falta')),
            matriculasAusentes: array_values(array_map('strval', (array) $request->input('faltas', []))),
            confirmarReenvio: $request->boolean('confirmar_reenvio'),
        );
    }
}
