<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfessorUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $professorId = $this->route('professor')->id;

        return [
            'nome' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('professores', 'email')->ignore($professorId),
            ],
            'cpf' => [
                'required',
                'string',
                'size:11',
                Rule::unique('professores', 'cpf')->ignore($professorId),
            ],
            'data_nascimento' => 'required|date|before:today',
            'telefone' => 'nullable|string|max:15',
            'endereco' => 'nullable|string|max:500',
            'especialidade' => 'required|string|max:255',
            'formacao' => 'required|string|max:1000',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ativo' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'Este email já está sendo usado por outro professor.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter exatamente 11 dígitos.',
            'cpf.unique' => 'Este CPF já está sendo usado por outro professor.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.date' => 'A data de nascimento deve ser uma data válida.',
            'data_nascimento.before' => 'A data de nascimento deve ser anterior à data atual.',
            'telefone.max' => 'O telefone não pode ter mais de 15 caracteres.',
            'endereco.max' => 'O endereço não pode ter mais de 500 caracteres.',
            'especialidade.required' => 'A especialidade é obrigatória.',
            'especialidade.max' => 'A especialidade não pode ter mais de 255 caracteres.',
            'formacao.required' => 'A formação é obrigatória.',
            'formacao.max' => 'A formação não pode ter mais de 1000 caracteres.',
            'foto_perfil.image' => 'A foto de perfil deve ser uma imagem.',
            'foto_perfil.mimes' => 'A foto de perfil deve ser do tipo: jpeg, png, jpg ou gif.',
            'foto_perfil.max' => 'A foto de perfil não pode ser maior que 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'ativo' => $this->has('ativo'),
        ]);
    }
}