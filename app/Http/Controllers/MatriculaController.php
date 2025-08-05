<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Matricula;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MatriculaController extends Controller
{
    public function index()
    {
        $matriculas = Matricula::with(['aluno', 'turma'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.matriculas.index', compact('matriculas'));
    }

    public function create()
    {
        $alunos = Aluno::orderBy('nome')->get();
        $turmas = Turma::orderBy('nome')->get();

        return view('admin.matriculas.create', compact('alunos', 'turmas'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateMatriculaData($request);

        $this->checkDuplicateMatricula($validated['aluno_id'], $validated['turma_id']);
        $this->checkTurmaCapacity($validated['turma_id']);

        Matricula::create($validated);

        return redirect()->route('matriculas.index')
            ->with('success', 'Matrícula realizada com sucesso!');
    }

    public function show(Matricula $matricula)
    {
        $matricula->load(['aluno', 'turma']);
        return view('admin.matriculas.show', compact('matricula'));
    }

    public function edit(Matricula $matricula)
    {
        $alunos = Aluno::orderBy('nome')->get();
        $turmas = Turma::orderBy('nome')->get();

        return view('admin.matriculas.edit', compact('matricula', 'alunos', 'turmas'));
    }

    public function update(Request $request, Matricula $matricula)
    {
        $validated = $this->validateMatriculaData($request, $matricula->id);

        if ($this->isChangingAlunoOrTurma($matricula, $validated)) {
            $this->checkDuplicateMatricula($validated['aluno_id'], $validated['turma_id']);
            $this->checkTurmaCapacity($validated['turma_id']);
        }

        $matricula->update($validated);

        return redirect()->route('matriculas.index')
            ->with('success', 'Matrícula atualizada com sucesso!');
    }

    public function destroy(Matricula $matricula)
    {
        $matricula->delete();

        return redirect()->route('matriculas.index')
            ->with('success', 'Matrícula removida com sucesso!');
    }

    public function porTurma(Turma $turma)
    {
        $matriculas = $turma->matriculas()
            ->with('aluno')
            ->orderBy('data_matricula', 'desc')
            ->paginate(15);

        return view('admin.matriculas.por-turma', compact('turma', 'matriculas'));
    }

    public function porAluno(Aluno $aluno)
    {
        $matriculas = $aluno->matriculas()
            ->with('turma')
            ->orderBy('data_matricula', 'desc')
            ->paginate(15);

        return view('admin.matriculas.por-aluno', compact('aluno', 'matriculas'));
    }

    private function validateMatriculaData(Request $request, $matriculaId = null)
    {
        return $request->validate([
            'aluno_id' => ['required', 'exists:alunos,id'],
            'turma_id' => ['required', 'exists:turmas,id'],
            'data_matricula' => ['required', 'date'],
            'status' => ['required', Rule::in(['ativa', 'inativa', 'transferida', 'cancelada'])],
        ]);
    }

    private function checkDuplicateMatricula($alunoId, $turmaId)
    {
        $exists = Matricula::where('aluno_id', $alunoId)
            ->where('turma_id', $turmaId)
            ->exists();

        if ($exists) {
            abort(422, 'Este aluno já está matriculado nesta turma.');
        }
    }

    private function checkTurmaCapacity($turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        $matriculasAtivas = $turma->matriculas()
            ->where('status', 'ativa')
            ->count();

        if ($matriculasAtivas >= $turma->capacidade_maxima) {
            abort(422, 'A turma já atingiu sua capacidade máxima.');
        }
    }

    private function isChangingAlunoOrTurma(Matricula $matricula, array $validated)
    {
        return $matricula->aluno_id !== $validated['aluno_id'] ||
               $matricula->turma_id !== $validated['turma_id'];
    }
}
