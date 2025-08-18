<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TurmaStoreRequest;
use App\Http\Requests\TurmaUpdateRequest;
use App\Models\Turma;
use App\Models\Aluno;
use App\Models\Professor;
use App\Services\TurmaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\VincularAlunosRequest;
use App\Http\Requests\DesvincularAlunoRequest;
use App\Http\Requests\VincularProfessorRequest;
use App\Http\Requests\DesvincularProfessorRequest;


class TurmaController extends Controller
{
    /**
     * Serviço responsável pela lógica de negócio das turmas
     */
    private TurmaService $turmaService;

    /**
     * Construtor com injeção de dependência
     */
    public function __construct(TurmaService $turmaService)
    {
        $this->turmaService = $turmaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Turma::withCount('alunos');

        // Filtro de busca por nome
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nome', 'like', "%{$search}%");
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

        // Filtro por nível educacional
        if ($request->filled('nivel_educacional')) {
            $query->where('serie', $request->get('nivel_educacional'));
        }

        // Filtro por turno
        if ($request->filled('turno')) {
            $query->where('turno', $request->get('turno'));
        }

        // Lógica de ordenação
        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        
        // Validar campos de ordenação permitidos
        $allowedSortFields = ['nome', 'serie', 'turno', 'ativo'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'nome';
        }
        
        // Validar direção de ordenação
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $turmas = $query->orderBy($sortField, $sortDirection)->paginate(15)->withQueryString();

        return view('admin.turmas.index', compact('turmas', 'sortField', 'sortDirection'));
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
            ->route('admin.turmas.index')
            ->with('success', 'Turma criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Turma $turma): View
    {
        $dadosParaView = $this->turmaService->prepararDadosParaExibicao($turma);
        
        return view('admin.turmas.show', $dadosParaView);
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
        
        $turma->update($validatedData);

        return redirect()
            ->route('admin.turmas.show', $turma)
            ->with('success', 'Turma atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Turma $turma): RedirectResponse
    {
        if (!$this->turmaService->podeSerExcluida($turma)) {
            $informacoes = $this->turmaService->obterInformacoesRelacionamentos($turma);
            
            return redirect()
                ->route('admin.turmas.show', $turma)
                ->with('error', 
                    "Não é possível excluir a turma '{$turma->nome}'. " .
                    "Ela possui {$informacoes['total_alunos']} aluno(s) e {$informacoes['total_professores']} professor(es) vinculado(s). " .
                    "Remova todos os vínculos antes de excluir a turma."
                );
        }

        $turma->delete();

        return redirect()
            ->route('admin.turmas.index')
            ->with('success', 'Turma excluída com sucesso!');
    }

    /**
     * Vincular múltiplos alunos à turma.
     */
    public function vincularAlunos(VincularAlunosRequest $request, Turma $turma): RedirectResponse
    {
        $alunosParaVincular = $this->obterAlunosParaVincular($request, $turma);

        if ($this->temAlunosParaVincular($alunosParaVincular)) {
            return $this->executarVinculacaoAlunos($alunosParaVincular, $turma);
        }

        return $this->retornarSemVinculacao($turma);
    }

    /**
     * Obtém lista de alunos que podem ser vinculados à turma
     */
    private function obterAlunosParaVincular(VincularAlunosRequest $request, Turma $turma): array
    {
        $alunosJaVinculados = $turma->alunos()->pluck('id')->toArray();
        return array_diff($request->validated()['alunos'], $alunosJaVinculados);
    }

    /**
     * Verifica se existem alunos para vincular
     */
    private function temAlunosParaVincular(array $alunosParaVincular): bool
    {
        return !empty($alunosParaVincular);
    }

    /**
     * Executa a vinculação dos alunos à turma
     */
    private function executarVinculacaoAlunos(array $alunosParaVincular, Turma $turma): RedirectResponse
    {
        Aluno::whereIn('id', $alunosParaVincular)->update(['turma_id' => $turma->id]);
        $quantidadeVinculada = count($alunosParaVincular);
        
        return redirect()->route('admin.turmas.show', $turma)
            ->with('success', "{$quantidadeVinculada} aluno(s) vinculado(s) com sucesso!");
    }

    /**
     * Retorna resposta quando não há alunos para vincular
     */
    private function retornarSemVinculacao(Turma $turma): RedirectResponse
    {
        return redirect()->route('admin.turmas.show', $turma)
            ->with('info', 'Todos os alunos selecionados já estão vinculados à turma.');
    }

    /**
     * Desvincular aluno da turma.
     */
    public function desvincularAluno(DesvincularAlunoRequest $request, Turma $turma, Aluno $aluno): RedirectResponse
    {
        if (!$this->turmaService->desvincularAlunoComSeguranca($aluno, $turma)) {
            return redirect()
                ->route('admin.turmas.show', $turma)
                ->with('error', "O aluno {$aluno->nome} não pertence a esta turma.");
        }
        
        return redirect()
            ->route('admin.turmas.show', $turma)
            ->with('success', "Aluno {$aluno->nome} desvinculado com sucesso!");
    }

    /**
     * Vincular professor e disciplina à turma.
     */
    public function vincularProfessor(VincularProfessorRequest $request, Turma $turma): RedirectResponse
    {
        $dadosVinculacao = $this->extrairDadosVinculacaoProfessor($request);
        $this->executarVinculacaoProfessor($turma, $dadosVinculacao);
        
        return $this->retornarSucessoVinculacaoProfessor($turma);
    }

    /**
     * Desvincular professor e disciplina da turma.
     */
    public function desvincularProfessor(DesvincularProfessorRequest $request, Turma $turma): RedirectResponse
    {
        $dadosDesvinculacao = $this->extrairDadosDesvinculacaoProfessor($request);
        $this->executarDesvinculacaoProfessor($turma, $dadosDesvinculacao);
        
        return $this->retornarSucessoDesvinculacaoProfessor($turma);
    }

    /**
     * Extrai dados de vinculação do professor do request
     */
    private function extrairDadosVinculacaoProfessor(VincularProfessorRequest $request): array
    {
        return [
            'professor_id' => $request->validated()['professor_id'],
            'disciplina_id' => $request->validated()['disciplina_id']
        ];
    }

    /**
     * Extrai dados de desvinculação do professor do request
     */
    private function extrairDadosDesvinculacaoProfessor(DesvincularProfessorRequest $request): array
    {
        return [
            'professor_id' => $request->validated()['professor_id'],
            'disciplina_id' => $request->validated()['disciplina_id']
        ];
    }

    /**
     * Executa a vinculação do professor à turma
     */
    private function executarVinculacaoProfessor(Turma $turma, array $dados): void
    {
        $turma->professores()->attach($dados['professor_id'], [
            'disciplina_id' => $dados['disciplina_id']
        ]);
    }

    /**
     * Executa a desvinculação do professor da turma
     */
    private function executarDesvinculacaoProfessor(Turma $turma, array $dados): void
    {
        $turma->professores()
            ->wherePivot('disciplina_id', $dados['disciplina_id'])
            ->detach($dados['professor_id']);
    }

    /**
     * Retorna resposta de sucesso para vinculação de professor
     */
    private function retornarSucessoVinculacaoProfessor(Turma $turma): RedirectResponse
    {
        return redirect()
            ->route('admin.turmas.show', $turma)
            ->with('success', 'Professor vinculado com sucesso!');
    }

    /**
     * Retorna resposta de sucesso para desvinculação de professor
     */
    private function retornarSucessoDesvinculacaoProfessor(Turma $turma): RedirectResponse
    {
        return redirect()
            ->route('admin.turmas.show', $turma)
            ->with('success', 'Professor desvinculado com sucesso!');
    }
}
