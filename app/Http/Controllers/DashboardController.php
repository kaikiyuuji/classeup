<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Chamada;
use App\Models\Aluno;
use App\Http\Requests\ValidateTurmaProfessorRequest;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Models\Turma;

class DashboardController extends Controller
{
    /**
     * Redireciona para o dashboard apropriado baseado no tipo de usuário
     */
    public function index(): RedirectResponse
    {
        $tipoUsuario = auth()->user()->tipo_usuario;
        
        return match ($tipoUsuario) {
            'admin' => redirect()->route('dashboard.admin'),
            'professor' => redirect()->route('dashboard.professor'),
            'aluno' => redirect()->route('dashboard.aluno'),
            default => abort(403, 'Tipo de usuário não reconhecido')
        };
    }

    /**
     * Dashboard para administradores
     */
    public function admin(): View
    {
        // Estatísticas básicas
        $totalAlunos = Aluno::count();
        $totalProfessores = Professor::count();
        $totalTurmas = Turma::count();
        $totalDisciplinas = Disciplina::count();
        
        // Estatísticas avançadas
        $alunosAtivos = Aluno::whereHas('user')->count();
        $professoresAtivos = Professor::where('ativo', true)->count();
        $turmasComAlunos = Turma::has('alunos')->count();
        
        // Distribuição de alunos por turma
        $alunosPorTurma = Turma::withCount('alunos')
            ->orderBy('alunos_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($turma) {
                $nivelFormatado = ucfirst($turma->serie);
                $turnoFormatado = ucfirst($turma->turno);
                $nomeCompleto = $turma->nome . ' - ' . $nivelFormatado . ' - ' . $turnoFormatado;
                
                return [
                    'nome' => $nomeCompleto,
                    'total' => $turma->alunos_count
                ];
            });
        
        // Distribuição de alunos por nível educacional
        $alunosPorNivel = Turma::select('serie', DB::raw('COUNT(alunos.id) as alunos_count'))
            ->leftJoin('alunos', 'turmas.id', '=', 'alunos.turma_id')
            ->groupBy('serie')
            ->get()
            ->map(function ($item) {
                return [
                    'nivel' => $item->serie,
                    'total' => $item->alunos_count
                ];
            });
        
