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
use App\Services\DashboardService;
use App\Http\Resources\TurmaComAlunosResource;
use App\Http\Resources\AlunoTurmaResource;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}
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
        $estatisticasBasicas = $this->dashboardService->obterEstatisticasBasicas();
        $alunosPorTurma = $this->dashboardService->obterDistribuicaoAlunosPorTurma();
        $alunosPorNivel = $this->dashboardService->obterDistribuicaoAlunosPorNivel();
        $atividadesRecentes = $this->dashboardService->obterAtividadesRecentes();
        $professoresMaisAtivos = $this->dashboardService->obterProfessoresMaisAtivos();
        $estatisticasFrequencia = $this->dashboardService->obterEstatisticasFrequencia();
        $alunosComMaisFaltas = $this->dashboardService->obterAlunosComMaisFaltas();
        
        return view('admin.dashboard', array_merge(
            $estatisticasBasicas,
            [
                'alunosPorTurma' => $alunosPorTurma,
                'alunosPorNivel' => $alunosPorNivel,
                'atividadesRecentes' => $atividadesRecentes,
                'professoresMaisAtivos' => $professoresMaisAtivos,
                'alunosComMaisFaltas' => $alunosComMaisFaltas
            ],
            $estatisticasFrequencia
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
        
        $dadosProfessor = $this->dashboardService->obterDadosProfessor($professor);
        $alunosComMaisFaltas = $this->dashboardService->obterAlunosComMaisFaltas(5, $professor->id);
        
        return view('professor.dashboard', array_merge(
            ['professor' => $professor],
            $dadosProfessor,
            ['alunosComMaisFaltas' => $alunosComMaisFaltas]
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
        
        $turmasComDisciplinas = $this->dashboardService->obterTurmasComDisciplinasProfessor($professor);
        
        return response()->json([
            'success' => true,
            'data' => $turmasComDisciplinas->values()
        ]);
    }

    /**
     * Lista os alunos de uma turma específica do professor
     */
    public function alunosTurma(ValidateTurmaProfessorRequest $request): JsonResponse
    {
        $turmaId = $request->validated()['turma_id'];
        
        $turma = Turma::with([
                'alunos' => function ($query) {
                    $query->with(['user:id,email'])
                          ->select('id', 'nome', 'numero_matricula', 'data_nascimento', 'turma_id', 'user_id', 'foto_perfil')
                          ->orderBy('nome');
                }
            ])
            ->withCount('alunos')
            ->findOrFail($turmaId);
        
        return response()->json([
            'success' => true,
            'data' => new TurmaComAlunosResource($turma)
        ]);
    }
}
