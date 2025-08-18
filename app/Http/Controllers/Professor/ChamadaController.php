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