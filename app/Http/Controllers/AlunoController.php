<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlunoStoreRequest;
use App\Http\Requests\AlunoUpdateRequest;
use App\Http\Requests\AvaliacaoUpdateRequest;
use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Chamada;
use App\Models\Disciplina;
use App\Models\Turma;
use App\Services\AvaliacaoService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlunoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Aluno::with('turma');

        // Filtro de busca por nome, email ou número de matrícula
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('numero_matricula', 'like', "%{$search}%");
            });
        }

        // Filtro por status de matrícula
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'ativo') {
                $query->where('status_matricula', 'ativa');
            } elseif ($status === 'inativo') {
                $query->whereIn('status_matricula', ['inativa', 'cancelada', 'transferida']);
            }
        }

        // Filtro por turma
        if ($request->filled('turma_id')) {
            $query->where('turma_id', $request->get('turma_id'));
        }

        // Lógica de ordenação
        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        
        // Validar campos de ordenação permitidos
        $allowedSortFields = ['nome', 'email', 'data_nascimento', 'status_matricula'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'nome';
        }
        
        // Validar direção de ordenação
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $alunos = $query->orderBy($sortField, $sortDirection)->paginate(15)->withQueryString();
        
        // Buscar turmas para o filtro
        $turmas = Turma::where('ativo', true)->orderBy('nome')->get();

        return view('admin.alunos.index', compact('alunos', 'turmas', 'sortField', 'sortDirection'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $turmas = Turma::where('ativo', true)
            ->orderBy('nome')
            ->get();
            
        $turmaSelecionada = $request->get('turma_id');
            
        return view('admin.alunos.create', compact('turmas', 'turmaSelecionada'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AlunoStoreRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        
        // Handle photo upload
        if ($request->hasFile('foto_perfil')) {
            $validatedData['foto_perfil'] = $this->handlePhotoUpload($request->file('foto_perfil'));
        }
        
        // Gerar dados de matrícula automaticamente
        $validatedData['numero_matricula'] = Aluno::gerarNumeroMatricula();
        $validatedData['data_matricula'] = now()->format('Y-m-d');
        $validatedData['status_matricula'] = 'ativa';
        
        Aluno::create($validatedData);

        return redirect()
            ->route('admin.alunos.index')
            ->with('success', 'Aluno criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Aluno $aluno): View
    {
        $aluno->load('turma');
        
        // Obter estatísticas de chamadas
        $totalPresencas = Chamada::porAluno($aluno->numero_matricula)
            ->where('status', 'presente')
            ->count();
            
        $faltasParaJustificar = Chamada::porAluno($aluno->numero_matricula)
            ->where('status', 'falta')
            ->where('justificada', false)
            ->with(['disciplina', 'professor'])
            ->orderBy('data_chamada', 'desc')
            ->get();
            
        return view('admin.alunos.show', compact('aluno', 'totalPresencas', 'faltasParaJustificar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aluno $aluno): View
    {
        $turmas = Turma::where('ativo', true)
            ->orderBy('nome')
            ->get();
            
        return view('admin.alunos.edit', compact('aluno', 'turmas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AlunoUpdateRequest $request, Aluno $aluno): RedirectResponse
    {
        $validatedData = $request->validated();
        
        // Handle photo upload
        if ($request->hasFile('foto_perfil')) {
            // Delete old photo if exists
            if ($aluno->foto_perfil && \Storage::disk('public')->exists($aluno->foto_perfil)) {
                \Storage::disk('public')->delete($aluno->foto_perfil);
            }
            $validatedData['foto_perfil'] = $this->handlePhotoUpload($request->file('foto_perfil'));
        }
        
        $aluno->update($validatedData);

        return redirect()
            ->route('admin.alunos.show', $aluno)
            ->with('success', 'Aluno atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aluno $aluno): RedirectResponse
    {
        $aluno->delete();

        return redirect()
            ->route('admin.alunos.index')
            ->with('success', 'Aluno excluído com sucesso!');
    }





    /**
     * Handle photo upload and return the stored path.
     */
    private function handlePhotoUpload($file): string
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Store in public disk under alunos folder
        $path = $file->storeAs('alunos', $filename, 'public');
        
        return $path;
    }

    /**
     * Exibe o boletim de avaliações do aluno
     */
    public function boletim(Aluno $aluno, AvaliacaoService $avaliacaoService): View
    {
        $avaliacoes = $avaliacaoService->obterAvaliacoesDoAluno($aluno);
        
        return view('admin.alunos.notas.boletim', compact('aluno', 'avaliacoes'));
    }

    /**
     * Atualiza as notas de uma avaliação específica
     */
    public function atualizarAvaliacao(
        AvaliacaoUpdateRequest $request, 
        Aluno $aluno, 
        Avaliacao $avaliacao,
        AvaliacaoService $avaliacaoService
    ): RedirectResponse {
        $avaliacaoService->atualizarNotas($avaliacao, $request->validated());
        
        return redirect()
            ->route('admin.alunos.boletim', $aluno)
            ->with('success', 'Notas atualizadas com sucesso!');
    }
    
    /**
     * Retorna as presenças de um aluno via AJAX
     */
    public function presencasAluno(Request $request, Aluno $aluno)
    {
        $dataInicio = $request->get('data_inicio');
        $dataFim = $request->get('data_fim');
        $disciplinaId = $request->get('disciplina_id');
        
        $query = Chamada::with(['disciplina', 'professor'])
            ->porAluno($aluno->numero_matricula)
            ->presencas();
        
        if ($dataInicio && $dataFim) {
            $query->porPeriodo(Carbon::parse($dataInicio), Carbon::parse($dataFim));
        }
        
        if ($disciplinaId) {
            $query->porDisciplina($disciplinaId);
        }
        
        $presencas = $query->orderBy('data_chamada', 'desc')->paginate(10);
        
        return response()->json([
            'presencas' => $presencas->items(),
            'pagination' => [
                'current_page' => $presencas->currentPage(),
                'last_page' => $presencas->lastPage(),
                'per_page' => $presencas->perPage(),
                'total' => $presencas->total()
            ]
        ]);
    }

    /**
     * Retorna as disciplinas que o aluno teve chamadas
     */
    public function disciplinasAluno(Aluno $aluno)
    {
        $disciplinas = Disciplina::whereHas('chamadas', function ($query) use ($aluno) {
            $query->where('matricula', $aluno->numero_matricula);
        })->select('id', 'nome')->get();

        return response()->json(['disciplinas' => $disciplinas]);
    }
}
