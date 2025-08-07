<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlunoStoreRequest;
use App\Http\Requests\AlunoUpdateRequest;
use App\Http\Requests\AvaliacaoUpdateRequest;
use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Turma;
use App\Services\AvaliacaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlunoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $alunos = Aluno::with('turma')
            ->orderBy('nome')
            ->paginate(15);

        return view('admin.alunos.index', compact('alunos'));
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
            ->route('alunos.index')
            ->with('success', 'Aluno criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Aluno $aluno): View
    {
        $aluno->load('turma');
            
        return view('admin.alunos.show', compact('aluno'));
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
            ->route('alunos.show', $aluno)
            ->with('success', 'Aluno atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aluno $aluno): RedirectResponse
    {
        $aluno->delete();

        return redirect()
            ->route('alunos.index')
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
            ->route('alunos.boletim', $aluno)
            ->with('success', 'Notas atualizadas com sucesso!');
    }
}
