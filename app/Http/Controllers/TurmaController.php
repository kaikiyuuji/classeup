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
    public function index(): View
    {
        $turmas = Turma::orderBy('nome')
            ->paginate(15);

        return view('admin.turmas.index', compact('turmas'));
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
            ->route('turmas.index')
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
            ->route('turmas.show', $turma)
            ->with('success', 'Turma atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Turma $turma): RedirectResponse
    {
        $turma->delete();

        return redirect()
            ->route('turmas.index')
            ->with('success', 'Turma excluída com sucesso!');
    }

    /**
     * Vincular múltiplos alunos à turma.
     */
    public function vincularAlunos(VincularAlunosRequest $request, Turma $turma): RedirectResponse
    {
        // Vincula apenas alunos que ainda não estão na turma
        $alunosJaVinculados = $turma->alunos()->pluck('aluno_id')->toArray();
        $alunosParaVincular = array_diff($request->validated()['alunos'], $alunosJaVinculados);

        if (!empty($alunosParaVincular)) {
            $turma->alunos()->attach($alunosParaVincular);
            $quantidadeVinculada = count($alunosParaVincular);
            return redirect()->route('turmas.show', $turma)
                ->with('success', "{$quantidadeVinculada} aluno(s) vinculado(s) com sucesso!");
        }

        return redirect()->route('turmas.show', $turma)
            ->with('info', 'Todos os alunos selecionados já estão vinculados à turma.');
    }

    /**
     * Desvincular aluno da turma.
     */
    public function desvincularAluno(DesvincularAlunoRequest $request, Turma $turma): RedirectResponse
    {
        $aluno = Aluno::findOrFail($request->validated()['aluno_id']);
        
        $turma->alunos()->detach($aluno->id);
        
        return redirect()
            ->route('turmas.show', $turma)
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
            ->route('turmas.show', $turma)
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
            ->route('turmas.show', $turma)
            ->with('success', 'Professor desvinculado com sucesso!');
    }
}
