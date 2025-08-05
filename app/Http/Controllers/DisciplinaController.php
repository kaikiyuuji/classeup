<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Disciplina;
use Illuminate\Http\Request;
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
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|max:20|unique:disciplinas,codigo',
            'descricao' => 'nullable|string|max:1000',
            'carga_horaria' => 'required|integer|min:1|max:999',
        ]);

        // Processar campo ativo (checkbox)
        $validatedData['ativo'] = $request->has('ativo');

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
    public function update(Request $request, Disciplina $disciplina): RedirectResponse
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|max:20|unique:disciplinas,codigo,' . $disciplina->id,
            'descricao' => 'nullable|string|max:1000',
            'carga_horaria' => 'required|integer|min:1|max:999',
        ]);

        // Processar campo ativo (checkbox)
        $validatedData['ativo'] = $request->has('ativo');

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
