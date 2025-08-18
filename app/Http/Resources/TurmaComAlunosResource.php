<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TurmaComAlunosResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'turma' => [
                'id' => $this->id,
                'nome' => $this->nome,
                'serie' => ucfirst($this->serie),
                'turno' => ucfirst($this->turno),
                'capacidade_maxima' => $this->capacidade_maxima,
                'total_alunos' => $this->whenCounted('alunos')
            ],
            'alunos' => AlunoTurmaResource::collection($this->whenLoaded('alunos'))
        ];
    }
}