<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TurmaStoreRequest;
use App\Http\Requests\TurmaUpdateRequest;
use App\Models\Turma;
use Illuminate\Http\RedirectResponse;
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
        return view('admin.turmas.show', compact('turma'));
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
}
