<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->validateProfessorData($request);
        
        // Handle checkbox field 'ativo' - if not checked, it won't be in request
        $validatedData['ativo'] = $request->has('ativo');
        
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
    public function show(Professor $professore): View
    {
        return view('admin.professores.show', compact('professore'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Professor $professore): View
    {
        return view('admin.professores.edit', compact('professore'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Professor $professore): RedirectResponse
    {
        $validatedData = $this->validateProfessorData($request, $professore->id);
        
        // Handle checkbox field 'ativo' - if not checked, it won't be in request
        $validatedData['ativo'] = $request->has('ativo');
        
        // Handle photo upload
        if ($request->hasFile('foto_perfil')) {
            // Delete old photo if exists
            if ($professore->foto_perfil && \Storage::disk('public')->exists($professore->foto_perfil)) {
                \Storage::disk('public')->delete($professore->foto_perfil);
            }
            $validatedData['foto_perfil'] = $this->handlePhotoUpload($request->file('foto_perfil'));
        }
        
        $professore->update($validatedData);

        return redirect()
            ->route('professores.show', $professore)
            ->with('success', 'Professor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Professor $professore): RedirectResponse
    {
        $professore->delete();

        return redirect()
            ->route('professores.index')
            ->with('success', 'Professor excluído com sucesso!');
    }

    /**
     * Validate professor data with complete validation rules.
     */
    private function validateProfessorData(Request $request, ?int $professorId = null): array
    {
        $emailRule = $professorId 
            ? 'required|email|unique:professores,email,' . $professorId
            : 'required|email|unique:professores,email';
            
        $cpfRule = $professorId 
            ? 'required|string|size:11|unique:professores,cpf,' . $professorId
            : 'required|string|size:11|unique:professores,cpf';

        return $request->validate([
            'nome' => 'required|string|max:255',
            'email' => $emailRule,
            'cpf' => $cpfRule,
            'data_nascimento' => 'required|date|before:today',
            'telefone' => 'nullable|string|max:15',
            'endereco' => 'nullable|string|max:500',
            'especialidade' => 'required|string|max:255',
            'formacao' => 'required|string|max:1000',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ativo' => 'boolean',
        ]);
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
