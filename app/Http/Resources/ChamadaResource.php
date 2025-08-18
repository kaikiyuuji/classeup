<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChamadaResource extends JsonResource
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
            'justificada' => $this->justificada,
            'observacoes' => $this->observacoes,
            'aluno' => $this->whenLoaded('aluno', function () {
                return [
                    'id' => $this->aluno->id,
                    'nome' => $this->aluno->nome,
                    'numero_matricula' => $this->aluno->numero_matricula
                ];
            }),
            'disciplina' => $this->whenLoaded('disciplina', function () {
                return [
                    'id' => $this->disciplina->id,
                    'nome' => $this->disciplina->nome,
                    'codigo' => $this->disciplina->codigo
                ];
            }),
            'professor' => $this->whenLoaded('professor', function () {
                return [
                    'id' => $this->professor->id,
                    'nome' => $this->professor->nome
                ];
            }),
            'turma' => $this->whenLoaded('turma', function () {
                return [
                    'id' => $this->turma->id,
                    'nome' => $this->turma->nome,
                    'ano' => $this->turma->ano,
                    'semestre' => $this->turma->semestre
                ];
            }),
            'pode_ser_editada' => $this->podeSerEditada(),
            'dias_para_edicao' => $this->diasRestantesParaEdicao(),
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
                'status_options' => [
                    'presente' => 'Presente',
                    'falta' => 'Falta'
                ],
                'prazo_edicao_dias' => 7
            ]
        ];
    }

    // Métodos privados seguindo Object Calisthenics
    
    private function formatarDataChamada(): string
    {
        return $this->data_chamada?->format('d/m/Y') ?? '';
    }

    private function podeSerEditada(): bool
    {
        if (!$this->data_chamada) {
            return false;
        }
        
        $diasLimite = 7;
        $dataLimite = $this->data_chamada->addDays($diasLimite);
        
        return now()->lte($dataLimite);
    }

    private function diasRestantesParaEdicao(): int
    {
        if (!$this->data_chamada) {
            return 0;
        }
        
        $diasLimite = 7;
        $dataLimite = $this->data_chamada->addDays($diasLimite);
        $diasRestantes = now()->diffInDays($dataLimite, false);
        
        return max(0, (int) $diasRestantes);
    }
}