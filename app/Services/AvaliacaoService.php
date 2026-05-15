<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Avaliacao\NotaCalculator;
use App\Enums\SituacaoAvaliacao;
use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Disciplina;
use Illuminate\Database\Eloquent\Collection;

class AvaliacaoService
{
    public function __construct(
        private readonly NotaCalculator $calculator = new NotaCalculator,
    ) {}

    /**
     * Obtém avaliações para um aluno baseado nas disciplinas da sua turma.
     * Cria avaliações faltantes como efeito colateral (legacy behavior).
     *
     * @return Collection<int, Avaliacao>
     */
    public function obterAvaliacoesDoAluno(Aluno $aluno): Collection
    {
        if (! $this->alunoTemTurma($aluno)) {
            return new Collection;
        }

        $this->garantirAvaliacoesParaAluno($aluno);

        return $aluno->avaliacoes()->with('disciplina')->get();
    }

    /**
     * Garante que existe uma Avaliacao para cada disciplina da turma do aluno.
     * Idempotente.
     */
    public function garantirAvaliacoesParaAluno(Aluno $aluno): void
    {
        if (! $this->alunoTemTurma($aluno)) {
            return;
        }

        foreach ($this->obterDisciplinasDaTurma($aluno) as $disciplina) {
            $this->criarAvaliacaoSeNaoExistir($aluno, $disciplina);
        }
    }

    /**
     * Atualiza as notas de uma avaliação e recalcula a nota final.
     *
     * @param array<string, mixed> $notas
     */
    public function atualizarNotas(Avaliacao $avaliacao, array $notas): Avaliacao
    {
        $avaliacao->fill($notas);
        $avaliacao->calcularNotaFinal($this->calculator);

        return $avaliacao;
    }

    private function alunoTemTurma(Aluno $aluno): bool
    {
        return ! is_null($aluno->turma_id);
    }

    /**
     * @return Collection<int, Disciplina>
     */
    private function obterDisciplinasDaTurma(Aluno $aluno): Collection
    {
        return $aluno->turma->disciplinas;
    }

    private function criarAvaliacaoSeNaoExistir(Aluno $aluno, Disciplina $disciplina): void
    {
        Avaliacao::firstOrCreate(
            [
                'aluno_id' => $aluno->id,
                'disciplina_id' => $disciplina->id,
            ],
            [
                'av1' => 0,
                'av2' => 0,
                'av3' => 0,
                'av4' => 0,
                'nota_final' => 0,
                'situacao' => SituacaoAvaliacao::EmAndamento,
            ]
        );
    }
}
