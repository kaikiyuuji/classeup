<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\Chamada;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Models\Turma;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Obtém estatísticas básicas para o dashboard administrativo
     */
    public function obterEstatisticasBasicas(): array
    {
        return [
            'totalAlunos' => Aluno::count(),
            'totalProfessores' => Professor::count(),
            'totalTurmas' => Turma::count(),
            'totalDisciplinas' => Disciplina::count(),
            'alunosAtivos' => Aluno::whereHas('user')->count(),
            'professoresAtivos' => Professor::where('ativo', true)->count(),
            'turmasComAlunos' => Turma::has('alunos')->count()
        ];
    }

    /**
     * Obtém distribuição de alunos por turma (top 5)
     */
    public function obterDistribuicaoAlunosPorTurma(): Collection
    {
        return Turma::withCount('alunos')
            ->orderBy('alunos_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($turma) {
                return $this->formatarTurmaParaDistribuicao($turma);
            });
    }

    /**
     * Obtém distribuição de alunos por nível educacional
     */
    public function obterDistribuicaoAlunosPorNivel(): Collection
    {
        return Turma::select('serie', DB::raw('COUNT(alunos.id) as alunos_count'))
            ->leftJoin('alunos', 'turmas.id', '=', 'alunos.turma_id')
            ->groupBy('serie')
            ->get()
            ->map(function ($item) {
                return [
                    'nivel' => $item->serie,
                    'total' => $item->alunos_count
                ];
            });
    }

    /**
     * Obtém atividades recentes (últimas chamadas agrupadas)
     */
    public function obterAtividadesRecentes(): Collection
    {
        return Chamada::select(
                'data_chamada',
                'disciplina_id', 
                'professor_id',
                DB::raw('COUNT(*) as total_registros'),
                DB::raw('SUM(CASE WHEN status = "presente" THEN 1 ELSE 0 END) as presencas'),
                DB::raw('SUM(CASE WHEN status = "falta" THEN 1 ELSE 0 END) as faltas')
            )
            ->with(['disciplina', 'professor'])
            ->groupBy('data_chamada', 'disciplina_id', 'professor_id')
            ->orderBy('data_chamada', 'desc')
            ->limit(10)
            ->get()
            ->groupBy('data_chamada')
            ->map(function ($chamadas, $data) {
                return $this->formatarAtividadeRecente($chamadas, $data);
            })
            ->take(7)
            ->values();
    }

    /**
     * Obtém professores mais ativos
     */
    public function obterProfessoresMaisAtivos(): Collection
    {
        return Professor::select(
                'professores.id',
                'professores.nome',
                DB::raw('COUNT(DISTINCT CONCAT(chamadas.disciplina_id, "-", chamadas.data_chamada)) as chamadas_unicas_count')
            )
            ->leftJoin('chamadas', 'professores.id', '=', 'chamadas.professor_id')
            ->where('professores.ativo', true)
            ->groupBy('professores.id', 'professores.nome')
            ->orderBy('chamadas_unicas_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($professor) {
                return [
                    'nome' => $professor->nome,
                    'total_chamadas' => $professor->chamadas_unicas_count
                ];
            });
    }

    /**
     * Obtém estatísticas de frequência geral
     */
    public function obterEstatisticasFrequencia(): array
    {
        $estatisticas = Chamada::select(
                DB::raw('COUNT(DISTINCT CONCAT(disciplina_id, "-", professor_id, "-", data_chamada)) as total_chamadas_unicas'),
                DB::raw('COUNT(*) as total_registros'),
                DB::raw('SUM(CASE WHEN status = "presente" THEN 1 ELSE 0 END) as total_presencas'),
                DB::raw('SUM(CASE WHEN status = "falta" THEN 1 ELSE 0 END) as total_faltas')
            )
            ->first();

        $totalRegistros = $estatisticas->total_registros;
        $totalPresencas = $estatisticas->total_presencas;
        
        return [
            'totalChamadas' => $estatisticas->total_chamadas_unicas,
            'totalPresencas' => $totalPresencas,
            'totalFaltas' => $estatisticas->total_faltas,
            'percentualFrequencia' => $totalRegistros > 0 ? round(($totalPresencas / $totalRegistros) * 100, 1) : 0
        ];
    }

    /**
     * Obtém alunos com mais faltas (resolvendo problema N+1)
     */
    public function obterAlunosComMaisFaltas(int $limite = 5, ?int $professorId = null): Collection
    {
        $query = Chamada::select('matricula', DB::raw('COUNT(*) as total_faltas'))
            ->where('status', 'falta')
            ->groupBy('matricula')
            ->orderBy('total_faltas', 'desc')
            ->limit($limite);

        if ($professorId) {
            $query->where('professor_id', $professorId);
        }

        $faltasData = $query->get();
        $matriculas = $faltasData->pluck('matricula');
        
        // Resolver N+1: buscar todos os alunos de uma vez
        $alunos = Aluno::whereIn('numero_matricula', $matriculas)
            ->get()
            ->keyBy('numero_matricula');

        return $faltasData->map(function ($item) use ($alunos) {
            $aluno = $alunos->get($item->matricula);
            return [
                'nome' => $aluno ? $aluno->nome : 'Aluno não encontrado',
                'matricula' => $item->matricula,
                'total_faltas' => $item->total_faltas
            ];
        });
    }

    /**
     * Obtém dados do dashboard do professor
     */
    public function obterDadosProfessor(Professor $professor): array
    {
        $turmasComDisciplinas = $this->obterTurmasComDisciplinasProfessor($professor);
        $turmaIds = $turmasComDisciplinas->keys();
        $disciplinaIds = $this->extrairDisciplinaIds($turmasComDisciplinas);
        
        return [
            'turmasComDisciplinas' => $turmasComDisciplinas,
            'totalAlunos' => Aluno::whereIn('turma_id', $turmaIds)->count(),
            'alunosPorTurma' => $this->obterAlunosPorTurma($turmaIds),
            'chamadasRecentes' => $this->obterChamadasRecentesProfessor($professor, $disciplinaIds),
            'chamadasAgrupadasPorData' => $this->obterChamadasAgrupadasPorData($professor, $disciplinaIds),
            'estatisticasFrequenciaProfessor' => $this->obterEstatisticasFrequenciaProfessor($professor, $disciplinaIds),
            'disciplinas' => Disciplina::whereIn('id', $disciplinaIds)->get(),
            'totalChamadasRealizadas' => $this->contarChamadasRealizadasProfessor($professor, $disciplinaIds)
        ];
    }

    /**
     * Formata turma para distribuição
     */
    private function formatarTurmaParaDistribuicao(Turma $turma): array
    {
        $nivelFormatado = ucfirst($turma->serie);
        $turnoFormatado = ucfirst($turma->turno);
        $nomeCompleto = $turma->nome . ' - ' . $nivelFormatado . ' - ' . $turnoFormatado;
        
        return [
            'nome' => $nomeCompleto,
            'total' => $turma->alunos_count
        ];
    }

    /**
     * Formata atividade recente
     */
    private function formatarAtividadeRecente(Collection $chamadas, string $data): array
    {
        return [
            'data' => $data,
            'total_chamadas' => $chamadas->count(),
            'presencas' => $chamadas->sum('presencas'),
            'faltas' => $chamadas->sum('faltas')
        ];
    }

    /**
     * Obtém turmas com disciplinas do professor usando relacionamentos Eloquent
     */
    public function obterTurmasComDisciplinasProfessor(Professor $professor): Collection
    {
        $turmasData = DB::table('professor_disciplina_turma')
            ->join('turmas', 'professor_disciplina_turma.turma_id', '=', 'turmas.id')
            ->join('disciplinas', 'professor_disciplina_turma.disciplina_id', '=', 'disciplinas.id')
            ->where('professor_disciplina_turma.professor_id', $professor->id)
            ->select(
                'turmas.id as turma_id',
                'turmas.nome as turma_nome',
                'turmas.serie',
                'turmas.turno',
                'turmas.capacidade_maxima',
                'disciplinas.id as disciplina_id',
                'disciplinas.nome as disciplina_nome',
                'disciplinas.codigo as disciplina_codigo'
            )
            ->get()
            ->groupBy('turma_id');

        // Resolver N+1: buscar contagem de alunos para todas as turmas de uma vez
        $turmaIds = $turmasData->keys();
        $alunosPorTurma = Aluno::whereIn('turma_id', $turmaIds)
            ->select('turma_id', DB::raw('COUNT(*) as total'))
            ->groupBy('turma_id')
            ->get()
            ->keyBy('turma_id');

        return $turmasData->map(function ($items) use ($alunosPorTurma) {
            return $this->formatarTurmaComDisciplinasCompleta($items, $alunosPorTurma);
        });
    }

    /**
     * Formata turma com suas disciplinas
     */
    private function formatarTurmaComDisciplinas(Collection $items): array
    {
        $firstItem = $items->first();
        return [
            'id' => $firstItem->turma_id,
            'nome' => $firstItem->turma_nome,
            'serie' => $firstItem->serie,
            'turno' => $firstItem->turno,
            'disciplinas' => $items->map(function ($item) {
                return [
                    'id' => $item->disciplina_id,
                    'nome' => $item->disciplina_nome
                ];
            })->toArray()
        ];
    }

    /**
     * Formata turma com suas disciplinas incluindo dados completos
     */
    private function formatarTurmaComDisciplinasCompleta(Collection $items, Collection $alunosPorTurma): array
    {
        $firstItem = $items->first();
        $totalAlunos = $alunosPorTurma->get($firstItem->turma_id)?->total ?? 0;
        
        return [
            'id' => $firstItem->turma_id,
            'nome' => $firstItem->turma_nome,
            'serie' => ucfirst($firstItem->serie),
            'turno' => ucfirst($firstItem->turno),
            'capacidade_maxima' => $firstItem->capacidade_maxima,
            'total_alunos' => $totalAlunos,
            'disciplinas' => $items->map(function ($item) {
                return [
                    'id' => $item->disciplina_id,
                    'nome' => $item->disciplina_nome,
                    'codigo' => $item->disciplina_codigo
                ];
            })->values()->toArray()
        ];
    }

    /**
     * Extrai IDs das disciplinas das turmas
     */
    private function extrairDisciplinaIds(Collection $turmasComDisciplinas): Collection
    {
        return $turmasComDisciplinas
            ->flatMap(function ($turma) {
                return collect($turma['disciplinas'])->pluck('id');
            })
            ->unique()
            ->values();
    }

    /**
     * Obtém alunos por turma
     */
    private function obterAlunosPorTurma(Collection $turmaIds): Collection
    {
        return Aluno::whereIn('turma_id', $turmaIds)
            ->select('turma_id', DB::raw('COUNT(*) as total'))
            ->groupBy('turma_id')
            ->get()
            ->keyBy('turma_id');
    }

    /**
     * Obtém chamadas recentes do professor
     */
    private function obterChamadasRecentesProfessor(Professor $professor, Collection $disciplinaIds): Collection
    {
        return Chamada::where('professor_id', $professor->id)
            ->whereIn('disciplina_id', $disciplinaIds)
            ->with(['disciplina'])
            ->select(
                'data_chamada',
                'disciplina_id',
                DB::raw('COUNT(*) as total_registros'),
                DB::raw('SUM(CASE WHEN status = "presente" THEN 1 ELSE 0 END) as presencas'),
                DB::raw('SUM(CASE WHEN status = "falta" THEN 1 ELSE 0 END) as faltas')
            )
            ->groupBy('data_chamada', 'disciplina_id')
            ->orderBy('data_chamada', 'desc')
            ->limit(4)
            ->get();
    }

    /**
     * Obtém chamadas agrupadas por data
     */
    private function obterChamadasAgrupadasPorData(Professor $professor, Collection $disciplinaIds): Collection
    {
        return Chamada::where('professor_id', $professor->id)
            ->whereIn('disciplina_id', $disciplinaIds)
            ->with(['disciplina', 'aluno'])
            ->orderBy('data_chamada', 'desc')
            ->limit(50)
            ->get()
            ->groupBy(function($chamada) {
                return $chamada->data_chamada->format('Y-m-d');
            })
            ->map(function ($chamadas, $data) {
                return $this->formatarChamadasPorData($chamadas, $data);
            })
            ->take(7);
    }

    /**
     * Formata chamadas por data
     */
    private function formatarChamadasPorData(Collection $chamadas, string $data): array
    {
        return [
            'data' => $data,
            'total_registros' => $chamadas->count(),
            'presencas' => $chamadas->where('status', 'presente')->count(),
            'faltas' => $chamadas->where('status', 'falta')->count(),
            'disciplinas' => $chamadas->groupBy('disciplina.nome')->map(function($group, $nome) {
                $disciplina = $group->first()->disciplina;
                return $disciplina && $disciplina->codigo ? $disciplina->codigo . ' - ' . $nome : $nome;
            })->values()->toArray()
        ];
    }

    /**
     * Obtém estatísticas de frequência do professor
     */
    private function obterEstatisticasFrequenciaProfessor(Professor $professor, Collection $disciplinaIds): array
    {
        $estatisticas = Chamada::where('professor_id', $professor->id)
            ->whereIn('disciplina_id', $disciplinaIds)
            ->selectRaw('
                COUNT(*) as total_registros,
                SUM(CASE WHEN status = "presente" THEN 1 ELSE 0 END) as total_presencas,
                SUM(CASE WHEN status = "falta" THEN 1 ELSE 0 END) as total_faltas
            ')
            ->first();

        $totalRegistros = $estatisticas->total_registros ?? 0;
        $totalPresencas = $estatisticas->total_presencas ?? 0;
        
        return [
            'totalRegistros' => $totalRegistros,
            'totalPresencas' => $totalPresencas,
            'totalFaltas' => $estatisticas->total_faltas ?? 0,
            'percentualFrequencia' => $totalRegistros > 0 ? round(($totalPresencas / $totalRegistros) * 100, 1) : 0
        ];
    }

    /**
     * Conta chamadas realizadas pelo professor
     */
    private function contarChamadasRealizadasProfessor(Professor $professor, Collection $disciplinaIds): int
    {
        return Chamada::where('professor_id', $professor->id)
            ->whereIn('disciplina_id', $disciplinaIds)
            ->selectRaw('COUNT(DISTINCT CONCAT(disciplina_id, "-", data_chamada)) as total')
            ->value('total') ?? 0;
    }
}