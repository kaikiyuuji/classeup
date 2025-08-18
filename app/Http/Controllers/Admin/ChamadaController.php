<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chamada;
use App\Models\Turma;
use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Services\ChamadaService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChamadaController extends Controller
{
    private ChamadaService $chamadaService;

    public function __construct(ChamadaService $chamadaService)
    {
        $this->chamadaService = $chamadaService;
    }

    /**
     * Exibe a lista de turmas para chamada (Admin)
     */
    public function index()
    {
        $turmasComVinculo = $this->chamadaService->obterTurmasComVinculoAdmin();
        
        return view('admin.chamadas.index', compact('turmasComVinculo'));
    }

    /**
     * Exibe a interface de chamada para uma turma/disciplina específica
     */
    public function chamada(Request $request, $turma, $disciplina)
    {
        $professorId = $request->get('professor_id');
        $data = $request->get('data', now()->format('Y-m-d'));
        
        $turma = Turma::findOrFail($turma);
        $disciplina = Disciplina::findOrFail($disciplina);
        
        if (!$professorId) {
            $professorId = $this->chamadaService->obterProfessorVinculado($turma->id, $disciplina->id);
            
            if (!$professorId) {
                return redirect()->route('admin.chamadas.index')
                    ->with('error', 'Nenhum professor vinculado a esta turma/disciplina.');
            }
        }
        
        $professor = Professor::findOrFail($professorId);
        $alunos = $this->chamadaService->obterAlunosAtivos($turma->id);
        $presencasExistentes = $this->chamadaService->obterChamadasExistentes(
            $disciplina->id, 
            $professorId, 
            $data, 
            $turma->id
        );
        
        return view('admin.chamadas.chamada', compact(
            'turma', 'disciplina', 'professor', 'alunos', 'presencasExistentes', 'data'
        ));
    }

    /**
     * Registra as chamadas do dia
     */
    public function store(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'professor_id' => 'required|exists:professores,id',
            'data_chamada' => 'required|date',
            'presencas' => 'array',
            'presencas.*' => 'string',
            'confirmar_reenvio' => 'sometimes|boolean'
        ]);
        
        $resultado = $this->chamadaService->processarChamada($request->all());
        
        if (!$resultado['sucesso']) {
            return redirect()->back()
                           ->withInput()
                           ->with('warning', $resultado['mensagem'])
                           ->with('mostrar_confirmacao', true);
        }
        
        return redirect()->route('admin.chamadas.index')
            ->with('success', $resultado['mensagem']);
    }

    /**
     * Exibe relatório de chamadas por aluno
     */
    public function relatorioAluno(Request $request)
    {
        $matricula = $request->get('matricula');
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        
        $aluno = null;
        $chamadas = collect();
        
        if ($matricula) {
            $aluno = Aluno::where('numero_matricula', $matricula)->first();
            if ($aluno) {
                $chamadas = $this->chamadaService->obterChamadasDoAluno($matricula, $dataInicio, $dataFim);
            }
        }
        
        return view('admin.chamadas.relatorio-aluno', compact(
            'aluno', 'chamadas', 'matricula', 'dataInicio', 'dataFim'
        ));
    }

    /**
     * Exibe formulário para justificar falta
     */
    public function justificar($id)
    {
        $chamada = Chamada::with(['aluno', 'disciplina', 'professor'])->findOrFail($id);
        
        return view('admin.chamadas.justificar', compact('chamada'));
    }

    /**
     * Processa justificativa de falta
     */
    public function processarJustificativa(Request $request, $id)
    {
        $request->validate([
            'observacoes' => 'required|string|max:1000'
        ]);
        
        $chamada = Chamada::findOrFail($id);
        $chamada->justificar($request->observacoes);
        
        return redirect()->route('admin.chamadas.relatorio-aluno', ['matricula' => $chamada->matricula])
                        ->with('success', 'Falta justificada com sucesso!');
    }

    /**
     * Remove justificativa de falta
     */
    public function removerJustificativa($id)
    {
        $chamada = Chamada::findOrFail($id);
        $chamada->removerJustificativa();
        
        return redirect()->route('admin.chamadas.relatorio-aluno', ['matricula' => $chamada->matricula])
                        ->with('success', 'Justificativa removida com sucesso!');
    }

    /**
     * Exibe interface para gerenciar chamadas de uma turma/disciplina
     */
    public function gerenciar(Request $request, $turma, $disciplina)
    {
        $professorId = $request->get('professor_id');
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        
        $turma = Turma::findOrFail($turma);
        $disciplina = Disciplina::findOrFail($disciplina);
        
        if (!$professorId) {
            $professorId = $this->chamadaService->obterProfessorVinculado($turma->id, $disciplina->id);
            
            if (!$professorId) {
                return redirect()->route('admin.chamadas.index')
                    ->with('error', 'Nenhum professor vinculado a esta turma/disciplina.');
            }
        }
        
        $professor = Professor::findOrFail($professorId);
        $chamadasPorDia = $this->chamadaService->obterChamadasPorDia(
            $turma->id, 
            $disciplina->id, 
            $professorId, 
            $dataInicio, 
            $dataFim
        );
        
        return view('admin.chamadas.gerenciar', compact(
            'turma', 'disciplina', 'professor', 'chamadasPorDia', 'dataInicio', 'dataFim'
        ));
    }
}