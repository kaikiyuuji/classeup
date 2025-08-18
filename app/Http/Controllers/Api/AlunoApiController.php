<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlunoPresencaResource;
use App\Http\Resources\DisciplinaResource;
use App\Models\Aluno;
use App\Models\Chamada;
use App\Models\Disciplina;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlunoApiController extends Controller
{
    /**
     * Retorna as presenças de um aluno via AJAX
     */
    public function presencasAluno(Request $request, Aluno $aluno): JsonResponse
    {
        $dataInicio = $request->get('data_inicio');
        $dataFim = $request->get('data_fim');
        $disciplinaId = $request->get('disciplina_id');
        
        $query = Chamada::with(['disciplina', 'professor'])
            ->porAluno($aluno->numero_matricula)
            ->presencas();
        
        if ($dataInicio && $dataFim) {
            $query->porPeriodo(Carbon::parse($dataInicio), Carbon::parse($dataFim));
        }
        
        if ($disciplinaId) {
            $query->porDisciplina($disciplinaId);
        }
        
        $presencas = $query->orderBy('data_chamada', 'desc')->paginate(10);
        
        return AlunoPresencaResource::collection($presencas)
            ->response()
            ->getData(true);
    }

    /**
     * Retorna as disciplinas que o aluno teve chamadas
     */
    public function disciplinasAluno(Aluno $aluno): JsonResponse
    {
        $disciplinas = Disciplina::whereHas('chamadas', function ($query) use ($aluno) {
            $query->where('matricula', $aluno->numero_matricula);
        })->select('id', 'nome')->get();

        return response()->json([
            'disciplinas' => DisciplinaResource::collection($disciplinas)
        ]);
    }
    
    /**
     * Retorna estatísticas básicas do aluno
     */
    public function estatisticas(Aluno $aluno): JsonResponse
    {
        $totalPresencas = Chamada::porAluno($aluno->numero_matricula)
            ->where('status', 'presente')
            ->count();
            
        $totalFaltas = Chamada::porAluno($aluno->numero_matricula)
            ->where('status', 'falta')
            ->count();
            
        $faltasNaoJustificadas = Chamada::porAluno($aluno->numero_matricula)
            ->where('status', 'falta')
            ->where('justificada', false)
            ->count();
            
        return response()->json([
            'total_presencas' => $totalPresencas,
            'total_faltas' => $totalFaltas,
            'faltas_nao_justificadas' => $faltasNaoJustificadas,
            'percentual_presenca' => $totalPresencas + $totalFaltas > 0 
                ? round(($totalPresencas / ($totalPresencas + $totalFaltas)) * 100, 2)
                : 0
        ]);
    }
}