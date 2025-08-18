<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlunoTurmaResource extends JsonResource
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
            'numero_matricula' => $this->numero_matricula,
            'data_nascimento' => $this->when(
                $this->data_nascimento,
                $this->data_nascimento?->format('d/m/Y')
            ),
            'idade' => $this->when(
                $this->data_nascimento,
                $this->data_nascimento?->age
            ),
            'email' => $this->whenLoaded('user', $this->user?->email),
            'turma' => $this->whenLoaded('turma', $this->turma?->nome),
            'foto_perfil_url' => $this->foto_perfil_url
        ];
    }
}