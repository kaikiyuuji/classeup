<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChamadaCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = ChamadaResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'estatisticas' => $this->calcularEstatisticasGerais(),
            'resumo_periodo' => $this->obterResumoPeriodo()
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'total_registros' => $this->collection->count(),
                'filtros_aplicados' => $this->obterFiltrosAplicados($request),
                'opcoes_status' => [
                    'presente' => 'Presente',
                    'falta' => 'Falta'
                ],
                'configuracoes' => [
                    'prazo_edicao_dias' => 7,
                    'prazo_justificativa_dias' => 30
                ]
            ]
        ];
    }

    // Métodos privados seguindo Object Calisthenics
    
    private function calcularEstatisticasGerais(): array
    {
        $totalChamadas = $this->collection->count();
        
        if ($totalChamadas === 0) {
            return $this->criarEstatisticasVazias();
        }
        
        $presencas = $this->contarPresencas();
        $faltas = $this->contarFaltas();
        $faltasJustificadas = $this->contarFaltasJustificadas();
        
        return [
            'total_chamadas' => $totalChamadas,
            'total_presencas' => $presencas,
            'total_faltas' => $faltas,
            'faltas_justificadas' => $faltasJustificadas,
            'faltas_nao_justificadas' => $faltas - $faltasJustificadas,
            'percentual_presenca' => $this->calcularPercentualPresenca($presencas, $totalChamadas),
            'percentual_faltas' => $this->calcularPercentualFaltas($faltas, $totalChamadas)
        ];
    }

    private function criarEstatisticasVazias(): array
    {
        return [
            'total_chamadas' => 0,
            'total_presencas' => 0,
            'total_faltas' => 0,
            'faltas_justificadas' => 0,
            'faltas_nao_justificadas' => 0,
            'percentual_presenca' => 0.0,
            'percentual_faltas' => 0.0
        ];
    }

    private function contarPresencas(): int
    {
        return $this->collection->where('status', 'presente')->count();
    }

    private function contarFaltas(): int
    {
        return $this->collection->where('status', 'falta')->count();
    }

    private function contarFaltasJustificadas(): int
    {
        return $this->collection
            ->where('status', 'falta')
            ->where('justificada', true)
            ->count();
    }

    private function calcularPercentualPresenca(int $presencas, int $total): float
    {
        return round(($presencas / $total) * 100, 2);
    }

    private function calcularPercentualFaltas(int $faltas, int $total): float
    {
        return round(($faltas / $total) * 100, 2);
    }

    private function obterResumoPeriodo(): array
    {
        if ($this->collection->isEmpty()) {
            return [];
        }
        
        $datas = $this->collection->pluck('data_chamada')->filter();
        
        if ($datas->isEmpty()) {
            return [];
        }
        
        return [
            'data_inicio' => $datas->min()?->format('d/m/Y'),
            'data_fim' => $datas->max()?->format('d/m/Y'),
            'total_dias' => $datas->unique()->count()
        ];
    }

    private function obterFiltrosAplicados(Request $request): array
    {
        return array_filter([
            'data_inicio' => $request->get('data_inicio'),
            'data_fim' => $request->get('data_fim'),
            'turma_id' => $request->get('turma_id'),
            'disciplina_id' => $request->get('disciplina_id'),
            'professor_id' => $request->get('professor_id'),
            'status' => $request->get('status')
        ]);
    }
}