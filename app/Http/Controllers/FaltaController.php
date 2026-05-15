<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\FaltaStoreRequest;
use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Falta;
use App\Models\Professor;
use App\Models\Turma;
use App\Services\ChamadaService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FaltaController extends Controller
{
    public function __construct(
        private readonly ChamadaService $chamada,
    ) {}

    public function index(): View
    {
        $turmasComVinculo = $this->chamada->obterTurmasComVinculo();

        return view('admin.faltas.index', compact('turmasComVinculo'));
    }

    public function chamada(Request $request, int $turma, int $disciplina): View|RedirectResponse
    {
        $professorId = $request->integer('professor_id');
        $data = $request->get('data', now()->format('Y-m-d'));

        $turma = Turma::findOrFail($turma);
        $disciplina = Disciplina::findOrFail($disciplina);

        if ($professorId === 0) {
            $vinculo = DB::table('professor_disciplina_turma')
                ->where('turma_id', $turma->id)
                ->where('disciplina_id', $disciplina->id)
                ->first();

            if ($vinculo === null) {
                return redirect()->route('faltas.index')
                    ->with('error', 'Nenhum professor vinculado a esta turma/disciplina.');
            }

            $professorId = (int) $vinculo->professor_id;
        }

        $professor = Professor::findOrFail($professorId);
        $alunos = $this->chamada->alunosDaTurma($turma->id);
        $faltasExistentes = $this->chamada->matriculasAusentes($disciplina->id, $professorId, $data);

        return view('admin.faltas.chamada', compact(
            'turma', 'disciplina', 'professor', 'alunos', 'faltasExistentes', 'data'
        ));
    }

    public function store(FaltaStoreRequest $request): RedirectResponse
    {
        $dto = $request->toDto();

        if (! $dto->confirmarReenvio
            && $this->chamada->jaExisteChamada($dto->disciplinaId, $dto->professorId, $dto->dataFalta->toDateString())
        ) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Já existe uma chamada cadastrada para este dia. Deseja confirmar o reenvio?')
                ->with('mostrar_confirmacao', true);
        }

        $this->chamada->registrar($dto);

        return redirect()->route('faltas.index')->with('success', 'Chamada registrada com sucesso!');
    }

    public function relatorioAluno(Request $request): View
    {
        $matricula = $request->get('matricula');
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));

        $aluno = null;
        $faltas = collect();

        if ($matricula !== null) {
            $aluno = Aluno::where('numero_matricula', $matricula)->first();
            if ($aluno !== null) {
                $faltas = $this->chamada->relatorioPorMatricula(
                    (string) $matricula,
                    CarbonImmutable::parse((string) $dataInicio),
                    CarbonImmutable::parse((string) $dataFim),
                );
            }
        }

        return view('admin.faltas.relatorio-aluno', compact('aluno', 'faltas', 'matricula', 'dataInicio', 'dataFim'));
    }

    public function justificar(Falta $falta): View
    {
        $falta->load(['aluno', 'disciplina', 'professor']);

        return view('admin.faltas.justificar', compact('falta'));
    }

    public function processarJustificativa(Request $request, Falta $falta): RedirectResponse
    {
        $request->validate(['observacoes' => 'required|string|max:1000']);

        $falta->justificar((string) $request->input('observacoes'));

        return redirect()->route('faltas.relatorio-aluno', ['matricula' => $falta->matricula])
            ->with('success', 'Falta justificada com sucesso!');
    }

    public function removerJustificativa(Falta $falta): RedirectResponse
    {
        $falta->removerJustificativa();

        return redirect()->back()->with('success', 'Justificativa removida com sucesso!');
    }
}
