<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\RegistrarChamadaData;
use App\Models\Falta;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FaltaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Falta::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'turma_id' => 'required|exists:turmas,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'professor_id' => 'required|exists:professores,id',
            'data_falta' => 'required|date',
            'faltas' => 'array',
            'faltas.*' => 'string',
            'confirmar_reenvio' => 'sometimes|boolean',
        ];
    }

    public function toDto(): RegistrarChamadaData
    {
        return RegistrarChamadaData::fromRequest($this);
    }
}
