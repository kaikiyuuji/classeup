<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chamada;
use App\Models\Turma;
use App\Models\Disciplina;
use App\Services\ChamadaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ChamadaController extends Controller
{
    private ChamadaService $chamadaService;

    public function __construct(ChamadaService $chamadaService)
    {
        $this->chamadaService = $chamadaService;
    }

    /**
     * Edita uma chamada específica
     */
    public function editarChamada(Request $request, $id): JsonResponse
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
    public function excluirChamadaDia($data, $turma, $disciplina): JsonResponse
    {
        $turma = Turma::findOrFail($turma);
        $disciplina = Disciplina::findOrFail($disciplina);
        $professorId = $this->chamadaService->obterProfessorVinculado($turma->id, $disciplina->id);
        
        if (!$professorId) {
            return response()->json([
                'success' => false,
                'message' => 'Professor não encontrado para esta turma/disciplina.'
            ], 404);
        }
        
        $chamadasExcluidas = $this->chamadaService->excluirChamadasDoDia(
            $data, 
            $turma->id, 
            $disciplina->id, 
            $professorId
        );
        
        return response()->json([
            'success' => true,
            'message' => "Chamada do dia {$data} excluída com sucesso! ({$chamadasExcluidas} registros removidos)"
        ]);
    }

    /**
     * Retorna presenças de um aluno específico
     */
    public function presencasAluno(Request $request, $matricula): JsonResponse
    {
        $filtros = $this->extrairFiltrosPresencas($request);
        $presencas = $this->chamadaService->obterPresencasAluno($matricula, $filtros);
        
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

    /**
     * Retorna estatísticas de chamadas para um período
     */
    public function estatisticasChamadas(Request $request): JsonResponse
    {
        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'turma_id' => 'sometimes|exists:turmas,id',
            'disciplina_id' => 'sometimes|exists:disciplinas,id',
            'professor_id' => 'sometimes|exists:professores,id'
        ]);
        
        // Implementar lógica de estatísticas baseada nos filtros
        $filtros = $request->only(['data_inicio', 'data_fim', 'turma_id', 'disciplina_id', 'professor_id']);
        
        // Por enquanto, retorna estrutura básica - pode ser expandida conforme necessário
        return response()->json([
            'success' => true,
            'data' => [
                'periodo' => [
                    'inicio' => $filtros['data_inicio'],
                    'fim' => $filtros['data_fim']
                ],
                'estatisticas' => [
                    'total_chamadas' => 0,
                    'total_presencas' => 0,
                    'total_faltas' => 0,
                    'percentual_presenca' => 0
                ]
            ]
        ]);
    }

    // Métodos privados seguindo Object Calisthenics
    
    private function extrairFiltrosPresencas(Request $request): array
    {
        return [
            'data_inicio' => $request->get('data_inicio'),
            'data_fim' => $request->get('data_fim'),
            'disciplina_id' => $request->get('disciplina_id')
        ];
    }
}