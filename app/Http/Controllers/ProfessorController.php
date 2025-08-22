<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessorDeleteRequest;
use App\Http\Requests\ProfessorStoreRequest;
use App\Http\Requests\ProfessorUpdateRequest;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Services\ProfessorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfessorController extends Controller
{
    private ProfessorService $professorService;
    
    public function __construct(ProfessorService $professorService)
    {
        $this->professorService = $professorService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Professor::query();

        // Filtro de busca por nome, email ou especialidade
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('especialidade', 'like', "%{$search}%")
                  ->orWhere('formacao', 'like', "%{$search}%");
            });
        }

        // Filtro por status
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        // Filtro por especialidade
        if ($request->filled('especialidade')) {
            $query->where('especialidade', $request->get('especialidade'));
        }

        // Filtro por disciplina
        if ($request->filled('disciplina_id')) {
            $query->whereHas('disciplinas', function ($q) use ($request) {
                $q->where('disciplinas.id', $request->get('disciplina_id'));
            });
        }

        // Lógica de ordenação
        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        
        // Validar campos de ordenação permitidos
        $allowedSortFields = ['nome', 'email', 'especialidade', 'ativo'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'nome';
        }
        
        // Validar direção de ordenação
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $professores = $query->orderBy($sortField, $sortDirection)->paginate(15)->withQueryString();
        
        // Buscar especialidades únicas para o filtro
        $especialidades = Professor::whereNotNull('especialidade')
            ->distinct()
            ->pluck('especialidade')
            ->filter()
            ->sort()
            ->values();
            
        // Buscar disciplinas para o filtro
        $disciplinas = Disciplina::where('ativo', true)->orderBy('nome')->get();

        return view('admin.professores.index', compact('professores', 'especialidades', 'disciplinas', 'sortField', 'sortDirection'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.professores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfessorStoreRequest $request): RedirectResponse
    {
        $this->professorService->criarProfessor($request->validated());

        return redirect()
            ->route('admin.professores.index')
            ->with('success', 'Professor criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Professor $professor): View
    {
        $contexto = $this->prepararContextoVisualizacao($professor);
        
        return view('admin.professores.show', $contexto);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Professor $professor): View
    {
        return view('admin.professores.edit', compact('professor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfessorUpdateRequest $request, Professor $professor): RedirectResponse
    {
        $this->professorService->atualizarProfessor($professor, $request->validated());

        return redirect()
            ->route('admin.professores.show', $professor)
            ->with('success', 'Professor atualizado com sucesso!');
    }

    /**
     * Verifica relacionamentos existentes do professor.
     */
    public function verificarRelacionamentos(Professor $professor)
    {
        $relacionamentos = $this->professorService->verificarRelacionamentosExistentes($professor);
        
        return response()->json($relacionamentos);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProfessorDeleteRequest $request, Professor $professor): RedirectResponse
    {
        try {
            $this->professorService->excluirProfessorComRelacionamentos($professor);
            
            return redirect()->route('admin.professores.index')
                ->with('success', 'Professor e todos os dados relacionados foram excluídos com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir professor: ' . $e->getMessage());
        }
    }



    /**
     * Vincular professor a uma disciplina
     */
    public function vincularDisciplina(Request $request, Professor $professor): RedirectResponse
    {
        $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id'
        ]);
        
        $resultado = $this->professorService->vincularDisciplina($professor, $request->disciplina_id);
        
        $tipoMensagem = $resultado['sucesso'] ? 'success' : 'error';
        
        return redirect()
            ->route('admin.professores.show', $professor)
            ->with($tipoMensagem, $resultado['mensagem']);
    }
    
    /**
     * Desvincular professor de uma disciplina
     */
    public function desvincularDisciplina(Request $request, Professor $professor): RedirectResponse
    {
        $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id'
        ]);
        
        $resultado = $this->professorService->desvincularDisciplina($professor, $request->disciplina_id);
        
        $tipoMensagem = $resultado['sucesso'] ? 'success' : 'error';
        
        return redirect()
            ->route('admin.professores.show', $professor)
            ->with($tipoMensagem, $resultado['mensagem']);
    }

    /**
     * Exibe as turmas vinculadas ao professor autenticado
     */
    public function minhasTurmas(): View
    {
        $professor = auth()->user()->professor;
        
        if (!$professor) {
            abort(403, 'Professor não encontrado');
        }
        
        // Buscar turmas vinculadas ao professor com suas disciplinas específicas
        $turmasComVinculo = DB::table('professor_disciplina_turma')
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
            ->groupBy('turma_id')
            ->map(function ($items) {
                $firstItem = $items->first();
                return (object) [
                    'turma_id' => $firstItem->turma_id,
                    'turma_nome' => $firstItem->turma_nome,
                    'serie' => $firstItem->serie,
                    'turno' => $firstItem->turno,
                    'capacidade_maxima' => $firstItem->capacidade_maxima,
                    'disciplinas' => $items->map(function ($item) {
                        return (object) [
                            'disciplina_id' => $item->disciplina_id,
                            'disciplina_nome' => $item->disciplina_nome,
                            'disciplina_codigo' => $item->disciplina_codigo
                        ];
                    })
                ];
            });
        
        return view('professor.turmas.index', compact('turmasComVinculo', 'professor'));
    }

    /**
     * Preparar contexto para visualização do professor
     */
    private function prepararContextoVisualizacao(Professor $professor): array
    {
        $disciplinasVinculadas = $professor->disciplinas;
        $disciplinasDisponiveis = $this->obterDisciplinasDisponiveis($disciplinasVinculadas);
        
        return compact('professor', 'disciplinasVinculadas', 'disciplinasDisponiveis');
    }
    
    /**
     * Obter disciplinas disponíveis para vinculação
     */
    private function obterDisciplinasDisponiveis($disciplinasVinculadas)
    {
        return Disciplina::whereNotIn('id', $disciplinasVinculadas->pluck('id'))
                        ->orderBy('nome')
                        ->get();
    }
}
