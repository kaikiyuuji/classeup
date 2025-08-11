<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Chamada;

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
        $totalAlunos = \App\Models\Aluno::count();
        $totalProfessores = \App\Models\Professor::count();
        $totalTurmas = \App\Models\Turma::count();
        $totalDisciplinas = \App\Models\Disciplina::count();
        
        // Estatísticas avançadas
        $alunosAtivos = \App\Models\Aluno::whereHas('user')->count();
        $professoresAtivos = \App\Models\Professor::where('ativo', true)->count();
        $turmasComAlunos = \App\Models\Turma::has('alunos')->count();
        
        // Distribuição de alunos por turma
        $alunosPorTurma = \App\Models\Turma::withCount('alunos')
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
        $alunosPorNivel = \App\Models\Turma::select('serie', DB::raw('COUNT(alunos.id) as alunos_count'))
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
        $atividadesRecentes = \App\Models\Chamada::select(
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
        $professoresMaisAtivos = \App\Models\Professor::select(
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
        $estatisticasChamadas = \App\Models\Chamada::select(
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
        $alunosComMaisFaltas = \App\Models\Chamada::select('matricula')
            ->where('status', 'falta')
            ->groupBy('matricula')
            ->selectRaw('matricula, COUNT(*) as total_faltas')
            ->orderBy('total_faltas', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $aluno = \App\Models\Aluno::where('numero_matricula', $item->matricula)->first();
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
        
        // Dados específicos para o dashboard do professor
        $turmas = $professor->turmas()->with('alunos')->get();
        $disciplinas = $professor->disciplinas;
        $totalAlunos = $turmas->sum(fn($turma) => $turma->alunos->count());
        
        return view('professor.dashboard', compact(
            'professor',
            'turmas',
            'disciplinas',
            'totalAlunos'
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
}
