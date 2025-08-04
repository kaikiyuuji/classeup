<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AlunoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $alunos = Aluno::orderBy('nome')
            ->paginate(15);

        return view('admin.alunos.index', compact('alunos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.alunos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->validateAlunoData($request);
        
        // Handle checkbox field 'ativo' - if not checked, it won't be in request
        $validatedData['ativo'] = $request->has('ativo');
        
        // Handle photo upload
        if ($request->hasFile('foto_perfil')) {
            $validatedData['foto_perfil'] = $this->handlePhotoUpload($request->file('foto_perfil'));
        }
        
        Aluno::create($validatedData);

        return redirect()
            ->route('alunos.index')
            ->with('success', 'Aluno criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Aluno $aluno): View
    {
        return view('admin.alunos.show', compact('aluno'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aluno $aluno): View
    {
        return view('admin.alunos.edit', compact('aluno'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aluno $aluno): RedirectResponse
    {
        $validatedData = $this->validateAlunoData($request, $aluno->id);
        
        // Handle checkbox field 'ativo' - if not checked, it won't be in request
        $validatedData['ativo'] = $request->has('ativo');
        
        // Handle photo upload
        if ($request->hasFile('foto_perfil')) {
            // Delete old photo if exists
            if ($aluno->foto_perfil && \Storage::disk('public')->exists($aluno->foto_perfil)) {
                \Storage::disk('public')->delete($aluno->foto_perfil);
            }
            $validatedData['foto_perfil'] = $this->handlePhotoUpload($request->file('foto_perfil'));
        }
        
        $aluno->update($validatedData);

        return redirect()
            ->route('alunos.show', $aluno)
            ->with('success', 'Aluno atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aluno $aluno): RedirectResponse
    {
        $aluno->delete();

        return redirect()
            ->route('alunos.index')
            ->with('success', 'Aluno excluído com sucesso!');
    }

    /**
     * Validate aluno data with complete validation rules.
     */
    private function validateAlunoData(Request $request, ?int $alunoId = null): array
    {
        $emailRule = $alunoId 
            ? 'required|email|unique:alunos,email,' . $alunoId
            : 'required|email|unique:alunos,email';
            
        $cpfRule = $alunoId 
            ? 'required|string|size:11|unique:alunos,cpf,' . $alunoId
            : 'required|string|size:11|unique:alunos,cpf';

        return $request->validate([
            'nome' => 'required|string|max:255',
            'email' => $emailRule,
            'cpf' => $cpfRule,
            'data_nascimento' => 'required|date|before:today',
            'telefone' => 'nullable|string|max:15',
            'endereco' => 'nullable|string|max:500',
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
        
        // Store in public disk under alunos folder
        $path = $file->storeAs('alunos', $filename, 'public');
        
        return $path;
    }
}
