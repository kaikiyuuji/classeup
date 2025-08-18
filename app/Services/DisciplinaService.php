<?php

namespace App\Services;

use App\Models\Disciplina;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class DisciplinaService
{
    /**
     * Obtém disciplinas com filtros e paginação
     */
    public function obterDisciplinasComFiltros(Request $request): LengthAwarePaginator
    {
        $query = Disciplina::query();

        $this->aplicarFiltros($query, $request);
        $this->aplicarOrdenacao($query, $request);

        return $query->paginate(10)->withQueryString();
    }

    /**
     * Obtém todas as disciplinas ativas
     */
    public function obterDisciplinasAtivas(): Collection
    {
        return Disciplina::ativas()->get();
    }

    /**
     * Cria uma nova disciplina
     */
    public function criarDisciplina(array $dadosValidados): Disciplina
    {
        return Disciplina::create($dadosValidados);
    }

    /**
     * Atualiza uma disciplina existente
     */
    public function atualizarDisciplina(Disciplina $disciplina, array $dadosValidados): bool
    {
        return $disciplina->update($dadosValidados);
    }

    /**
     * Exclui uma disciplina com verificação de segurança
     */
    public function excluirDisciplina(Disciplina $disciplina): array
    {
        if ($this->verificarRelacionamentosAtivos($disciplina)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Não é possível excluir esta disciplina pois ela possui relacionamentos ativos (professores).'
            ];
        }

        try {
            $disciplina->delete();
            return [
                'sucesso' => true,
                'mensagem' => 'Disciplina excluída com sucesso!'
            ];
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao excluir disciplina: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verifica se a disciplina possui relacionamentos ativos
     */
    public function verificarRelacionamentosAtivos(Disciplina $disciplina): bool
    {
        return $disciplina->possuiRelacionamentosAtivos();
    }

    /**
     * Obtém estatísticas das disciplinas
     */
    public function obterEstatisticas(): array
    {
        $total = Disciplina::count();
        $ativas = Disciplina::ativas()->count();
        $inativas = $total - $ativas;
        $comProfessores = Disciplina::has('professores')->count();
        $comTurmas = Disciplina::has('turmas')->count();

        return [
            'total_disciplinas' => $total,
            'disciplinas_ativas' => $ativas,
            'disciplinas_inativas' => $inativas,
            'disciplinas_com_professores' => $comProfessores,
            'disciplinas_com_turmas' => $comTurmas,
            'percentual_ativas' => $total > 0 ? round(($ativas / $total) * 100, 1) : 0
        ];
    }

    /**
     * Aplica filtros à query
     */
    private function aplicarFiltros($query, Request $request): void
    {
        $query->comBusca($request->get('search'))
              ->comStatus($request->get('status'));
    }

    /**
     * Aplica ordenação à query
     */
    private function aplicarOrdenacao($query, Request $request): void
    {
        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        
        $query->comOrdenacao($sortField, $sortDirection);
    }

    /**
     * Busca disciplinas por termo
     */
    public function buscarDisciplinas(string $termo): Collection
    {
        return Disciplina::comBusca($termo)->ativas()->get();
    }

    /**
     * Obtém disciplinas de um professor específico
     */
    public function obterDisciplinasProfessor(int $professorId): Collection
    {
        return Disciplina::whereHas('professores', function ($query) use ($professorId) {
            $query->where('professor_id', $professorId);
        })->ativas()->get();
    }

    /**
     * Verifica se uma disciplina pode ser desativada
     */
    public function podeDesativar(Disciplina $disciplina): array
    {
        $temTurmasAtivas = $disciplina->turmas()->exists();
        $temAvaliacoesRecentes = $disciplina->avaliacoes()
            ->where('created_at', '>=', now()->subDays(30))
            ->exists();
        
        if ($temTurmasAtivas || $temAvaliacoesRecentes) {
            return [
                'pode_desativar' => false,
                'motivo' => 'Disciplina possui turmas ativas ou avaliações recentes'
            ];
        }

        return [
            'pode_desativar' => true,
            'motivo' => null
        ];
    }
}