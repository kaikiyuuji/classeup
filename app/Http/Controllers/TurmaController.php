<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TurmaStoreRequest;
use App\Http\Requests\TurmaUpdateRequest;
use App\Models\Turma;
use App\Models\Aluno;
use App\Models\Professor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\VincularAlunosRequest;
use App\Http\Requests\DesvincularAlunoRequest;
use App\Http\Requests\VincularProfessorRequest;
use App\Http\Requests\DesvincularProfessorRequest;


class TurmaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Turma::with('alunos');

        // Filtro de busca por nome
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nome', 'like', "%{$search}%");
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

        // Filtro por nível educacional
        if ($request->filled('nivel_educacional')) {
            $query->where('serie', $request->get('nivel_educacional'));
        }

        // Filtro por turno
        if ($request->filled('turno')) {
            $query->where('turno', $request->get('turno'));
        }

        // Lógica de ordenação
        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        
        // Validar campos de ordenação permitidos
        $allowedSortFields = ['nome', 'nivel_educacional', 'turno', 'ativo'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'nome';
        }
        
        // Validar direção de ordenação
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $turmas = $query->orderBy($sortField, $sortDirection)->paginate(15)->withQueryString();

        return view('admin.turmas.index', compact('turmas', 'sortField', 'sortDirection'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.turmas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TurmaStoreRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        
        Turma::create($validatedData);

        return redirect()
            ->route('admin.turmas.index')
            ->with('success', 'Turma criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Turma $turma): View
    {
        $turma->load(['alunos', 'professores', 'disciplinas']);
        
        // Buscar alunos disponíveis (sem turma)
        $alunosDisponiveis = Aluno::whereNull('turma_id')
            ->orderBy('nome')
            ->get();
            
        // Buscar professores ativos com suas disciplinas que não estão vinculados à turma
        $professoresJaVinculados = $turma->professores()->pluck('professor_id')->toArray();
        $professoresDisponiveis = Professor::with('disciplinas')
            ->where('ativo', true)
            ->whereNotIn('id', $professoresJaVinculados)
            ->whereHas('disciplinas') // Apenas professores que têm disciplinas vinculadas
            ->orderBy('nome')
            ->get();
        
        return view('admin.turmas.show', compact('turma', 'alunosDisponiveis', 'professoresDisponiveis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Turma $turma): View
    {
        return view('admin.turmas.edit', compact('turma'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TurmaUpdateRequest $request, Turma $turma): RedirectResponse
    {
        $validatedData = $request->validated();
        
        // Garantir que o campo 'ativo' seja sempre processado
        $validatedData['ativo'] = $request->has('ativo') ? (bool) $request->input('ativo') : false;
        
        $turma->update($validatedData);

        return redirect()
            ->route('admin.turmas.show', $turma)
            ->with('success', 'Turma atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Turma $turma): RedirectResponse
    {
        $turma->delete();

        return redirect()
            ->route('admin.turmas.index')
            ->with('success', 'Turma excluída com sucesso!');
    }

    /**
     * Vincular múltiplos alunos à turma.
     */
    public function vincularAlunos(VincularAlunosRequest $request, Turma $turma): RedirectResponse
    {
        // Vincula apenas alunos que ainda não estão na turma
        $alunosJaVinculados = $turma->alunos()->pluck('id')->toArray();
        $alunosParaVincular = array_diff($request->validated()['alunos'], $alunosJaVinculados);

        if (!empty($alunosParaVincular)) {
            // Atualiza o turma_id dos alunos selecionados
            Aluno::whereIn('id', $alunosParaVincular)->update(['turma_id' => $turma->id]);
            $quantidadeVinculada = count($alunosParaVincular);
            return redirect()->route('admin.turmas.show', $turma)
                ->with('success', "{$quantidadeVinculada} aluno(s) vinculado(s) com sucesso!");
        }

        return redirect()->route('admin.turmas.show', $turma)
            ->with('info', 'Todos os alunos selecionados já estão vinculados à turma.');
    }

    /**
     * Desvincular aluno da turma.
     */
    public function desvincularAluno(DesvincularAlunoRequest $request, Turma $turma, Aluno $aluno): RedirectResponse
    {
        // Remove a vinculação definindo turma_id como null
        $aluno->update(['turma_id' => null]);
        
        return redirect()
            ->route('admin.turmas.show', $turma)
            ->with('success', "Aluno {$aluno->nome} desvinculado com sucesso!");
    }

    /**
     * Vincular professor e disciplina à turma.
     */
    public function vincularProfessor(VincularProfessorRequest $request, Turma $turma): RedirectResponse
    {
        $professorId = $request->validated()['professor_id'];
        $disciplinaId = $request->validated()['disciplina_id'];
        
        // Criar o vínculo usando Eloquent
        $turma->professores()->attach($professorId, [
            'disciplina_id' => $disciplinaId
        ]);
        
        return redirect()
            ->route('admin.turmas.show', $turma)
            ->with('success', 'Professor vinculado com sucesso!');
    }

    /**
     * Desvincular professor e disciplina da turma.
     */
    public function desvincularProfessor(DesvincularProfessorRequest $request, Turma $turma): RedirectResponse
    {
        $professorId = $request->validated()['professor_id'];
        $disciplinaId = $request->validated()['disciplina_id'];
        
        // Remover o vínculo usando Eloquent
        $turma->professores()
            ->wherePivot('disciplina_id', $disciplinaId)
            ->detach($professorId);
        
        return redirect()
            ->route('admin.turmas.show', $turma)
            ->with('success', 'Professor desvinculado com sucesso!');
    }
}
