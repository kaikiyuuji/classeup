<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\RegistrarChamadaData;
use App\Models\Aluno;
use App\Models\Falta;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Orquestra o registro de chamada de presença e relatórios derivados.
 *
 * O fluxo de registro é idempotente: re-submeter para a mesma combinação
 * (disciplina + professor + data) apaga as faltas anteriores antes de
 * gravar as novas, dentro de uma transação.
 */
class ChamadaService
{
    /**
     * @return Collection<int, object>
     */
    public function obterTurmasComVinculo(): Collection
    {
        return DB::table('turmas')
            ->join('professor_disciplina_turma', 'turmas.id', '=', 'professor_disciplina_turma.turma_id')
            ->join('professores', 'professor_disciplina_turma.professor_id', '=', 'professores.id')
            ->join('disciplinas', 'professor_disciplina_turma.disciplina_id', '=', 'disciplinas.id')
            ->select(
                'turmas.id as turma_id',
                'turmas.nome as turma_nome',
                'turmas.serie',
                'disciplinas.id as disciplina_id',
                'disciplinas.nome as disciplina_nome',
                'professores.id as professor_id',
                'professores.nome as professor_nome',
            )
            ->orderBy('turmas.nome')
            ->orderBy('disciplinas.nome')
            ->get()
            ->groupBy('turma_nome');
    }

    /**
     * @return EloquentCollection<int, Aluno>
     */
    public function alunosDaTurma(int $turmaId): EloquentCollection
    {
        return Aluno::where('turma_id', $turmaId)->orderBy('nome')->get();
    }

    /**
     * @return array<int, string> matrículas dos alunos ausentes
     */
    public function matriculasAusentes(int $disciplinaId, int $professorId, string $data): array
    {
        return Falta::where('disciplina_id', $disciplinaId)
            ->where('professor_id', $professorId)
            ->whereDate('data_falta', $data)
            ->pluck('matricula')
            ->toArray();
    }

    public function jaExisteChamada(int $disciplinaId, int $professorId, string $data): bool
    {
        return Falta::where('disciplina_id', $disciplinaId)
            ->where('professor_id', $professorId)
            ->whereDate('data_falta', $data)
            ->exists();
    }

    public function registrar(RegistrarChamadaData $data): void
    {
        DB::transaction(function () use ($data): void {
            Falta::where('disciplina_id', $data->disciplinaId)
                ->where('professor_id', $data->professorId)
                ->whereDate('data_falta', $data->dataFalta->toDateString())
                ->delete();

            foreach ($data->matriculasAusentes as $matricula) {
                $alunoId = Aluno::where('numero_matricula', $matricula)->value('id');

                Falta::create([
                    'aluno_id' => $alunoId,
                    'matricula' => $matricula,
                    'disciplina_id' => $data->disciplinaId,
                    'professor_id' => $data->professorId,
                    'data_falta' => $data->dataFalta->toDateString(),
                    'justificada' => false,
                ]);
            }
        });
    }

    /**
     * @return EloquentCollection<int, Falta>
     */
    public function relatorioPorMatricula(string $matricula, CarbonImmutable $dataInicio, CarbonImmutable $dataFim): EloquentCollection
    {
        return Falta::with(['disciplina', 'professor'])
            ->where('matricula', $matricula)
            ->whereBetween('data_falta', [$dataInicio, $dataFim])
            ->orderBy('data_falta', 'desc')
            ->get();
    }
}