        // Atividades recentes (últimas chamadas agrupadas por disciplina/professor/data)
        $atividadesRecentes = Chamada::select(
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
                return [
                    'data' => $data,
                    'total_chamadas' => $chamadas->count(), // Número de chamadas únicas (disciplina/professor/data)
                    'presencas' => $chamadas->sum('presencas'),
                    'faltas' => $chamadas->sum('faltas')
                ];
            })
            ->take(7)
            ->values();
        
        // Professores mais ativos (com mais chamadas únicas por disciplina/data)
        $professoresMaisAtivos = Professor::select(
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
        
        // Estatísticas de frequência geral
        $estatisticasChamadas = Chamada::select(
                DB::raw('COUNT(DISTINCT CONCAT(disciplina_id, "-", professor_id, "-", data_chamada)) as total_chamadas_unicas'),
                DB::raw('COUNT(*) as total_registros'),
                DB::raw('SUM(CASE WHEN status = "presente" THEN 1 ELSE 0 END) as total_presencas'),
                DB::raw('SUM(CASE WHEN status = "falta" THEN 1 ELSE 0 END) as total_faltas')
            )
            ->first();
        
        $totalChamadas = $estatisticasChamadas->total_chamadas_unicas;
        $totalPresencas = $estatisticasChamadas->total_presencas;
        $totalFaltas = $estatisticasChamadas->total_faltas;
        $totalRegistros = $estatisticasChamadas->total_registros;
        $percentualFrequencia = $totalRegistros > 0 ? round(($totalPresencas / $totalRegistros) * 100, 1) : 0;
        
        // Alunos com mais faltas (top 5)
        $alunosComMaisFaltas = Chamada::select('matricula')
            ->where('status', 'falta')
            ->groupBy('matricula')
            ->selectRaw('matricula, COUNT(*) as total_faltas')
            ->orderBy('total_faltas', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $aluno = Aluno::where('numero_matricula', $item->matricula)->first();
                return [
                    'nome' => $aluno ? $aluno->nome : 'Aluno não encontrado',
                    'matricula' => $item->matricula,
                    'total_faltas' => $item->total_faltas
                ];
            });
        
        return view('admin.dashboard', compact(
            'totalAlunos',
            'totalProfessores', 
            'totalTurmas',
            'totalDisciplinas',
            'alunosAtivos',
            'professoresAtivos',
            'turmasComAlunos',
            'alunosPorTurma',
            'alunosPorNivel',
            'atividadesRecentes',
            'professoresMaisAtivos',
            'totalChamadas',
            'totalPresencas',
            'totalFaltas',
            'percentualFrequencia',
            'alunosComMaisFaltas'
        ));
    }

    /**
     * Dashboard para professores
     */
    public function professor(): View
    {
        $professor = auth()->user()->professor;
        
        if (!$professor) {
            abort(403, 'Professor não encontrado');
        }
        
        // Buscar turmas vinculadas ao professor com suas disciplinas específicas
        $turmasComDisciplinas = DB::table('professor_disciplina_turma')
            ->join('turmas', 'professor_disciplina_turma.turma_id', '=', 'turmas.id')
            ->join('disciplinas', 'professor_disciplina_turma.disciplina_id', '=', 'disciplinas.id')
            ->where('professor_disciplina_turma.professor_id', $professor->id)
            ->select(
                'turmas.id as turma_id',
                'turmas.nome as turma_nome',
                'turmas.serie',
                'turmas.turno',
                'disciplinas.id as disciplina_id',
                'disciplinas.nome as disciplina_nome'
            )
            ->get()
            ->groupBy('turma_id')
            ->map(function ($items) {
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
            });
        
        // Total de alunos sob responsabilidade do professor
        $turmaIds = $turmasComDisciplinas->keys();
        $totalAlunos = Aluno::whereIn('turma_id', $turmaIds)->count();
        
        // Alunos por turma para o professor
        $alunosPorTurma = Aluno::whereIn('turma_id', $turmaIds)
            ->select('turma_id', DB::raw('COUNT(*) as total'))
            ->groupBy('turma_id')
            ->get()
            ->keyBy('turma_id');
        
        // Chamadas referentes apenas às turmas e disciplinas do professor
        $disciplinaIds = collect($turmasComDisciplinas)
            ->flatMap(function ($turma) {
                return collect($turma['disciplinas'])->pluck('id');
            })
            ->unique()
            ->values();
        
        // Chamadas recentes agrupadas por data e disciplina
        $chamadasRecentes = Chamada::where('professor_id', $professor->id)
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

        // Chamadas agrupadas por data para estatísticas
        $chamadasAgrupadasPorData = Chamada::where('professor_id', $professor->id)
            ->whereIn('disciplina_id', $disciplinaIds)
            ->with(['disciplina', 'aluno'])
            ->orderBy('data_chamada', 'desc')
            ->limit(50)
            ->get()
            ->groupBy(function($chamada) {
                return $chamada->data_chamada->format('Y-m-d');
            })
            ->map(function ($chamadas, $data) {
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
            })
            ->take(7);
        
        // Estatísticas de frequência do professor
        $estatisticasChamadas = Chamada::where('professor_id', $professor->id)
            ->whereIn('disciplina_id', $disciplinaIds)
            ->selectRaw('
                COUNT(*) as total_registros,
                SUM(CASE WHEN status = "presente" THEN 1 ELSE 0 END) as total_presencas,
                SUM(CASE WHEN status = "falta" THEN 1 ELSE 0 END) as total_faltas
            ')
            ->first();
        
        $totalRegistros = $estatisticasChamadas->total_registros ?? 0;
        $totalPresencas = $estatisticasChamadas->total_presencas ?? 0;
        $totalFaltas = $estatisticasChamadas->total_faltas ?? 0;
        $percentualFrequencia = $totalRegistros > 0 ? round(($totalPresencas / $totalRegistros) * 100, 1) : 0;
        
        // Alunos com mais faltas nas disciplinas do professor
        $alunosComMaisFaltas = Chamada::where('professor_id', $professor->id)
            ->whereIn('disciplina_id', $disciplinaIds)
            ->where('status', 'falta')
            ->select('matricula')
            ->groupBy('matricula')
            ->selectRaw('matricula, COUNT(*) as total_faltas')
            ->orderBy('total_faltas', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $aluno = Aluno::where('numero_matricula', $item->matricula)->first();
                return [
                    'nome' => $aluno ? $aluno->nome : 'Aluno não encontrado',
                    'matricula' => $item->matricula,
                    'total_faltas' => $item->total_faltas
                ];
            });

        // Disciplinas do professor para estatísticas
        $disciplinas = Disciplina::whereIn('id', $disciplinaIds)->get();

        // Estatísticas adicionais para o dashboard melhorado
        $totalChamadasRealizadas = Chamada::where('professor_id', $professor->id)
            ->whereIn('disciplina_id', $disciplinaIds)
            ->selectRaw('COUNT(DISTINCT CONCAT(disciplina_id, "-", data_chamada)) as total')
            ->value('total') ?? 0;
        
        return view('professor.dashboard', compact(
            'professor',
            'turmasComDisciplinas',
            'totalAlunos',
            'alunosPorTurma',
            'chamadasRecentes',
            'chamadasAgrupadasPorData',
            'totalRegistros',
            'totalPresencas',
            'totalFaltas',
            'percentualFrequencia',
            'alunosComMaisFaltas',
            'disciplinas',
            'totalChamadasRealizadas',
        ));
    }

    /**
     * Dashboard para alunos
     */
    public function aluno(): View
    {
        $aluno = auth()->user()->aluno;
        
        if (!$aluno) {
            abort(403, 'Aluno não encontrado');
        }
        
        // Dados específicos para o dashboard do aluno
        $turma = $aluno->turma;
        $avaliacoes = $aluno->avaliacoes()->with('disciplina')->latest()->take(5)->get();
        $chamadas = Chamada::porAluno($aluno->numero_matricula)
            ->with(['disciplina', 'professor'])
            ->latest('data_chamada')
            ->limit(10)
            ->get();
        $faltas = $chamadas->where('status', 'falta')->take(5);
        
        return view('aluno.dashboard', compact(
            'aluno',
            'turma',
            'avaliacoes',
            'faltas'
        ));
    }

    /**
     * Retorna as turmas vinculadas ao professor logado
     */
    public function turmasProfessor(): JsonResponse
    {
        $professor = auth()->user()->professor;
        
        if (!$professor) {
            return response()->json([
                'success' => false,
                'message' => 'Professor não encontrado'
            ], 403);
        }
        
        $turmas = DB::table('professor_disciplina_turma')
            ->join('turmas', 'professor_disciplina_turma.turma_id', '=', 'turmas.id')
            ->join('disciplinas', 'professor_disciplina_turma.disciplina_id', '=', 'disciplinas.id')
            ->where('professor_disciplina_turma.professor_id', $professor->id)
            ->select(
                'turmas.id',
                'turmas.nome',
                'turmas.serie',
                'turmas.turno',
                'turmas.capacidade_maxima',
                'disciplinas.id as disciplina_id',
                'disciplinas.nome as disciplina_nome',
                'disciplinas.codigo as disciplina_codigo'
            )
            ->get()
            ->groupBy('id')
            ->map(function ($items) {
                $firstItem = $items->first();
                $totalAlunos = Aluno::where('turma_id', $firstItem->id)->count();
                
                return [
                    'id' => $firstItem->id,
                    'nome' => $firstItem->nome,
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
            })
            ->values();
        
        return response()->json([
            'success' => true,
            'data' => $turmas
        ]);
    }

    /**
     * Lista os alunos de uma turma específica do professor
     */
    public function alunosTurma(ValidateTurmaProfessorRequest $request): JsonResponse
    {
        $turmaId = $request->validated()['turma_id'];
        
        $alunos = Aluno::where('turma_id', $turmaId)
            ->with(['user:id,email', 'turma:id,nome'])
            ->select(
                'id',
                'nome',
                'numero_matricula',
                'data_nascimento',
                'turma_id',
                'user_id',
                'foto_perfil'
            )
            ->orderBy('nome')
            ->get()
            ->map(function ($aluno) {
                return [
                    'id' => $aluno->id,
                    'nome' => $aluno->nome,
                    'numero_matricula' => $aluno->numero_matricula,
                    'data_nascimento' => $aluno->data_nascimento?->format('d/m/Y'),
                    'idade' => $aluno->data_nascimento?->age,
                    'email' => $aluno->user?->email,
                    'turma' => $aluno->turma?->nome,
                    'foto_perfil_url' => $aluno->foto_perfil_url
                ];
            });
        
        // Buscar informações da turma
        $turma = DB::table('turmas')
            ->where('id', $turmaId)
            ->select('id', 'nome', 'serie', 'turno', 'capacidade_maxima')
            ->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'turma' => [
                    'id' => $turma->id,
                    'nome' => $turma->nome,
                    'serie' => ucfirst($turma->serie),
                    'turno' => ucfirst($turma->turno),
                    'capacidade_maxima' => $turma->capacidade_maxima,
                    'total_alunos' => $alunos->count()
                ],
                'alunos' => $alunos
            ]
        ]);
    }
}
