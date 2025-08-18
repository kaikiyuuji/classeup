<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PresencaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data_chamada' => $this->formatarDataChamada(),
            'status' => $this->status,
            'status_formatado' => $this->formatarStatus(),
            'justificada' => $this->justificada,
            'observacoes' => $this->observacoes,
            'aluno' => [
                'id' => $this->aluno->id,
                'nome' => $this->aluno->nome,
                'numero_matricula' => $this->aluno->numero_matricula,
                'foto' => $this->aluno->foto
            ],
            'disciplina' => [
                'id' => $this->disciplina->id,
                'nome' => $this->disciplina->nome,
                'codigo' => $this->disciplina->codigo,
                'carga_horaria' => $this->disciplina->carga_horaria
            ],
            'professor' => [
                'id' => $this->professor->id,
                'nome' => $this->professor->nome
            ],
            'turma' => [
                'id' => $this->turma->id,
                'nome' => $this->turma->nome,
                'ano' => $this->turma->ano,
                'semestre' => $this->turma->semestre
            ],
            'estatisticas' => $this->when($request->has('incluir_estatisticas'), function () {
                return $this->calcularEstatisticas();
            }),
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i:s')
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
                'status_legenda' => [
                    'presente' => [
                        'label' => 'Presente',
                        'color' => 'success',
                        'icon' => 'check-circle'
                    ],
                    'falta' => [
                        'label' => 'Falta',
                        'color' => 'danger',
                        'icon' => 'x-circle'
                    ]
                ],
                'justificativa_info' => [
                    'pode_justificar' => $this->podeJustificar(),
                    'prazo_justificativa_dias' => 30
                ]
            ]
        ];
    }

    // Métodos privados seguindo Object Calisthenics
    
    private function formatarDataChamada(): string
    {
        return $this->data_chamada?->format('d/m/Y') ?? '';
    }

    private function formatarStatus(): string
    {
        $statusMap = [
            'presente' => 'Presente',
            'falta' => $this->justificada ? 'Falta Justificada' : 'Falta'
        ];
        
        return $statusMap[$this->status] ?? 'Status Desconhecido';
    }

    private function podeJustificar(): bool
    {
        if ($this->status !== 'falta' || $this->justificada) {
            return false;
        }
        
        if (!$this->data_chamada) {
            return false;
        }
        
        $diasLimite = 30;
        $dataLimite = $this->data_chamada->addDays($diasLimite);
        
        return now()->lte($dataLimite);
    }

    private function calcularEstatisticas(): array
    {
        // Esta funcionalidade pode ser expandida para incluir estatísticas
        // específicas do aluno na disciplina/turma
        return [
            'total_chamadas' => 0, // Implementar conforme necessário
            'total_presencas' => 0,
            'total_faltas' => 0,
            'percentual_presenca' => 0.0
        ];
    }
}