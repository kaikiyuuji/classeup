<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\Chamada;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AlunoService
{
    /**
     * Prepara dados para exibição do aluno
     */
    public function prepararDadosParaExibicao(Aluno $aluno): array
    {
        $aluno->load('turma');
        
        return [
            'aluno' => $aluno,
            'totalPresencas' => $this->obterTotalPresencas($aluno),
            'faltasParaJustificar' => $this->obterFaltasParaJustificar($aluno)
        ];
    }
    
    /**
     * Obtém o total de presenças do aluno
     */
    private function obterTotalPresencas(Aluno $aluno): int
    {
        return Chamada::porAluno($aluno->numero_matricula)
            ->where('status', 'presente')
            ->count();
    }
    
    /**
     * Obtém faltas não justificadas do aluno
     */
    private function obterFaltasParaJustificar(Aluno $aluno)
    {
        return Chamada::porAluno($aluno->numero_matricula)
            ->where('status', 'falta')
            ->where('justificada', false)
            ->with(['disciplina', 'professor'])
            ->orderBy('data_chamada', 'desc')
            ->get();
    }
    
    /**
     * Aplica filtros na consulta de alunos
     */
    public function aplicarFiltros(Builder $query, Request $request): Builder
    {
        // Filtro de busca
        if ($request->filled('search')) {
            $query->buscar($request->get('search'));
        }
        
        // Filtro por status
        if ($request->filled('status')) {
            $query->porStatus($request->get('status'));
        }
        
        // Filtro por turma
        if ($request->filled('turma_id')) {
            $query->porTurma($request->get('turma_id'));
        }
        
        // Aplicar ordenação
        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        $query->ordenadoPor($sortField, $sortDirection);
        
        return $query;
    }
    
    /**
     * Obtém alunos paginados com filtros aplicados
     */
    public function obterAlunosPaginados(Request $request): LengthAwarePaginator
    {
        $query = Aluno::with('turma');
        $query = $this->aplicarFiltros($query, $request);
        
        return $query->paginate(15)->withQueryString();
    }
    
    /**
     * Verifica se um aluno pode ser excluído
     */
    public function podeSerExcluido(Aluno $aluno): array
    {
        $temChamadas = $aluno->chamadas()->exists();
        $temAvaliacoes = $aluno->avaliacoes()->exists();
        
        $podeExcluir = !$temChamadas && !$temAvaliacoes;
        
        $motivos = [];
        if ($temChamadas) {
            $motivos[] = 'O aluno possui registros de chamadas';
        }
        if ($temAvaliacoes) {
            $motivos[] = 'O aluno possui avaliações registradas';
        }
        
        return [
            'pode_excluir' => $podeExcluir,
            'motivos' => $motivos
        ];
    }
    
    /**
     * Exclui um aluno com validações de segurança
     */
    public function excluirComSeguranca(Aluno $aluno): array
    {
        $verificacao = $this->podeSerExcluido($aluno);
        
        if (!$verificacao['pode_excluir']) {
            return [
                'sucesso' => false,
                'mensagem' => 'Não é possível excluir o aluno: ' . implode(', ', $verificacao['motivos'])
            ];
        }
        
        // Deletar foto de perfil se existir
        if ($aluno->foto_perfil && \Storage::disk('public')->exists($aluno->foto_perfil)) {
            \Storage::disk('public')->delete($aluno->foto_perfil);
        }
        
        $aluno->delete();
        
        return [
            'sucesso' => true,
            'mensagem' => 'Aluno excluído com sucesso!'
        ];
    }
}