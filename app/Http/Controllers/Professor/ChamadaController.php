<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessorChamadaRequest;
use App\Models\Aluno;
use App\Models\Chamada;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Models\Turma;
use App\Services\ChamadaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChamadaController extends Controller
{
    private ChamadaService $chamadaService;
    
    public function __construct(ChamadaService $chamadaService)
    {
        $this->chamadaService = $chamadaService;
    }

    /**
     * Exibe a lista de turmas para chamada (Professor)
     */
    public function index()
    {
        $professor = $this->obterProfessorAutenticado();
        $turmasComVinculo = $this->chamadaService->obterTurmasComVinculoProfessor($professor->id);
        
        return view('professor.chamadas.index', compact('turmasComVinculo'));
    }

    /**
     * Processar lançamento de chamada (redirecionamento)
     */
    public function lancar(ProfessorChamadaRequest $request): RedirectResponse
    {
        return redirect()->route('professor.chamada.fazer', [
            'turma' => $request->turma_id,
            'disciplina' => $request->disciplina_id
        ])->with('data', $request->data_chamada);
    }
    
    /**
     * Exibir interface de chamada
     */
    public function fazer(Request $request, int $turmaId, int $disciplinaId): View
    {
        $professor = $this->obterProfessorAutenticado();
        $data = $this->obterDataChamada($request);
        
        $this->validarPermissaoAcesso($professor, $turmaId, $disciplinaId);
        
        $contexto = $this->prepararContextoChamada($turmaId, $disciplinaId, $professor, $data);
        
        return view('professor.chamada.fazer', $contexto);
    }

    /**
     * Exibe relatórios de chamadas para professores
     */
    public function relatorio(Request $request)
    {
        $professor = $this->obterProfessorAutenticado();
        
        $filtros = $this->extrairFiltrosRelatorio($request);
        $turmasComVinculo = $this->obterTurmasVinculadasProfessor($professor->id);
        
        $estatisticas = $this->chamadaService->obterEstatisticasChamadasProfessor($professor, $filtros);
        $chamadas = $this->chamadaService->obterChamadasDetalhadasProfessor($professor, $filtros);
        
        return view('professor.chamadas.relatorio', array_merge(
            compact('turmasComVinculo', 'chamadas'),
            $filtros,
            $estatisticas
        ));
    }

    /**
     * Exibe interface para gerenciar chamadas de uma turma/disciplina (Professor)
     */
    public function gerenciar(Request $request, $turma, $disciplina)
    {
        $professor = $this->obterProfessorAutenticado();
        
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        
        $turma = Turma::findOrFail($turma);
        $disciplina = Disciplina::findOrFail($disciplina);
        
        $this->verificarVinculoProfessor($professor->id, $turma->id, $disciplina->id);
        
        $chamadasPorDia = $this->chamadaService->obterChamadasPorDia(
            $turma->id, 
            $disciplina->id, 
            $professor->id, 
            $dataInicio, 
            $dataFim
        );
        
        return view('professor.chamadas.gerenciar', compact(
            'turma', 'disciplina', 'professor', 'chamadasPorDia', 'dataInicio', 'dataFim'
        ));
    }
    
    /**
     * Salvar chamada
     */
    public function salvar(ProfessorChamadaRequest $request): RedirectResponse
    {
        $professor = $this->obterProfessorAutenticado();
        
        $resultado = $this->chamadaService->processarChamada(
            $professor,
            $request->validated()
        );
        
        if (!$resultado['sucesso']) {
            return $this->redirecionarComAviso($resultado);
        }
        
        return $this->redirecionarComSucesso($request, $resultado);
    }
    
    /**
     * Obter professor autenticado
     */
    private function obterProfessorAutenticado(): Professor
    {
        return auth()->user()->professor;
    }
    
    /**
     * Obter data da chamada
     */
    private function obterDataChamada(Request $request): string
    {
        return $request->get('data', session('data', now()->format('Y-m-d')));
    }
    
    /**
     * Validar permissão de acesso
     */
    private function validarPermissaoAcesso(Professor $professor, int $turmaId, int $disciplinaId): void
    {
        $temPermissao = $professor->disciplinasComTurma()
            ->where('professor_disciplina_turma.turma_id', $turmaId)
            ->where('disciplinas.id', $disciplinaId)
            ->exists();
            
        if (!$temPermissao) {
            abort(403, 'Você não tem permissão para acessar esta turma/disciplina.');
        }
    }
    
    /**
     * Preparar contexto para a view de chamada
     */
    private function prepararContextoChamada(int $turmaId, int $disciplinaId, Professor $professor, string $data): array
    {
        $turma = Turma::findOrFail($turmaId);
        $disciplina = Disciplina::findOrFail($disciplinaId);
        $alunos = $this->obterAlunosAtivos($turmaId);
        
        $presencasExistentes = $this->obterPresencasExistentes(
            $disciplinaId, 
            $professor->id, 
            $data, 
            $alunos->pluck('numero_matricula')->toArray()
        );
        
        $chamadaJaLancada = $this->verificarChamadaJaLancada(
            $disciplinaId, 
            $professor->id, 
            $data, 
            $alunos->pluck('numero_matricula')->toArray()
        );
        
        return compact(
            'turma', 'disciplina', 'professor', 'alunos', 
            'presencasExistentes', 'data', 'chamadaJaLancada'
        );
    }

    // Métodos privados adicionais para relatórios e gerenciamento
    
    private function extrairFiltrosRelatorio(Request $request): array
    {
        return [
            'data_inicio' => $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d')),
            'data_fim' => $request->get('data_fim', now()->format('Y-m-d')),
            'turma_id' => $request->get('turma_id'),
            'disciplina_id' => $request->get('disciplina_id'),
            'busca_aluno' => $request->get('busca_aluno')
        ];
    }

    private function obterTurmasVinculadasProfessor(int $professorId)
    {
        return \Illuminate\Support\Facades\DB::table('professor_disciplina_turma')
            ->join('turmas', 'professor_disciplina_turma.turma_id', '=', 'turmas.id')
            ->join('disciplinas', 'professor_disciplina_turma.disciplina_id', '=', 'disciplinas.id')
            ->where('professor_disciplina_turma.professor_id', $professorId)
            ->select(
                'turmas.id as turma_id',
                'turmas.nome as turma_nome',
                'turmas.serie',
                'turmas.turno',
                'disciplinas.id as disciplina_id',
                'disciplinas.nome as disciplina_nome',
                'disciplinas.codigo as disciplina_codigo'
            )
            ->get();
    }

    private function verificarVinculoProfessor(int $professorId, int $turmaId, int $disciplinaId): void
    {
        if (!$this->chamadaService->professorTemVinculo($professorId, $turmaId, $disciplinaId)) {
            abort(403, 'Você não tem permissão para gerenciar chamadas desta turma/disciplina.');
        }
    }
    
    /**
     * Obter alunos ativos da turma
     */
    private function obterAlunosAtivos(int $turmaId)
    {
        return Aluno::where('turma_id', $turmaId)
            ->where('status_matricula', 'ativa')
            ->orderBy('nome')
            ->get();
    }
    
    /**
     * Obter presenças existentes
     */
    private function obterPresencasExistentes(int $disciplinaId, int $professorId, string $data, array $matriculas): array
    {
        return Chamada::where('disciplina_id', $disciplinaId)
            ->where('professor_id', $professorId)
            ->whereDate('data_chamada', $data)
            ->whereIn('matricula', $matriculas)
            ->where('status', 'presente')
            ->pluck('matricula')
            ->toArray();
    }
    
    /**
     * Verificar se chamada já foi lançada
     */
    private function verificarChamadaJaLancada(int $disciplinaId, int $professorId, string $data, array $matriculas): bool
    {
        return Chamada::where('disciplina_id', $disciplinaId)
            ->where('professor_id', $professorId)
            ->whereDate('data_chamada', $data)
            ->whereIn('matricula', $matriculas)
            ->exists();
    }
    
    /**
     * Redirecionar com aviso
     */
    private function redirecionarComAviso(array $resultado): RedirectResponse
    {
        return redirect()->back()
            ->with('warning', $resultado['mensagem'])
            ->with('mostrar_confirmacao', true)
            ->withInput();
    }
    
    /**
     * Redirecionar com sucesso
     */
    private function redirecionarComSucesso(ProfessorChamadaRequest $request, array $resultado): RedirectResponse
    {
        return redirect()->route('professor.chamadas.gerenciar', [
            'turma' => $request->turma_id,
            'disciplina' => $request->disciplina_id
        ])->with('success', $resultado['mensagem']);
    }
}