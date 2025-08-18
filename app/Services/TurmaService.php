<?php

namespace App\Services;

use App\Models\Turma;
use App\Models\Aluno;
use App\Models\Professor;
use Illuminate\Database\Eloquent\Collection;

/**
 * Serviço responsável pela lógica de negócio relacionada às Turmas
 * Aplica princípios de Object Calisthenics e Single Responsibility Principle
 */
class TurmaService
{
    /**
     * Prepara dados completos para exibição de uma turma
     * Centraliza a lógica de carregamento de relacionamentos e dados auxiliares
     */
    public function prepararDadosParaExibicao(Turma $turma): array
    {
        $this->carregarRelacionamentos($turma);
        
        return [
            'turma' => $turma,
            'alunosDisponiveis' => $this->buscarAlunosDisponiveis(),
            'professoresDisponiveis' => $this->buscarProfessoresDisponiveis($turma)
        ];
    }

    /**
     * Carrega os relacionamentos necessários da turma
     * Aplica o princípio de "Apenas Um Nível de Indentação por Método"
     */
    private function carregarRelacionamentos(Turma $turma): void
    {
        $turma->load(['alunos', 'professores', 'disciplinas']);
    }

    /**
     * Busca alunos que estão disponíveis para vinculação (sem turma)
     * Método focado em uma única responsabilidade
     */
    private function buscarAlunosDisponiveis(): Collection
    {
        return Aluno::whereNull('turma_id')
            ->orderBy('nome')
            ->get();
    }

    /**
     * Busca professores disponíveis para vinculação à turma
     * Aplica filtros de negócio de forma encapsulada
     */
    private function buscarProfessoresDisponiveis(Turma $turma): Collection
    {
        $professoresJaVinculados = $this->obterProfessoresJaVinculados($turma);
        
        return Professor::with('disciplinas')
            ->where('ativo', true)
            ->whereNotIn('id', $professoresJaVinculados)
            ->whereHas('disciplinas')
            ->orderBy('nome')
            ->get();
    }

    /**
     * Obtém IDs dos professores já vinculados à turma
     * Extrai lógica específica para método dedicado
     */
    private function obterProfessoresJaVinculados(Turma $turma): array
    {
        return $turma->professores()->pluck('professor_id')->toArray();
    }

    /**
     * Verifica se uma turma pode ser excluída com segurança
     * Implementa regra de negócio de validação antes da exclusão
     */
    public function podeSerExcluida(Turma $turma): bool
    {
        return $this->turmaEstaVazia($turma) && $this->naoTemProfessoresVinculados($turma);
    }

    /**
     * Verifica se a turma está vazia (sem alunos)
     */
    private function turmaEstaVazia(Turma $turma): bool
    {
        return $turma->alunos()->count() === 0;
    }

    /**
     * Verifica se a turma não tem professores vinculados
     */
    private function naoTemProfessoresVinculados(Turma $turma): bool
    {
        return $turma->professores()->count() === 0;
    }

    /**
     * Obtém informações sobre relacionamentos existentes da turma
     * Útil para exibir detalhes antes da exclusão
     */
    public function obterInformacoesRelacionamentos(Turma $turma): array
    {
        return [
            'total_alunos' => $turma->alunos()->count(),
            'total_professores' => $turma->professores()->count(),
            'nomes_alunos' => $turma->alunos()->pluck('nome')->toArray(),
            'nomes_professores' => $turma->professores()->pluck('nome')->toArray()
        ];
    }

    /**
     * Valida se um aluno pertence à turma especificada
     * Implementa validação de segurança para operações de desvinculação
     */
    public function alunoPerteceATurma(Aluno $aluno, Turma $turma): bool
    {
        return $aluno->turma_id === $turma->id;
    }

    /**
     * Executa a desvinculação segura de um aluno da turma
     * Centraliza a lógica de desvinculação com validações
     */
    public function desvincularAlunoComSeguranca(Aluno $aluno, Turma $turma): bool
    {
        if (!$this->alunoPerteceATurma($aluno, $turma)) {
            return false;
        }

        $aluno->update(['turma_id' => null]);
        return true;
    }
}