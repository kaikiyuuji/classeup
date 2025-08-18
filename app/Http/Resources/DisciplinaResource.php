<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DisciplinaResource extends JsonResource
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
            'nome' => $this->nome,
            'codigo' => $this->codigo,
            'descricao' => $this->descricao,
            'carga_horaria' => $this->carga_horaria,
            'ativo' => $this->ativo,
            'status_texto' => $this->ativo ? 'Ativa' : 'Inativa',
            'total_professores' => $this->whenLoaded('professores', fn() => $this->professores->count()),
            'total_turmas' => $this->whenLoaded('turmas', fn() => $this->turmas->count()),
            'total_avaliacoes' => $this->whenLoaded('avaliacoes', fn() => $this->avaliacoes->count()),
            'pode_excluir' => !$this->possuiRelacionamentosAtivos(),
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
        ];
    }
}
