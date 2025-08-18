<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Chamada;
use Carbon\Carbon;

class JustificativaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Apenas administradores podem justificar faltas
        return auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'chamada_id' => 'required|exists:chamadas,id',
            'motivo' => 'required|string|min:10|max:500',
            'documento_comprobatorio' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'data_justificativa' => 'sometimes|date|before_or_equal:today'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'chamada_id.required' => 'A chamada é obrigatória.',
            'chamada_id.exists' => 'A chamada selecionada não existe.',
            'motivo.required' => 'O motivo da justificativa é obrigatório.',
            'motivo.string' => 'O motivo deve ser um texto.',
            'motivo.min' => 'O motivo deve ter pelo menos 10 caracteres.',
            'motivo.max' => 'O motivo não pode exceder 500 caracteres.',
            'documento_comprobatorio.file' => 'O documento deve ser um arquivo.',
            'documento_comprobatorio.mimes' => 'O documento deve ser PDF, JPG, JPEG ou PNG.',
            'documento_comprobatorio.max' => 'O documento não pode exceder 2MB.',
            'data_justificativa.date' => 'A data da justificativa deve ser válida.',
            'data_justificativa.before_or_equal' => 'A data da justificativa não pode ser futura.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->chamada_id) {
                return;
            }
            
            $this->validarChamadaPodeSerJustificada($validator);
            $this->validarPrazoJustificativa($validator);
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'data_justificativa' => $this->data_justificativa ?? now()->format('Y-m-d')
        ]);
    }

    // Métodos privados seguindo Object Calisthenics
    
    private function validarChamadaPodeSerJustificada($validator): void
    {
        $chamada = Chamada::find($this->chamada_id);
        
        if (!$chamada) {
            return;
        }
        
        if ($chamada->status !== 'falta') {
            $validator->errors()->add(
                'chamada_id', 
                'Apenas faltas podem ser justificadas.'
            );
            return;
        }
        
        if ($chamada->justificada) {
            $validator->errors()->add(
                'chamada_id', 
                'Esta falta já foi justificada.'
            );
        }
    }

    private function validarPrazoJustificativa($validator): void
    {
        $chamada = Chamada::find($this->chamada_id);
        
        if (!$chamada) {
            return;
        }
        
        $diasLimite = 30; // Prazo máximo para justificar uma falta
        $dataLimite = $chamada->data_chamada->addDays($diasLimite);
        
        if (now()->gt($dataLimite)) {
            $validator->errors()->add(
                'chamada_id', 
                "O prazo para justificar esta falta expirou. Limite: {$diasLimite} dias após a data da chamada."
            );
        }
    }
}