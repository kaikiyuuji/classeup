<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TurmaStoreRequest;
use App\Http\Requests\TurmaUpdateRequest;
use App\Models\Turma;
use App\Models\Aluno;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        $turma->load('alunos');
        
        // Buscar alunos disponíveis (sem turma)
        $alunosDisponiveis = Aluno::whereNull('turma_id')
            ->orderBy('nome')
            ->get();
        
        return view('admin.turmas.show', compact('turma', 'alunosDisponiveis'));
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
    public function vincularAlunos(Request $request, Turma $turma): RedirectResponse
    {
        $request->validate([
            'alunos' => 'required|array|min:1',
            'alunos.*' => 'exists:alunos,id'
        ], [
            'alunos.required' => 'Selecione pelo menos um aluno.',
            'alunos.min' => 'Selecione pelo menos um aluno.',
            'alunos.*.exists' => 'Um ou mais alunos selecionados não existem.'
        ]);

        $alunosIds = $request->input('alunos');
        
        // Verificar se a turma tem capacidade suficiente
        $alunosAtualmenteMatriculados = $turma->alunos()->count();
        $novosAlunos = count($alunosIds);
        
        if (($alunosAtualmenteMatriculados + $novosAlunos) > $turma->capacidade_maxima) {
            return redirect()
                ->back()
                ->withErrors(['capacidade' => 'A turma não possui capacidade suficiente para matricular todos os alunos selecionados.']);
        }
        
        // Verificar se os alunos estão disponíveis (sem turma)
        $alunosIndisponiveis = Aluno::whereIn('id', $alunosIds)
            ->whereNotNull('turma_id')
            ->pluck('nome')
            ->toArray();
            
        if (!empty($alunosIndisponiveis)) {
            return redirect()
                ->back()
                ->withErrors(['alunos_indisponiveis' => 'Os seguintes alunos já estão matriculados em outras turmas: ' . implode(', ', $alunosIndisponiveis)]);
        }
        
        // Vincular os alunos à turma
        Aluno::whereIn('id', $alunosIds)->update(['turma_id' => $turma->id]);
        
        $quantidadeAlunos = count($alunosIds);
        $mensagem = $quantidadeAlunos === 1 
            ? '1 aluno foi matriculado com sucesso na turma!' 
            : "{$quantidadeAlunos} alunos foram matriculados com sucesso na turma!";

        return redirect()
            ->route('turmas.show', $turma)
            ->with('success', $mensagem);
    }

    /**
     * Desvincular aluno da turma.
     */
    public function desvincularAluno(Request $request, Turma $turma, Aluno $aluno): RedirectResponse
    {
        // Verificar se o aluno está realmente vinculado a esta turma
        if ($aluno->turma_id !== $turma->id) {
            return redirect()
                ->back()
                ->withErrors(['erro' => 'Este aluno não está matriculado nesta turma.']);
        }
        
        // Desvincular o aluno
        $aluno->update(['turma_id' => null]);
        
        return redirect()
            ->route('turmas.show', $turma)
            ->with('success', "Aluno {$aluno->nome} foi desvinculado da turma com sucesso!");
    }
}
