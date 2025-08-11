<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DisciplinaStoreRequest;
use App\Http\Requests\DisciplinaUpdateRequest;
use App\Models\Disciplina;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisciplinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Disciplina::query();

        // Filtro de busca por nome, código ou descrição
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
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

        // Lógica de ordenação
        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        
        // Validar campos de ordenação permitidos
        $allowedSortFields = ['nome', 'descricao', 'carga_horaria', 'ativo'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'nome';
        }
        
        // Validar direção de ordenação
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $disciplinas = $query->orderBy($sortField, $sortDirection)->paginate(10)->withQueryString();
        
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

        Disciplina::create($validatedData);

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
        
        // Processar o campo 'ativo' corretamente para radio buttons
        // Radio buttons sempre enviam um valor quando selecionados
        $validatedData['ativo'] = $request->input('ativo') === '1';

        $disciplina->update($validatedData);

        return redirect()->route('admin.disciplinas.show', $disciplina)
            ->with('success', 'Disciplina atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disciplina $disciplina): RedirectResponse
    {
        $disciplina->delete();

        return redirect()->route('admin.disciplinas.index')
            ->with('success', 'Disciplina excluída com sucesso!');
    }
}
