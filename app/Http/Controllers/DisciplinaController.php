<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DisciplinaStoreRequest;
use App\Http\Requests\DisciplinaUpdateRequest;
use App\Models\Disciplina;
use App\Services\DisciplinaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisciplinaController extends Controller
{
    public function __construct(
        private readonly DisciplinaService $disciplinaService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $disciplinas = $this->disciplinaService->obterDisciplinasComFiltros($request);
        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        
        return view('admin.disciplinas.index', compact('disciplinas', 'sortField', 'sortDirection'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.disciplinas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DisciplinaStoreRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $this->disciplinaService->criarDisciplina($validatedData);

        return redirect()->route('admin.disciplinas.index')
            ->with('success', 'Disciplina criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Disciplina $disciplina): View
    {
        return view('admin.disciplinas.show', compact('disciplina'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Disciplina $disciplina): View
    {
        return view('admin.disciplinas.edit', compact('disciplina'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DisciplinaUpdateRequest $request, Disciplina $disciplina): RedirectResponse
    {
        $validatedData = $request->validated();
        $this->disciplinaService->atualizarDisciplina($disciplina, $validatedData);

        return redirect()->route('admin.disciplinas.show', $disciplina)
            ->with('success', 'Disciplina atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disciplina $disciplina): RedirectResponse
    {
        $resultado = $this->disciplinaService->excluirDisciplina($disciplina);
        
        $tipoMensagem = $resultado['sucesso'] ? 'success' : 'error';
        
        return redirect()->route('admin.disciplinas.index')
            ->with($tipoMensagem, $resultado['mensagem']);
    }
}
