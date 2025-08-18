<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfessorDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->tipo === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $professor = $this->route('professor');
        
        return [
            'confirmacao_exclusao' => 'required|accepted',
            'nome_confirmacao' => [
                'required',
                'string',
                Rule::in([$professor->nome])
            ],
            'ciente_exclusao_dados' => 'required|accepted'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'confirmacao_exclusao.required' => 'Você deve confirmar a exclusão.',
            'confirmacao_exclusao.accepted' => 'Você deve confirmar a exclusão.',
            'nome_confirmacao.required' => 'Digite o nome completo do professor para confirmar.',
            'nome_confirmacao.in' => 'O nome digitado não confere com o nome do professor.',
            'ciente_exclusao_dados.required' => 'Você deve confirmar que está ciente da exclusão dos dados relacionados.',
            'ciente_exclusao_dados.accepted' => 'Você deve confirmar que está ciente da exclusão dos dados relacionados.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'confirmacao_exclusao' => 'confirmação de exclusão',
            'nome_confirmacao' => 'nome de confirmação',
            'ciente_exclusao_dados' => 'ciência da exclusão de dados'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalizar o nome digitado removendo espaços extras
        if ($this->has('nome_confirmacao')) {
            $this->merge([
                'nome_confirmacao' => trim($this->input('nome_confirmacao'))
            ]);
        }
    }
}