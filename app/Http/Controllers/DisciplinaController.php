<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DisciplinaStoreRequest;
use App\Http\Requests\DisciplinaUpdateRequest;
use App\Models\Disciplina;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DisciplinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $disciplinas = Disciplina::orderBy('nome')->paginate(10);
        
        return view('admin.disciplinas.index', compact('disciplinas'));
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

        Disciplina::create($validatedData);

        return redirect()->route('disciplinas.index')
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
        
        // Processar o campo 'ativo' corretamente para radio buttons
        // Radio buttons sempre enviam um valor quando selecionados
        $validatedData['ativo'] = $request->input('ativo') === '1';

        $disciplina->update($validatedData);

        return redirect()->route('disciplinas.show', $disciplina)
            ->with('success', 'Disciplina atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disciplina $disciplina): RedirectResponse
    {
        $disciplina->delete();

        return redirect()->route('disciplinas.index')
            ->with('success', 'Disciplina excluída com sucesso!');
    }
}
