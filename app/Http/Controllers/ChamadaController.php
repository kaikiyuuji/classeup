<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chamada;
use App\Models\Turma;
use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChamadaController extends Controller
{
    /**
     * Exibe a lista de turmas para chamada (Admin)
     */
    public function index()
    {
        $turmasComVinculo = $this->obterTurmasComVinculo();
        
        return view('admin.chamadas.index', compact('turmasComVinculo'));
    }

    /**
     * Exibe a lista de turmas para chamada (Professor)
     */
    public function indexProfessor()
    {
        $professor = auth()->user()->professor;
        
        if (!$professor) {
            abort(403, 'Professor não encontrado');
        }
        
        // Buscar turmas vinculadas ao professor com suas disciplinas específicas
        $turmasComVinculo = DB::table('professor_disciplina_turma')
            ->join('turmas', 'professor_disciplina_turma.turma_id', '=', 'turmas.id')
            ->join('disciplinas', 'professor_disciplina_turma.disciplina_id', '=', 'disciplinas.id')
            ->where('professor_disciplina_turma.professor_id', $professor->id)
            ->select(
                'turmas.id as turma_id',
                'turmas.nome as turma_nome',
                'turmas.serie',
                'turmas.turno',
                'disciplinas.id as disciplina_id',
                'disciplinas.nome as disciplina_nome',
                'disciplinas.codigo as disciplina_codigo',
                'professor_disciplina_turma.professor_id'
            )
            ->get()
            ->groupBy('turma_id')
            ->map(function ($items) {
                $firstItem = $items->first();
                return (object) [
                    'turma_id' => $firstItem->turma_id,
                    'turma_nome' => $firstItem->turma_nome,
                    'serie' => $firstItem->serie,
                    'turno' => $firstItem->turno,
                    'professor_id' => $firstItem->professor_id,
                    'disciplinas' => $items->map(function ($item) {
                        return (object) [
                            'disciplina_id' => $item->disciplina_id,
                            'disciplina_nome' => $item->disciplina_nome,
                            'disciplina_codigo' => $item->disciplina_codigo
                        ];
                    })
                ];
            });
        
        return view('professor.chamadas.index', compact('turmasComVinculo'));
    }

    /**
     * Exibe a interface de chamada para uma turma/disciplina específica
     */
    public function chamada(Request $request, $turma, $disciplina)
    {
        $professorId = $request->get('professor_id');
        $data = $request->get('data', now()->format('Y-m-d'));
        
        $turma = Turma::findOrFail($turma);
        $disciplina = Disciplina::findOrFail($disciplina);
        
        if (!$professorId) {
            $professorId = $this->obterProfessorVinculado($turma->id, $disciplina->id);
            
            if (!$professorId) {
                return redirect()->route('admin.chamadas.index')
                    ->with('error', 'Nenhum professor vinculado a esta turma/disciplina.');
            }
        }
        
        $professor = Professor::findOrFail($professorId);
        $alunos = $this->obterAlunosDaTurma($turma->id);
        $presencasExistentes = $this->obterChamadasExistentes($disciplina->id, $professorId, $data, $turma->id);
        
        return view('admin.chamadas.chamada', compact(
            'turma', 'disciplina', 'professor', 'alunos', 'presencasExistentes', 'data'
        ));
    }

    /**
     * Registra as chamadas do dia
     */
    public function store(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'professor_id' => 'required|exists:professores,id',
            'data_chamada' => 'required|date',
            'presencas' => 'array',
            'presencas.*' => 'string',
            'confirmar_reenvio' => 'sometimes|boolean'
        ]);
        
        if ($this->chamadaJaExiste($request) && !$request->has('confirmar_reenvio')) {
            return $this->solicitarConfirmacaoReenvio();
        }
        
        $this->processarChamada($request);
        
        return redirect()->route('admin.chamadas.index')
            ->with('success', 'Chamada registrada com sucesso!');
    }

    /**
     * Exibe relatório de chamadas por aluno
     */
    public function relatorioAluno(Request $request)
    {
        $matricula = $request->get('matricula');
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        
        $aluno = null;
        $chamadas = collect();
        
        if ($matricula) {
            $aluno = Aluno::where('numero_matricula', $matricula)->first();
            if ($aluno) {
                $chamadas = $this->obterChamadasDoAluno($matricula, $dataInicio, $dataFim);
            }
        }
        
        return view('admin.chamadas.relatorio-aluno', compact('aluno', 'chamadas', 'matricula', 'dataInicio', 'dataFim'));
    }

    /**
     * Exibe formulário para justificar falta
     */
    public function justificar($id)
    {
        $chamada = Chamada::with(['aluno', 'disciplina', 'professor'])->findOrFail($id);
        
        return view('admin.chamadas.justificar', compact('chamada'));
    }

    /**
     * Processa justificativa de falta
     */
    public function processarJustificativa(Request $request, $id)
    {
        $request->validate([
            'observacoes' => 'required|string|max:1000'
        ]);
        
        $chamada = Chamada::findOrFail($id);
        $chamada->justificar($request->observacoes);
        
        return redirect()->route('admin.chamadas.relatorio-aluno', ['matricula' => $chamada->matricula])
                        ->with('success', 'Falta justificada com sucesso!');
    }

    /**
     * Remove justificativa de falta
     */
    public function removerJustificativa($id)
    {
        $chamada = Chamada::findOrFail($id);
        $chamada->removerJustificativa();
        
        return redirect()->route('admin.chamadas.relatorio-aluno', ['matricula' => $chamada->matricula])
                        ->with('success', 'Justificativa removida com sucesso!');
    }

    /**
     * Exibe interface para gerenciar chamadas de uma turma/disciplina
     */
    public function gerenciar(Request $request, $turma, $disciplina)
    {
        $professorId = $request->get('professor_id');
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        
        $turma = Turma::findOrFail($turma);
        $disciplina = Disciplina::findOrFail($disciplina);
        
        if (!$professorId) {
            $professorId = $this->obterProfessorVinculado($turma->id, $disciplina->id);
            
            if (!$professorId) {
                return redirect()->route('admin.chamadas.index')
                    ->with('error', 'Nenhum professor vinculado a esta turma/disciplina.');
            }
        }
        
        $professor = Professor::findOrFail($professorId);
        $chamadasPorDia = $this->obterChamadasPorDia($turma->id, $disciplina->id, $professorId, $dataInicio, $dataFim);
        
        return view('admin.chamadas.gerenciar', compact(
            'turma', 'disciplina', 'professor', 'chamadasPorDia', 'dataInicio', 'dataFim'
        ));
    }

    /**
     * Edita uma chamada específica
     */
    public function editarChamada(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:presente,falta',
            'justificada' => 'sometimes|boolean',
            'observacoes' => 'nullable|string|max:1000'
        ]);
        
        $chamada = Chamada::findOrFail($id);
        
        if (!$chamada->podeSerEditada()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta chamada não pode mais ser editada (mais de 7 dias).'
            ], 422);
        }
        
        $chamada->update([
            'status' => $request->status,
            'justificada' => $request->boolean('justificada', false),
            'observacoes' => $request->observacoes
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Chamada atualizada com sucesso!'
        ]);
    }

    /**
     * Exclui todas as chamadas de um dia específico
     */
    public function excluirChamadaDia($data, $turma, $disciplina)
    {
        $turma = Turma::findOrFail($turma);
        $disciplina = Disciplina::findOrFail($disciplina);
        $professorId = $this->obterProfessorVinculado($turma->id, $disciplina->id);
        
        if (!$professorId) {
            return response()->json([
                'success' => false,
                'message' => 'Professor não encontrado para esta turma/disciplina.'
            ], 404);
        }
        
        // Obter matrículas dos alunos da turma
        $matriculasAlunos = Aluno::where('turma_id', $turma->id)
            ->where('status_matricula', 'ativa')
            ->pluck('numero_matricula')
            ->toArray();
        
        $chamadasExcluidas = Chamada::where('disciplina_id', $disciplina->id)
            ->where('professor_id', $professorId)
            ->where('data_chamada', $data)
            ->whereIn('matricula', $matriculasAlunos)
            ->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Chamada do dia {$data} excluída com sucesso! ({$chamadasExcluidas} registros removidos)"
        ]);
    }

    /**
     * Exibe relatórios de chamadas para professores
     */
    public function relatorioProfessor(Request $request)
    {
        $professor = auth()->user()->professor;
        
        if (!$professor) {
            abort(403, 'Professor não encontrado');
        }
        
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        $turmaId = $request->get('turma_id');
        $disciplinaId = $request->get('disciplina_id');
        $buscaAluno = $request->get('busca_aluno');
        
        // Buscar turmas vinculadas ao professor
        $turmasComVinculo = DB::table('professor_disciplina_turma')
            ->join('turmas', 'professor_disciplina_turma.turma_id', '=', 'turmas.id')
            ->join('disciplinas', 'professor_disciplina_turma.disciplina_id', '=', 'disciplinas.id')
            ->where('professor_disciplina_turma.professor_id', $professor->id)
            ->select(
                'turmas.id as turma_id',
                'turmas.nome as turma_nome',
                'turmas.serie',
                'turmas.turno',
                'disciplinas.id as disciplina_id',
                'disciplinas.nome as disciplina_nome',
                'disciplinas.codigo as disciplina_codigo'
            )
            ->get();
        
        // Buscar estatísticas de chamadas
        $query = Chamada::where('professor_id', $professor->id)
            ->whereDate('data_chamada', '>=', $dataInicio)
            ->whereDate('data_chamada', '<=', $dataFim);
        
        if ($turmaId) {
            $matriculasAlunos = Aluno::where('turma_id', $turmaId)
                ->where('status_matricula', 'ativa')
                ->pluck('numero_matricula');
            $query->whereIn('matricula', $matriculasAlunos);
        }
        
        if ($disciplinaId) {
            $query->where('disciplina_id', $disciplinaId);
        }
        
        if ($buscaAluno) {
            // Buscar por matrícula ou nome do aluno
            $query->where(function($q) use ($buscaAluno) {
                $q->where('matricula', 'like', '%' . $buscaAluno . '%')
                  ->orWhereHas('aluno', function($subQuery) use ($buscaAluno) {
                      $subQuery->where('nome', 'like', '%' . $buscaAluno . '%');
                  });
            });
        }
        
        $chamadas = $query->with(['aluno', 'disciplina'])->get();
        
        // Calcular estatísticas
        // Total de chamadas = número de chamadas únicas por disciplina/data
        $chamadasUnicas = $chamadas->groupBy(function($chamada) {
            return $chamada->disciplina_id . '-' . $chamada->data_chamada;
        });
        $totalChamadas = $chamadasUnicas->count();
        
        $totalPresencas = $chamadas->where('status', 'presente')->count();
        $totalFaltas = $chamadas->where('status', 'falta')->count();
        $totalRegistros = $chamadas->count();
        $percentualPresenca = $totalRegistros > 0 ? round(($totalPresencas / $totalRegistros) * 100, 2) : 0;
        
        return view('professor.chamadas.relatorio', compact(
            'turmasComVinculo', 'chamadas', 'dataInicio', 'dataFim', 'turmaId', 'disciplinaId', 'buscaAluno',
            'totalChamadas', 'totalPresencas', 'totalFaltas', 'totalRegistros', 'percentualPresenca'
        ));
    }

    /**
     * Exibe interface para gerenciar chamadas de uma turma/disciplina (Professor)
     */
    public function gerenciarProfessor(Request $request, $turma, $disciplina)
    {
        $professor = auth()->user()->professor;
        
        if (!$professor) {
            abort(403, 'Professor não encontrado');
        }
        
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        
        $turma = Turma::findOrFail($turma);
        $disciplina = Disciplina::findOrFail($disciplina);
        
        // Verificar se o professor está vinculado à turma e disciplina
        $vinculo = DB::table('professor_disciplina_turma')
            ->where('professor_id', $professor->id)
            ->where('turma_id', $turma->id)
            ->where('disciplina_id', $disciplina->id)
            ->exists();
            
        if (!$vinculo) {
            abort(403, 'Você não tem permissão para gerenciar chamadas desta turma/disciplina.');
        }
        
        $chamadasPorDia = $this->obterChamadasPorDia($turma->id, $disciplina->id, $professor->id, $dataInicio, $dataFim);
        
        return view('professor.chamadas.gerenciar', compact(
            'turma', 'disciplina', 'professor', 'chamadasPorDia', 'dataInicio', 'dataFim'
        ));
    }

    /**
     * Retorna presenças de um aluno específico
     */
    public function presencasAluno(Request $request, $matricula)
    {
        $dataInicio = $request->get('data_inicio');
        $dataFim = $request->get('data_fim');
        $disciplinaId = $request->get('disciplina_id');
        
        $query = Chamada::with(['disciplina', 'professor'])
            ->porAluno($matricula)
            ->presencas();
        
        if ($dataInicio && $dataFim) {
            $query->porPeriodo(Carbon::parse($dataInicio), Carbon::parse($dataFim));
        }
        
        if ($disciplinaId) {
            $query->porDisciplina($disciplinaId);
        }
        
        $presencas = $query->orderBy('data_chamada', 'desc')->get();
        
        return response()->json([
            'presencas' => $presencas->map(function ($presenca) {
                return [
                    'id' => $presenca->id,
                    'data_chamada' => $presenca->data_chamada->format('d/m/Y'),
                    'disciplina' => $presenca->disciplina->nome,
                    'professor' => $presenca->professor->nome,
                    'status' => $presenca->status,
                    'justificada' => $presenca->justificada,
                    'observacoes' => $presenca->observacoes
                ];
            })
        ]);
    }

    // Métodos privados seguindo Object Calisthenics
    private function obterTurmasComVinculo()
    {
        return DB::table('professor_disciplina_turma as pdt')
            ->join('turmas as t', 't.id', '=', 'pdt.turma_id')
            ->join('disciplinas as d', 'd.id', '=', 'pdt.disciplina_id')
            ->join('professores as p', 'p.id', '=', 'pdt.professor_id')
            ->select(
                't.nome as turma_nome',
                't.id as turma_id',
                't.serie',
                't.ano_letivo',
                't.turno',
                'd.nome as disciplina_nome',
                'd.id as disciplina_id',
                'p.nome as professor_nome',
                'p.id as professor_id'
            )
            ->orderBy('t.nome')
            ->orderBy('t.ano_letivo')
            ->orderBy('t.turno')
            ->orderBy('d.nome')
            ->get()
            ->groupBy(function($item) {
                return $item->turma_nome . ' - ' . $item->ano_letivo . ' (' . ucfirst($item->turno) . ')';
            });
    }

    private function obterProfessorVinculado($turmaId, $disciplinaId)
    {
        $vinculo = DB::table('professor_disciplina_turma')
            ->where('turma_id', $turmaId)
            ->where('disciplina_id', $disciplinaId)
            ->first();
            
        return $vinculo ? $vinculo->professor_id : null;
    }

    private function obterAlunosDaTurma($turmaId)
    {
        return Aluno::where('turma_id', $turmaId)
            ->where('status_matricula', 'ativa')
            ->orderBy('nome')
            ->get();
    }

    private function obterChamadasExistentes($disciplinaId, $professorId, $data, $turmaId = null)
    {
        $query = Chamada::where('disciplina_id', $disciplinaId)
            ->where('professor_id', $professorId)
            ->where('data_chamada', $data);
            
        // Se turma_id for fornecida, filtrar apenas pelos alunos dessa turma
        if ($turmaId) {
            $matriculasAlunos = Aluno::where('turma_id', $turmaId)
                ->where('status_matricula', 'ativa')
                ->pluck('numero_matricula')
                ->toArray();
                
            $query->whereIn('matricula', $matriculasAlunos);
        }
            
        return $query->pluck('matricula')->toArray();
    }

    private function chamadaJaExiste(Request $request): bool
    {
        // Obter matrículas dos alunos da turma específica
        $matriculasAlunos = Aluno::where('turma_id', $request->turma_id)
            ->where('status_matricula', 'ativa')
            ->pluck('numero_matricula')
            ->toArray();
            
        return Chamada::where('disciplina_id', $request->disciplina_id)
                     ->where('professor_id', $request->professor_id)
                     ->whereDate('data_chamada', $request->data_chamada)
                     ->whereIn('matricula', $matriculasAlunos)
                     ->exists();
    }

    private function solicitarConfirmacaoReenvio()
    {
        return redirect()->back()
                       ->withInput()
                       ->with('warning', 'Já existe uma chamada cadastrada para este dia. Deseja confirmar o reenvio?')
                       ->with('mostrar_confirmacao', true);
    }

    private function processarChamada(Request $request): void
    {
        $this->removerChamadasExistentes($request);
        $this->registrarNovasChamadas($request);
    }

    private function removerChamadasExistentes(Request $request): void
    {
        // Obter matrículas dos alunos da turma específica
        $matriculasAlunos = Aluno::where('turma_id', $request->turma_id)
            ->where('status_matricula', 'ativa')
            ->pluck('numero_matricula')
            ->toArray();
            
        Chamada::where('disciplina_id', $request->disciplina_id)
               ->where('professor_id', $request->professor_id)
               ->where('data_chamada', $request->data_chamada)
               ->whereIn('matricula', $matriculasAlunos)
               ->delete();
    }

    private function registrarNovasChamadas(Request $request): void
    {
        $presencas = $request->get('presencas', []);
        $alunos = $this->obterAlunosDaTurma($request->turma_id);
        
        foreach ($alunos as $aluno) {
            $status = in_array($aluno->numero_matricula, $presencas) ? 'presente' : 'falta';
            
            Chamada::create([
                'matricula' => $aluno->numero_matricula,
                'disciplina_id' => $request->disciplina_id,
                'professor_id' => $request->professor_id,
                'data_chamada' => $request->data_chamada,
                'status' => $status
            ]);
        }
    }

    private function obterChamadasDoAluno($matricula, $dataInicio, $dataFim)
    {
        return Chamada::with(['disciplina', 'professor'])
            ->porAluno($matricula)
            ->porPeriodo(Carbon::parse($dataInicio), Carbon::parse($dataFim))
            ->orderBy('data_chamada', 'desc')
            ->get();
    }

    private function obterChamadasPorDia($turmaId, $disciplinaId, $professorId, $dataInicio, $dataFim)
    {
        // Obter matrículas dos alunos da turma
        $matriculasAlunos = Aluno::where('turma_id', $turmaId)
            ->where('status_matricula', 'ativa')
            ->pluck('numero_matricula')
            ->toArray();
        
        return Chamada::with(['aluno'])
            ->where('disciplina_id', $disciplinaId)
            ->where('professor_id', $professorId)
            ->whereIn('matricula', $matriculasAlunos)
            ->whereBetween('data_chamada', [$dataInicio, $dataFim])
            ->orderBy('data_chamada', 'desc')
            ->get()
            ->groupBy(function($chamada) {
                return $chamada->data_chamada->format('Y-m-d');
            });
    }
}
