<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Turma;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlunoUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $aluno = $this->route('aluno');

        return $aluno !== null && ($this->user()?->can('update', $aluno) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $alunoId = $this->route('aluno')->id;

        return [
            'nome' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('alunos', 'email')->ignore($alunoId),
            ],
            'cpf' => [
                'required',
                'string',
                'size:11',
                Rule::unique('alunos', 'cpf')->ignore($alunoId),
            ],
            'data_nascimento' => 'required|date|before:today',
            'telefone' => 'nullable|string|max:15',
            'endereco' => 'nullable|string|max:500',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,webp|mimetypes:image/jpeg,image/png,image/webp|dimensions:max_width=2000,max_height=2000|max:2048',
            'status_matricula' => 'required|in:ativa,inativa',
            'turma_id' => [
                'nullable',
                'exists:turmas,id',
                function ($attribute, $value, $fail) {
                    if ($value && ! Turma::where('id', $value)->where('ativo', true)->exists()) {
                        $fail('A turma selecionada não está ativa.');
                    }
                },
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => $this->input('cpf') ? preg_replace('/\D/', '', $this->input('cpf')) : null,
            'telefone' => $this->input('telefone') ? preg_replace('/\D/', '', $this->input('telefone')) : null,
        ]);
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
            'email.unique' => 'Este email já está sendo usado por outro aluno.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter exatamente 11 dígitos.',
            'cpf.unique' => 'Este CPF já está sendo usado por outro aluno.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.date' => 'A data de nascimento deve ser uma data válida.',
            'data_nascimento.before' => 'A data de nascimento deve ser anterior à data atual.',
            'telefone.max' => 'O telefone não pode ter mais de 15 caracteres.',
            'endereco.max' => 'O endereço não pode ter mais de 500 caracteres.',
            'foto_perfil.image' => 'A foto de perfil deve ser uma imagem.',
            'foto_perfil.mimes' => 'A foto de perfil deve ser do tipo: jpeg, png ou webp.',
            'foto_perfil.mimetypes' => 'O conteúdo do arquivo não corresponde a uma imagem válida.',
            'foto_perfil.dimensions' => 'A foto de perfil deve ter no máximo 2000x2000 pixels.',
            'foto_perfil.max' => 'A foto de perfil não pode ser maior que 2MB.',
            'turma_id.exists' => 'A turma selecionada não existe.',
        ];
    }
}
