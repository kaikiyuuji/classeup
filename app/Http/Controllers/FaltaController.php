<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Falta;
use App\Models\Turma;
use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FaltaController extends Controller
{
    /**
     * Exibe a lista de turmas para chamada
     */
    public function index()
    {
        $turmasComVinculo = $this->obterTurmasComVinculo();
        
        return view('admin.faltas.index', compact('turmasComVinculo'));
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
        
        // Se não foi especificado professor, pega o primeiro vinculado à turma/disciplina
        if (!$professorId) {
            $vinculo = DB::table('professor_disciplina_turma')
                ->where('turma_id', $turma->id)
                ->where('disciplina_id', $disciplina->id)
                ->first();
            
            if (!$vinculo) {
                return redirect()->route('faltas.index')
                    ->with('error', 'Nenhum professor vinculado a esta turma/disciplina.');
            }
            
            $professorId = $vinculo->professor_id;
        }
        
        $professor = Professor::findOrFail($professorId);
        $alunos = $this->obterAlunosDaTurma($turma->id);
        $faltasExistentes = $this->obterFaltasExistentes($disciplina->id, $professorId, $data);
        
        return view('admin.faltas.chamada', compact(
            'turma', 'disciplina', 'professor', 'alunos', 'faltasExistentes', 'data'
        ));
    }

    /**
     * Registra as faltas da chamada
     */
    public function store(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'professor_id' => 'required|exists:professores,id',
            'data_falta' => 'required|date',
            'faltas' => 'array',
            'faltas.*' => 'string',
            'confirmar_reenvio' => 'sometimes|boolean'
        ]);
        
        // Verifica se já existe chamada para o dia
        $chamadaExistente = Falta::where('disciplina_id', $request->disciplina_id)
                                 ->where('professor_id', $request->professor_id)
                                 ->whereDate('data_falta', $request->data_falta)
                                 ->exists();
        
        if ($chamadaExistente && !$request->has('confirmar_reenvio')) {
            return redirect()->back()
                           ->withInput()
                           ->with('warning', 'Já existe uma chamada cadastrada para este dia. Deseja confirmar o reenvio?')
                           ->with('mostrar_confirmacao', true);
        }
        
        $this->processarChamada($request);
        
        return redirect()->route('faltas.index')->with('success', 'Chamada registrada com sucesso!');
    }

    /**
     * Exibe relatório de faltas por aluno
     */
    public function relatorioAluno(Request $request)
    {
        $matricula = $request->get('matricula');
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        
        $aluno = null;
        $faltas = collect();
        
        if ($matricula) {
            $aluno = Aluno::where('numero_matricula', $matricula)->first();
            if ($aluno) {
                $faltas = $this->obterFaltasDoAluno($matricula, $dataInicio, $dataFim);
            }
        }
        
        return view('admin.faltas.relatorio-aluno', compact('aluno', 'faltas', 'matricula', 'dataInicio', 'dataFim'));
    }

    /**
     * Exibe formulário para justificar falta
     */
    public function justificar($id)
    {
        $falta = Falta::with(['aluno', 'disciplina', 'professor'])->findOrFail($id);
        
        return view('admin.faltas.justificar', compact('falta'));
    }

    /**
     * Processa justificativa de falta
     */
    public function processarJustificativa(Request $request, $id)
    {
        $request->validate([
            'observacoes' => 'required|string|max:1000'
        ]);
        
        $falta = Falta::findOrFail($id);
        $falta->justificar($request->observacoes);
        
        return redirect()->route('faltas.relatorio-aluno', ['matricula' => $falta->matricula])
                        ->with('success', 'Falta justificada com sucesso!');
    }

    /**
     * Remove justificativa de falta
     */
    public function removerJustificativa($id)
    {
        $falta = Falta::findOrFail($id);
        $falta->removerJustificativa();
        
        return redirect()->back()->with('success', 'Justificativa removida com sucesso!');
    }

    // Métodos auxiliares privados seguindo Object Calisthenics
    
    private function obterTurmasComVinculo()
    {
        return DB::table('turmas')
            ->join('professor_disciplina_turma', function($join) {
                $join->on('turmas.id', '=', 'professor_disciplina_turma.turma_id');
            })
            ->join('professores', 'professor_disciplina_turma.professor_id', '=', 'professores.id')
            ->join('disciplinas', 'professor_disciplina_turma.disciplina_id', '=', 'disciplinas.id')
            ->select(
                'turmas.id as turma_id',
                'turmas.nome as turma_nome',
                'turmas.serie',
                'disciplinas.id as disciplina_id',
                'disciplinas.nome as disciplina_nome',
                'professores.id as professor_id',
                'professores.nome as professor_nome'
            )
            ->orderBy('turmas.nome')
            ->orderBy('disciplinas.nome')
            ->get()
            ->groupBy('turma_nome');
    }
    
    private function obterAlunosDaTurma($turmaId)
    {
        return Aluno::where('turma_id', $turmaId)
                   ->orderBy('nome')
                   ->get();
    }
    
    private function obterFaltasExistentes($disciplinaId, $professorId, $data)
    {
        return Falta::where('disciplina_id', $disciplinaId)
                   ->where('professor_id', $professorId)
                   ->where('data_falta', $data)
                   ->pluck('matricula')
                   ->toArray();
    }
    
    private function processarChamada(Request $request)
    {
        DB::transaction(function() use ($request) {
            $this->removerFaltasExistentes($request);
            $this->registrarNovasFaltas($request);
        });
    }
    
    private function removerFaltasExistentes(Request $request)
    {
        Falta::where('disciplina_id', $request->disciplina_id)
             ->where('professor_id', $request->professor_id)
             ->where('data_falta', $request->data_falta)
             ->delete();
    }
    
    private function registrarNovasFaltas(Request $request)
    {
        $faltas = $request->get('faltas', []);
        
        foreach ($faltas as $matricula) {
            Falta::create([
                'matricula' => $matricula,
                'disciplina_id' => $request->disciplina_id,
                'professor_id' => $request->professor_id,
                'data_falta' => $request->data_falta,
                'justificada' => false
            ]);
        }
    }
    
    private function obterFaltasDoAluno($matricula, $dataInicio, $dataFim)
    {
        return Falta::with(['disciplina', 'professor'])
                   ->porAluno($matricula)
                   ->porPeriodo(Carbon::parse($dataInicio), Carbon::parse($dataFim))
                   ->orderBy('data_falta', 'desc')
                   ->get();
    }
}
