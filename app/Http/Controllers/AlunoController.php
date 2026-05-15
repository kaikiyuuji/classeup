<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\StatusMatricula;
use App\Http\Requests\AlunoStoreRequest;
use App\Http\Requests\AlunoUpdateRequest;
use App\Http\Requests\AvaliacaoUpdateRequest;
use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Turma;
use App\Services\AvaliacaoService;
use App\Services\MatriculaService;
use App\Services\PhotoUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlunoController extends Controller
{
    public function __construct(
        private readonly PhotoUploadService $photoUpload,
        private readonly MatriculaService $matriculas,
    ) {}

    public function index(): View
    {
        $alunos = Aluno::with('turma')->orderBy('nome')->paginate(15);

        return view('admin.alunos.index', compact('alunos'));
    }

    public function create(Request $request): View
    {
        $turmas = Turma::where('ativo', true)->orderBy('nome')->get();
        $turmaSelecionada = $request->get('turma_id');

        return view('admin.alunos.create', compact('turmas', 'turmaSelecionada'));
    }

    public function store(AlunoStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto_perfil')) {
            $data['foto_perfil'] = $this->photoUpload->store($request->file('foto_perfil'), 'alunos');
        }

        $data['numero_matricula'] = $this->matriculas->gerar();
        $data['data_matricula'] = now()->format('Y-m-d');
        $data['status_matricula'] = StatusMatricula::Ativa;

        Aluno::create($data);

        return redirect()->route('alunos.index')->with('success', 'Aluno criado com sucesso!');
    }

    public function show(Aluno $aluno): View
    {
        $aluno->load('turma');

        return view('admin.alunos.show', compact('aluno'));
    }

    public function edit(Aluno $aluno): View
    {
        $turmas = Turma::where('ativo', true)->orderBy('nome')->get();

        return view('admin.alunos.edit', compact('aluno', 'turmas'));
    }

    public function update(AlunoUpdateRequest $request, Aluno $aluno): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto_perfil')) {
            $data['foto_perfil'] = $this->photoUpload->store(
                $request->file('foto_perfil'),
                'alunos',
                $aluno->foto_perfil,
            );
        }

        $aluno->update($data);

        return redirect()->route('alunos.show', $aluno)->with('success', 'Aluno atualizado com sucesso!');
    }

    public function destroy(Aluno $aluno): RedirectResponse
    {
        $aluno->delete();

        return redirect()->route('alunos.index')->with('success', 'Aluno excluído com sucesso!');
    }

    public function boletim(Aluno $aluno, AvaliacaoService $avaliacaoService): View
    {
        $avaliacoes = $avaliacaoService->obterAvaliacoesDoAluno($aluno);

        return view('admin.alunos.notas.boletim', compact('aluno', 'avaliacoes'));
    }

    public function atualizarAvaliacao(
        AvaliacaoUpdateRequest $request,
        Aluno $aluno,
        Avaliacao $avaliacao,
        AvaliacaoService $avaliacaoService,
    ): RedirectResponse {
        $avaliacaoService->atualizarNotas($avaliacao, $request->validated());

        return redirect()->route('alunos.boletim', $aluno)->with('success', 'Notas atualizadas com sucesso!');
    }
}
