<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Disciplina;
use Illuminate\Database\Eloquent\Collection;

class AvaliacaoService
{
    /**
     * Obtém ou cria avaliações para um aluno baseado nas disciplinas da sua turma
     */
    public function obterAvaliacoesDoAluno(Aluno $aluno): Collection
    {
        if (!$this->alunoTemTurma($aluno)) {
            return new Collection();
        }

        $disciplinasDaTurma = $this->obterDisciplinasDaTurma($aluno);
        
        return $this->criarAvaliacoesSeNecessario($aluno, $disciplinasDaTurma);
    }

    /**
     * Atualiza as notas de uma avaliação e recalcula a nota final
     */
    public function atualizarNotas(Avaliacao $avaliacao, array $notas): Avaliacao
    {
        $avaliacao->fill($notas);
        $avaliacao->calcularNotaFinal();
        $avaliacao->save();
        
        return $avaliacao;
    }

    /**
     * Verifica se o aluno tem turma
     */
    private function alunoTemTurma(Aluno $aluno): bool
    {
        return !is_null($aluno->turma_id);
    }

    /**
     * Obtém as disciplinas da turma do aluno
     */
    private function obterDisciplinasDaTurma(Aluno $aluno): Collection
    {
        return $aluno->turma->disciplinas;
    }

    /**
     * Cria avaliações para o aluno se não existirem
     */
    private function criarAvaliacoesSeNecessario(Aluno $aluno, Collection $disciplinas): Collection
    {
        foreach ($disciplinas as $disciplina) {
            if (!$this->avaliacaoExiste($aluno, $disciplina)) {
                $this->criarAvaliacaoParaDisciplina($aluno, $disciplina);
            }
        }
        
        return $aluno->avaliacoes()->with('disciplina')->get();
    }

    /**
     * Verifica se já existe avaliação para a disciplina
     */
    private function avaliacaoExiste(Aluno $aluno, Disciplina $disciplina): bool
    {
        return Avaliacao::where('aluno_id', $aluno->id)
            ->where('disciplina_id', $disciplina->id)
            ->exists();
    }

    /**
     * Cria uma nova avaliação para o aluno na disciplina
     */
    private function criarAvaliacaoParaDisciplina(Aluno $aluno, Disciplina $disciplina): void
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
                'situacao' => 'em_andamento',
            ]
        );
    }
}