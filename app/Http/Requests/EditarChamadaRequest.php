<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Chamada;
use Carbon\Carbon;

class EditarChamadaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $chamada = $this->obterChamada();
        
        if (!$chamada) {
            return false;
        }
        
        // Administradores podem editar qualquer chamada
        if (auth()->user()->hasRole('admin')) {
            return true;
        }
        
        // Professores só podem editar suas próprias chamadas
        if (auth()->user()->hasRole('professor')) {
            return $this->professorPodeEditarChamada($chamada);
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:presente,falta',
            'justificada' => 'sometimes|boolean',
            'observacoes' => 'nullable|string|max:1000',
            'motivo_edicao' => 'sometimes|string|max:255'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'O status da presença é obrigatório.',
            'status.in' => 'O status deve ser "presente" ou "falta".',
            'justificada.boolean' => 'O campo justificada deve ser verdadeiro ou falso.',
            'observacoes.string' => 'As observações devem ser um texto.',
            'observacoes.max' => 'As observações não podem exceder 1000 caracteres.',
            'motivo_edicao.string' => 'O motivo da edição deve ser um texto.',
            'motivo_edicao.max' => 'O motivo da edição não pode exceder 255 caracteres.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $chamada = $this->obterChamada();
            
            if (!$chamada) {
                return;
            }
            
            $this->validarPrazoEdicao($validator, $chamada);
            $this->validarCoerenciaJustificativa($validator);
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Se status for 'presente', justificada deve ser false
        if ($this->status === 'presente') {
            $this->merge([
                'justificada' => false
            ]);
        }
    }

    // Métodos privados seguindo Object Calisthenics
    
    private function obterChamada(): ?Chamada
    {
        $chamadaId = $this->route('id') ?? $this->route('chamada');
        return Chamada::find($chamadaId);
    }

    private function professorPodeEditarChamada(Chamada $chamada): bool
    {
        $professor = auth()->user()->professor;
        
        if (!$professor) {
            return false;
        }
        
        return $chamada->professor_id === $professor->id;
    }

    private function validarPrazoEdicao($validator, Chamada $chamada): void
    {
        $diasLimite = 7; // Prazo máximo para editar uma chamada
        $dataLimite = $chamada->data_chamada->addDays($diasLimite);
        
        if (now()->gt($dataLimite)) {
            $validator->errors()->add(
                'chamada', 
                "Esta chamada não pode mais ser editada. Prazo limite: {$diasLimite} dias após a data da chamada."
            );
        }
    }

    private function validarCoerenciaJustificativa($validator): void
    {
        // Se status for 'presente', não pode ser justificada
        if ($this->status === 'presente' && $this->boolean('justificada')) {
            $validator->errors()->add(
                'justificada', 
                'Presenças não podem ser justificadas.'
            );
        }
        
        // Se status for 'falta' e justificada for true, deve ter observações
        if ($this->status === 'falta' && $this->boolean('justificada') && empty($this->observacoes)) {
            $validator->errors()->add(
                'observacoes', 
                'Faltas justificadas devem ter observações explicativas.'
            );
        }
    }
}