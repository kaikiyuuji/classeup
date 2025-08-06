<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessorStoreRequest;
use App\Http\Requests\ProfessorUpdateRequest;
use App\Models\Disciplina;
use App\Models\Professor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfessorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $professores = Professor::orderBy('nome')
            ->paginate(15);

        return view('admin.professores.index', compact('professores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.professores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfessorStoreRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        
        // Handle photo upload
        if ($request->hasFile('foto_perfil')) {
            $validatedData['foto_perfil'] = $this->handlePhotoUpload($request->file('foto_perfil'));
        }
        
        Professor::create($validatedData);

        return redirect()
            ->route('professores.index')
            ->with('success', 'Professor criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Professor $professor): View
    {
        $disciplinasVinculadas = $professor->disciplinas;
        $disciplinasDisponiveis = Disciplina::whereNotIn('id', $disciplinasVinculadas->pluck('id'))
                                          ->orderBy('nome')
                                          ->get();
        
        return view('admin.professores.show', compact('professor', 'disciplinasVinculadas', 'disciplinasDisponiveis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Professor $professor): View
    {
        return view('admin.professores.edit', compact('professor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfessorUpdateRequest $request, Professor $professor): RedirectResponse
    {
        $validatedData = $request->validated();
        
        // Handle photo upload
        if ($request->hasFile('foto_perfil')) {
            // Delete old photo if exists
            if ($professor->foto_perfil && \Storage::disk('public')->exists($professor->foto_perfil)) {
                \Storage::disk('public')->delete($professor->foto_perfil);
            }
            $validatedData['foto_perfil'] = $this->handlePhotoUpload($request->file('foto_perfil'));
        }
        
        $professor->update($validatedData);

        return redirect()
            ->route('professores.show', $professor)
            ->with('success', 'Professor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Professor $professor): RedirectResponse
    {
        $professor->delete();

        return redirect()
            ->route('professores.index')
            ->with('success', 'Professor excluído com sucesso!');
    }



    /**
     * Vincular professor a uma disciplina
     */
    public function vincularDisciplina(Request $request, Professor $professor): RedirectResponse
    {
        $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id'
        ]);
        
        $disciplinaId = $request->disciplina_id;
        
        // Verificar se já está vinculado
        if ($professor->disciplinas()->where('disciplina_id', $disciplinaId)->exists()) {
            return redirect()
                ->route('professores.show', $professor)
                ->with('error', 'Professor já está vinculado a esta disciplina!');
        }
        
        $professor->disciplinas()->attach($disciplinaId);
        
        $disciplina = Disciplina::find($disciplinaId);
        
        return redirect()
            ->route('professores.show', $professor)
            ->with('success', "Professor vinculado à disciplina {$disciplina->nome} com sucesso!");
    }
    
    /**
     * Desvincular professor de uma disciplina
     */
    public function desvincularDisciplina(Request $request, Professor $professor): RedirectResponse
    {
        $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id'
        ]);
        
        $disciplinaId = $request->disciplina_id;
        
        // Verificar se está vinculado
        if (!$professor->disciplinas()->where('disciplina_id', $disciplinaId)->exists()) {
            return redirect()
                ->route('professores.show', $professor)
                ->with('error', 'Professor não está vinculado a esta disciplina!');
        }
        
        $professor->disciplinas()->detach($disciplinaId);
        
        $disciplina = Disciplina::find($disciplinaId);
        
        return redirect()
            ->route('professores.show', $professor)
            ->with('success', "Professor desvinculado da disciplina {$disciplina->nome} com sucesso!");
    }

    /**
     * Handle photo upload and return the stored path.
     */
    private function handlePhotoUpload($file): string
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Store in public disk under professores folder
        $path = $file->storeAs('professores', $filename, 'public');
        
        return $path;
    }
}
