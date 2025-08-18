<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\Chamada;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Models\Turma;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChamadaService
{
    /**
     * Obtém turmas com vínculo professor-disciplina para administradores
     */
    public function obterTurmasComVinculoAdmin(): Collection
    {
        return $this->executarConsultaTurmasComVinculo()
            ->groupBy($this->gerarChaveAgrupamentoTurma());
    }

    /**
     * Obtém turmas com vínculo para um professor específico
     */
    public function obterTurmasComVinculoProfessor(Professor $professor): Collection
    {
        return $this->executarConsultaTurmasComVinculoProfessor($professor->id)
            ->groupBy('turma_id')
            ->map($this->mapearDadosTurmaProfessor());
    }

    /**
     * Obtém professor vinculado a uma turma e disciplina
     */
    public function obterProfessorVinculado(int $turmaId, int $disciplinaId): ?int
    {
        $vinculo = $this->buscarVinculoProfessorDisciplinaTurma($turmaId, $disciplinaId);
        
        return $vinculo ? $vinculo->professor_id : null;
    }

    /**
     * Obtém chamadas existentes para uma disciplina, professor e data
     */
    public function obterChamadasExistentes(int $disciplinaId, int $professorId, string $data, ?int $turmaId = null): array
    {
        $query = $this->criarQueryChamadasBase($disciplinaId, $professorId, $data);
        
        if ($turmaId) {
            $matriculasAlunos = $this->obterMatriculasAlunosTurma($turmaId);
            $query->whereIn('matricula', $matriculasAlunos);
        }
        
        return $query->pluck('matricula')->toArray();
    }

    /**
     * Processar chamada completa
     */
    public function processarChamada(Professor $professor, array $dadosValidados): array
    {
        $alunos = $this->obterAlunosAtivos($dadosValidados['turma_id']);
        
        if ($this->chamadaJaExiste($professor, $dadosValidados, $alunos)) {
            return $this->tratarChamadaExistente($dadosValidados);
        }
        
        $this->salvarChamadaCompleta($professor, $dadosValidados, $alunos);
        
        return $this->criarRespostaSucesso($dadosValidados);
    }
    
    /**
     * Obter alunos ativos da turma
     */
    private function obterAlunosAtivos(int $turmaId): Collection
    {
        return Aluno::where('turma_id', $turmaId)
            ->where('status_matricula', 'ativa')
            ->get();
    }
    
    /**
     * Verificar se chamada já existe
     */
    private function chamadaJaExiste(Professor $professor, array $dados, Collection $alunos): bool
    {
        $matriculas = $alunos->pluck('numero_matricula')->toArray();
        
        return Chamada::where('disciplina_id', $dados['disciplina_id'])
            ->where('professor_id', $professor->id)
            ->whereDate('data_chamada', $dados['data_chamada'])
            ->whereIn('matricula', $matriculas)
            ->exists();
    }
    
    /**
     * Tratar chamada existente
     */
    private function tratarChamadaExistente(array $dados): array
    {
        if (!isset($dados['confirmar_reenvio']) || !$dados['confirmar_reenvio']) {
            return [
                'sucesso' => false,
                'mensagem' => 'Já existe uma chamada registrada para esta data. Deseja substituir?'
            ];
        }
        
        return ['sucesso' => true];
    }
    
    /**
     * Salvar chamada completa
     */
    private function salvarChamadaCompleta(Professor $professor, array $dados, Collection $alunos): void
    {
        if (isset($dados['confirmar_reenvio']) && $dados['confirmar_reenvio']) {
            $this->removerChamadasExistentes($professor, $dados, $alunos);
        }
        
        $this->inserirNovasChamadas($professor, $dados, $alunos);
    }
    
    /**
     * Remover chamadas existentes (otimizado)
     */
    private function removerChamadasExistentes(Professor $professor, array $dados, Collection $alunos): void
    {
        $matriculas = $alunos->pluck('numero_matricula')->toArray();
        
        // Otimização: usar índices compostos para melhor performance
        Chamada::where('disciplina_id', $dados['disciplina_id'])
            ->where('professor_id', $professor->id)
            ->where('turma_id', $dados['turma_id'])
            ->whereDate('data_chamada', $dados['data_chamada'])
            ->whereIn('matricula', $matriculas)
            ->delete();
    }
    
    /**
     * Excluir chamadas de um dia específico
     */
    public function excluirChamadasDoDia(string $data, int $turmaId, int $disciplinaId, int $professorId): int
    {
        return Chamada::where('disciplina_id', $disciplinaId)
            ->where('professor_id', $professorId)
            ->where('turma_id', $turmaId)
            ->whereDate('data_chamada', $data)
            ->delete();
    }
    
    /**
     * Inserir novas chamadas usando bulk insert otimizado
     */
    private function inserirNovasChamadas(Professor $professor, array $dados, Collection $alunos): void
    {
        if ($alunos->isEmpty()) {
            return;
        }
        
        $presencas = $dados['presencas'] ?? [];
        $registrosChamada = $this->prepararRegistrosChamada($professor, $dados, $alunos, $presencas);
        
        // Bulk insert otimizado - processa em lotes para evitar limitações de memória
        $this->inserirEmLotes($registrosChamada);
    }
    
    /**
     * Preparar registros de chamada para bulk insert otimizado
     */
    private function prepararRegistrosChamada(Professor $professor, array $dados, Collection $alunos, array $presencas): array
    {
        $agora = Carbon::now();
        $presencasSet = array_flip($presencas); // Otimização: usar array_flip para busca O(1)
        
        return $alunos->map(function ($aluno) use ($professor, $dados, $presencasSet, $agora) {
            return [
                'matricula' => $aluno->numero_matricula,
                'disciplina_id' => $dados['disciplina_id'],
                'professor_id' => $professor->id,
                'turma_id' => $dados['turma_id'],
                'data_chamada' => $dados['data_chamada'],
                'status' => $this->determinarStatusPresencaOtimizado($aluno->numero_matricula, $presencasSet),
                'justificada' => false,
                'observacoes' => null,
                'created_at' => $agora,
                'updated_at' => $agora
            ];
        })->toArray();
    }
    
    /**
     * Determinar status de presença do aluno (otimizado)
     */
    private function determinarStatusPresencaOtimizado(string $matricula, array $presencasSet): string
    {
        return isset($presencasSet[$matricula]) ? 'presente' : 'falta';
    }
    
    /**
     * Inserir registros em lotes para otimizar memória
     */
    private function inserirEmLotes(array $registros, int $tamanhoLote = 500): void
    {
        $lotes = array_chunk($registros, $tamanhoLote);
        
        foreach ($lotes as $lote) {
            Chamada::insert($lote);
        }
    }
    
    /**
     * Otimizar inserção com transação para garantir atomicidade
     */
    public function inserirChamadasComTransacao(Professor $professor, array $dados, Collection $alunos): array
    {
        try {
            DB::beginTransaction();
            
            // Verificar se já existe chamada
            if ($this->chamadaJaExiste($dados['disciplina_id'], $professor->id, $dados['data_chamada'])) {
                $resultado = $this->tratarChamadaExistente($dados);
                if (!$resultado['sucesso']) {
                    DB::rollBack();
                    return $resultado;
                }
            }
            
            $this->salvarChamadaCompleta($professor, $dados, $alunos);
            
            DB::commit();
            
            return $this->criarRespostaSucesso($alunos->count());
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao registrar chamada: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtém chamadas de um aluno por período
     */
    public function obterChamadasDoAluno(string $matricula, string $dataInicio, string $dataFim): Collection
    {
        return Chamada::with(['disciplina', 'professor'])
            ->porAluno($matricula)
            ->porPeriodo(Carbon::parse($dataInicio), Carbon::parse($dataFim))
            ->orderBy('data_chamada', 'desc')
            ->get();
    }

    /**
     * Obtém chamadas agrupadas por dia para uma turma/disciplina
     */
    public function obterChamadasPorDia(int $turmaId, int $disciplinaId, int $professorId, string $dataInicio, string $dataFim): Collection
    {
        $matriculasAlunos = $this->obterMatriculasAlunosTurma($turmaId);
        
        return Chamada::with(['aluno'])
            ->where('disciplina_id', $disciplinaId)
            ->where('professor_id', $professorId)
            ->whereIn('matricula', $matriculasAlunos)
            ->whereBetween('data_chamada', [$dataInicio, $dataFim])
            ->orderBy('data_chamada', 'desc')
            ->get()
            ->groupBy('data_chamada');
    }

    /**
     * Obtém estatísticas de chamadas para professor
     */
    public function obterEstatisticasChamadasProfessor(Professor $professor, array $filtros): array
    {
        $query = $this->criarQueryEstatisticasProfessor($professor, $filtros);
        
        // Calcular estatísticas diretamente no banco para melhor performance
        $estatisticas = $query->selectRaw('
            COUNT(DISTINCT CONCAT(disciplina_id, "-", DATE(data_chamada))) as total_chamadas,
            COUNT(*) as total_registros,
            SUM(CASE WHEN status = "presente" THEN 1 ELSE 0 END) as total_presencas,
            SUM(CASE WHEN status = "falta" THEN 1 ELSE 0 END) as total_faltas
        ')->first();
        
        $percentualPresenca = $estatisticas->total_registros > 0 
            ? round(($estatisticas->total_presencas / $estatisticas->total_registros) * 100, 2) 
            : 0;
        
        return [
            'total_chamadas' => $estatisticas->total_chamadas,
            'total_registros' => $estatisticas->total_registros,
            'total_presencas' => $estatisticas->total_presencas,
            'total_faltas' => $estatisticas->total_faltas,
            'percentual_presenca' => $percentualPresenca
        ];
    }

    /**
     * Obtém chamadas detalhadas para professor com filtros
     */
    public function obterChamadasDetalhadasProfessor(Professor $professor, array $filtros): Collection
    {
        return $this->criarQueryEstatisticasProfessor($professor, $filtros)
            ->with(['aluno', 'disciplina'])
            ->get();
    }

    /**
     * Verifica se professor tem vínculo com turma e disciplina usando relacionamentos Eloquent
     */
    public function professorTemVinculo(int $professorId, int $turmaId, int $disciplinaId): bool
    {
        return Professor::find($professorId)
            ?->turmas()
            ->wherePivot('disciplina_id', $disciplinaId)
            ->where('turmas.id', $turmaId)
            ->exists() ?? false;
    }

    /**
     * Exclui chamadas de um dia específico
     */
    public function excluirChamadasDoDia(string $data, int $turmaId, int $disciplinaId, int $professorId): int
    {
        $matriculasAlunos = $this->obterMatriculasAlunosTurma($turmaId);
        
        return Chamada::where('disciplina_id', $disciplinaId)
            ->where('professor_id', $professorId)
            ->where('data_chamada', $data)
            ->whereIn('matricula', $matriculasAlunos)
            ->delete();
    }

    /**
     * Obtém presenças de um aluno com filtros
     */
    public function obterPresencasAluno(string $matricula, array $filtros): Collection
    {
        $query = Chamada::with(['disciplina', 'professor'])
            ->porAluno($matricula)
            ->presencas();
        
        if (isset($filtros['data_inicio']) && isset($filtros['data_fim'])) {
            $query->porPeriodo(
                Carbon::parse($filtros['data_inicio']), 
                Carbon::parse($filtros['data_fim'])
            );
        }
        
        if (isset($filtros['disciplina_id'])) {
            $query->porDisciplina($filtros['disciplina_id']);
        }
        
        return $query->orderBy('data_chamada', 'desc')->get();
    }

    /**
     * Criar resposta de sucesso
     */
    private function criarRespostaSucesso(array $dados): array
    {
        $turma = Turma::find($dados['turma_id']);
        $disciplina = Disciplina::find($dados['disciplina_id']);
        
        return [
            'sucesso' => true,
            'mensagem' => "Chamada da turma {$turma->nome} - {$disciplina->nome} salva com sucesso!"
        ];
    }

    // Métodos privados seguindo Object Calisthenics
    
    private function executarConsultaTurmasComVinculo(): Collection
    {
        return Turma::with(['professores.disciplinas'])
            ->orderBy('nome')
            ->orderBy('ano_letivo')
            ->orderBy('turno')
            ->get()
            ->flatMap(function ($turma) {
                return $turma->professores->flatMap(function ($professor) use ($turma) {
                    return $professor->disciplinas->map(function ($disciplina) use ($turma, $professor) {
                        return (object) [
                            'turma_nome' => $turma->nome,
                            'turma_id' => $turma->id,
                            'serie' => $turma->serie,
                            'ano_letivo' => $turma->ano_letivo,
                            'turno' => $turma->turno,
                            'disciplina_nome' => $disciplina->nome,
                            'disciplina_id' => $disciplina->id,
                            'professor_nome' => $professor->nome,
                            'professor_id' => $professor->id
                        ];
                    });
                });
            })
            ->sortBy(['turma_nome', 'ano_letivo', 'turno', 'disciplina_nome']);
    }

    private function gerarChaveAgrupamentoTurma(): \Closure
    {
        return function($item) {
            return $item->turma_nome . ' - ' . $item->ano_letivo . ' (' . ucfirst($item->turno) . ')';
        };
    }

    private function executarConsultaTurmasComVinculoProfessor(int $professorId): Collection
    {
        $professor = Professor::with(['turmas.disciplinas'])->find($professorId);
        
        if (!$professor) {
            return collect();
        }
        
        return $professor->turmas->flatMap(function ($turma) use ($professorId) {
            return $turma->disciplinas->map(function ($disciplina) use ($turma, $professorId) {
                return (object) [
                    'turma_id' => $turma->id,
                    'turma_nome' => $turma->nome,
                    'serie' => $turma->serie,
                    'turno' => $turma->turno,
                    'disciplina_id' => $disciplina->id,
                    'disciplina_nome' => $disciplina->nome,
                    'disciplina_codigo' => $disciplina->codigo,
                    'professor_id' => $professorId
                ];
            });
        });
    }

    private function mapearDadosTurmaProfessor(): \Closure
    {
        return function ($items) {
            $firstItem = $items->first();
            return (object) [
                'turma_id' => $firstItem->turma_id,
                'turma_nome' => $firstItem->turma_nome,
                'serie' => $firstItem->serie,
                'turno' => $firstItem->turno,
                'professor_id' => $firstItem->professor_id,
                'disciplinas' => $this->mapearDisciplinasTurma($items)
            ];
        };
    }

    private function mapearDisciplinasTurma(Collection $items): Collection
    {
        return $items->map(function ($item) {
            return (object) [
                'disciplina_id' => $item->disciplina_id,
                'disciplina_nome' => $item->disciplina_nome,
                'disciplina_codigo' => $item->disciplina_codigo
            ];
        });
    }

    private function buscarVinculoProfessorDisciplinaTurma(int $turmaId, int $disciplinaId)
    {
        $turma = Turma::with(['professores' => function ($query) use ($disciplinaId) {
            $query->wherePivot('disciplina_id', $disciplinaId);
        }])->find($turmaId);
        
        $professor = $turma?->professores->first();
        
        return $professor ? (object) [
            'professor_id' => $professor->id,
            'turma_id' => $turmaId,
            'disciplina_id' => $disciplinaId
        ] : null;
    }

    private function criarQueryChamadasBase(int $disciplinaId, int $professorId, string $data)
    {
        return Chamada::where('disciplina_id', $disciplinaId)
            ->where('professor_id', $professorId)
            ->where('data_chamada', $data);
    }

    private function obterMatriculasAlunosTurma(int $turmaId): array
    {
        return Aluno::where('turma_id', $turmaId)
            ->where('status_matricula', 'ativa')
            ->pluck('numero_matricula')
            ->toArray();
    }

    private function criarQueryEstatisticasProfessor(Professor $professor, array $filtros)
    {
        $query = Chamada::with(['aluno', 'turma', 'disciplina'])
            ->whereHas('professor', function ($q) use ($professor) {
                $q->where('id', $professor->id);
            })
            ->whereDate('data_chamada', '>=', $filtros['data_inicio'])
            ->whereDate('data_chamada', '<=', $filtros['data_fim']);
        
        if (isset($filtros['turma_id'])) {
            $query->whereHas('turma', function ($q) use ($filtros) {
                $q->where('id', $filtros['turma_id']);
            });
        }
        
        if (isset($filtros['disciplina_id'])) {
            $query->whereHas('disciplina', function ($q) use ($filtros) {
                $q->where('id', $filtros['disciplina_id']);
            });
        }
        
        if (isset($filtros['busca_aluno'])) {
            $query->where(function($q) use ($filtros) {
                $q->where('matricula', 'like', '%' . $filtros['busca_aluno'] . '%')
                  ->orWhereHas('aluno', function($subQuery) use ($filtros) {
                      $subQuery->where('nome', 'like', '%' . $filtros['busca_aluno'] . '%');
                  });
            });
        }
        
        return $query;
    }
}