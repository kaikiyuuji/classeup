<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfessorStoreRequest;
use App\Http\Requests\ProfessorUpdateRequest;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Services\PhotoUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfessorController extends Controller
{
    public function __construct(
        private readonly PhotoUploadService $photoUpload,
    ) {}

    public function index(): View
    {
        $professores = Professor::orderBy('nome')->paginate(15);

        return view('admin.professores.index', compact('professores'));
    }

    public function create(): View
    {
        return view('admin.professores.create');
    }

    public function store(ProfessorStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto_perfil')) {
            $data['foto_perfil'] = $this->photoUpload->store($request->file('foto_perfil'), 'professores');
        }

        Professor::create($data);

        return redirect()->route('professores.index')->with('success', 'Professor criado com sucesso!');
    }

    public function show(Professor $professor): View
    {
        $disciplinasVinculadas = $professor->disciplinas;
        $disciplinasDisponiveis = Disciplina::whereNotIn('id', $disciplinasVinculadas->pluck('id'))
            ->orderBy('nome')
            ->get();

        return view('admin.professores.show', compact('professor', 'disciplinasVinculadas', 'disciplinasDisponiveis'));
    }

    public function edit(Professor $professor): View
    {
        return view('admin.professores.edit', compact('professor'));
    }

    public function update(ProfessorUpdateRequest $request, Professor $professor): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto_perfil')) {
            $data['foto_perfil'] = $this->photoUpload->store(
                $request->file('foto_perfil'),
                'professores',
                $professor->foto_perfil,
            );
        }

        $professor->update($data);

        return redirect()->route('professores.show', $professor)->with('success', 'Professor atualizado com sucesso!');
    }

    public function destroy(Professor $professor): RedirectResponse
    {
        $professor->delete();

        return redirect()->route('professores.index')->with('success', 'Professor excluído com sucesso!');
    }

    public function vincularDisciplina(Request $request, Professor $professor): RedirectResponse
    {
        $request->validate(['disciplina_id' => 'required|exists:disciplinas,id']);

        $disciplinaId = (int) $request->disciplina_id;

        if ($professor->disciplinas()->where('disciplina_id', $disciplinaId)->exists()) {
            return redirect()->route('professores.show', $professor)
                ->with('error', 'Professor já está vinculado a esta disciplina!');
        }

        $professor->disciplinas()->attach($disciplinaId);
        $disciplina = Disciplina::findOrFail($disciplinaId);

        return redirect()->route('professores.show', $professor)
            ->with('success', "Professor vinculado à disciplina {$disciplina->nome} com sucesso!");
    }

    public function desvincularDisciplina(Request $request, Professor $professor): RedirectResponse
    {
        $request->validate(['disciplina_id' => 'required|exists:disciplinas,id']);

        $disciplinaId = (int) $request->disciplina_id;

        if (! $professor->disciplinas()->where('disciplina_id', $disciplinaId)->exists()) {
            return redirect()->route('professores.show', $professor)
                ->with('error', 'Professor não está vinculado a esta disciplina!');
        }

        $professor->disciplinas()->detach($disciplinaId);
        $disciplina = Disciplina::findOrFail($disciplinaId);

        return redirect()->route('professores.show', $professor)
            ->with('success', "Professor desvinculado da disciplina {$disciplina->nome} com sucesso!");
    }
}
