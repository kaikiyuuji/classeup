<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlunoStoreRequest;
use App\Http\Requests\AlunoUpdateRequest;
use App\Http\Requests\AvaliacaoUpdateRequest;
use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Turma;
use App\Services\AlunoService;
use App\Services\AvaliacaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlunoController extends Controller
{
    private AlunoService $alunoService;

    public function __construct(AlunoService $alunoService)
    {
        $this->alunoService = $alunoService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $alunos = $this->alunoService->obterAlunosPaginados($request);
        $turmas = $this->obterTurmasAtivas();
        $parametrosOrdenacao = $this->extrairParametrosOrdenacao($request);
        
        return view('admin.alunos.index', array_merge(
            compact('alunos', 'turmas'),
            $parametrosOrdenacao
        ));
    }
    
    /**
     * Obtém turmas ativas para filtros
     */
    private function obterTurmasAtivas()
    {
        return Turma::where('ativo', true)->orderBy('nome')->get();
    }
    
    /**
     * Extrai parâmetros de ordenação da requisição
     */
    private function extrairParametrosOrdenacao(Request $request): array
    {
        return [
            'sortField' => $request->get('sort', 'nome'),
            'sortDirection' => $request->get('direction', 'asc')
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $turmas = Turma::where('ativo', true)
            ->orderBy('nome')
            ->get();
            
        $turmaSelecionada = $request->get('turma_id');
            
        return view('admin.alunos.create', compact('turmas', 'turmaSelecionada'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AlunoStoreRequest $request): RedirectResponse
    {
        $dadosValidados = $request->validated();
        $dadosProcessados = $this->processarDadosAluno($dadosValidados, $request);
        
        Aluno::create($dadosProcessados);

        return $this->redirecionarComSucesso('admin.alunos.index', 'Aluno criado com sucesso!');
    }
    
    /**
     * Processa dados do aluno incluindo upload de foto
     */
    private function processarDadosAluno(array $dados, Request $request): array
    {
        if ($request->hasFile('foto_perfil')) {
            $dados['foto_perfil'] = $this->processarUploadFoto($request->file('foto_perfil'));
        }
        
        return $dados;
    }
    
    /**
     * Redireciona com mensagem de sucesso
     */
    private function redirecionarComSucesso(string $rota, string $mensagem): RedirectResponse
    {
        return redirect()->route($rota)->with('success', $mensagem);
    }

    /**
     * Display the specified resource.
     */
    public function show(Aluno $aluno): View
    {
        $dadosExibicao = $this->alunoService->prepararDadosParaExibicao($aluno);
        
        return view('admin.alunos.show', $dadosExibicao);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aluno $aluno): View
    {
        $turmas = Turma::where('ativo', true)
            ->orderBy('nome')
            ->get();
            
        return view('admin.alunos.edit', compact('aluno', 'turmas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AlunoUpdateRequest $request, Aluno $aluno): RedirectResponse
    {
        $dadosValidados = $request->validated();
        $dadosProcessados = $this->processarAtualizacaoAluno($dadosValidados, $request, $aluno);
        
        $aluno->update($dadosProcessados);

        return $this->redirecionarParaExibicao($aluno, 'Aluno atualizado com sucesso!');
    }
    
    /**
     * Processa dados para atualização incluindo gerenciamento de foto
     */
    private function processarAtualizacaoAluno(array $dados, Request $request, Aluno $aluno): array
    {
        if ($request->hasFile('foto_perfil')) {
            $this->removerFotoAnterior($aluno);
            $dados['foto_perfil'] = $this->processarUploadFoto($request->file('foto_perfil'));
        }
        
        return $dados;
    }
    
    /**
     * Remove foto anterior se existir
     */
    private function removerFotoAnterior(Aluno $aluno): void
    {
        if ($aluno->foto_perfil && \Storage::disk('public')->exists($aluno->foto_perfil)) {
            \Storage::disk('public')->delete($aluno->foto_perfil);
        }
    }
    
    /**
     * Redireciona para página de exibição do aluno
     */
    private function redirecionarParaExibicao(Aluno $aluno, string $mensagem): RedirectResponse
    {
        return redirect()->route('admin.alunos.show', $aluno)->with('success', $mensagem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aluno $aluno): RedirectResponse
    {
        $resultadoExclusao = $this->alunoService->excluirComSeguranca($aluno);
        
        return $this->processarResultadoExclusao($resultadoExclusao, $aluno);
    }
    
    /**
     * Processa resultado da exclusão e redireciona adequadamente
     */
    private function processarResultadoExclusao(array $resultado, Aluno $aluno): RedirectResponse
    {
        if ($resultado['sucesso']) {
            return $this->redirecionarComSucesso('admin.alunos.index', $resultado['mensagem']);
        }
        
        return redirect()
            ->route('admin.alunos.show', $aluno)
            ->with('error', $resultado['mensagem']);
    }





    /**
     * Processa upload de foto e retorna o caminho armazenado
     */
    private function processarUploadFoto($arquivo): string
    {
        $nomeArquivo = $this->gerarNomeUnicoArquivo($arquivo);
        
        return $arquivo->storeAs('alunos', $nomeArquivo, 'public');
    }
    
    /**
     * Gera nome único para arquivo
     */
    private function gerarNomeUnicoArquivo($arquivo): string
    {
        return time() . '_' . uniqid() . '.' . $arquivo->getClientOriginalExtension();
    }

    /**
     * Exibe o boletim de avaliações do aluno
     */
    public function boletim(Aluno $aluno, AvaliacaoService $avaliacaoService): View
    {
        $avaliacoes = $avaliacaoService->obterAvaliacoesDoAluno($aluno);
        
        return view('admin.alunos.notas.boletim', compact('aluno', 'avaliacoes'));
    }

    /**
     * Atualiza as notas de uma avaliação específica
     */
    public function atualizarAvaliacao(
        AvaliacaoUpdateRequest $request, 
        Aluno $aluno, 
        Avaliacao $avaliacao,
        AvaliacaoService $avaliacaoService
    ): RedirectResponse {
        $avaliacaoService->atualizarNotas($avaliacao, $request->validated());
        
        return redirect()
            ->route('admin.alunos.boletim', $aluno)
            ->with('success', 'Notas atualizadas com sucesso!');
    }
    

}
