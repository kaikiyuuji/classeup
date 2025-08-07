<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Professor;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Exibe lista de professores com status de usuário
     */
    public function professores(): View
    {
        $professores = Professor::with('user')
            ->orderBy('nome')
            ->get();
            
        return view('admin.usuarios.professores', compact('professores'));
    }
    
    /**
     * Exibe lista de alunos com status de usuário
     */
    public function alunos(): View
    {
        $alunos = Aluno::with('user')
            ->orderBy('nome')
            ->get();
            
        return view('admin.usuarios.alunos', compact('alunos'));
    }
    
    /**
     * Vincula um professor a um usuário do sistema
     */
    public function vinculaProfessor(int $professorId): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $professor = $this->encontrarProfessor($professorId);
            $this->verificarSeJaPossuiUsuario($professor->user);
            
            $user = $this->criarUsuarioParaProfessor($professor);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', "Usuário criado com sucesso para o professor {$professor->nome}. Email: {$user->email}");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao criar usuário: ' . $e->getMessage());
        }
    }
    
    /**
     * Vincula um aluno a um usuário do sistema
     */
    public function vinculaAluno(int $alunoId): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $aluno = $this->encontrarAluno($alunoId);
            $this->verificarSeJaPossuiUsuario($aluno->user);
            
            $user = $this->criarUsuarioParaAluno($aluno);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', "Usuário criado com sucesso para o aluno {$aluno->nome}. Email: {$user->email}");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao criar usuário: ' . $e->getMessage());
        }
    }
    
    /**
     * Ativa um usuário existente
     */
    public function ativarUsuario(int $userId): RedirectResponse
    {
        $user = User::findOrFail($userId);
        $user->update(['email_verified_at' => now()]);
        
        return redirect()->back()
            ->with('success', 'Usuário ativado com sucesso.');
    }
    
    /**
     * Desativa um usuário existente
     */
    public function desativarUsuario(int $userId): RedirectResponse
    {
        $user = User::findOrFail($userId);
        $user->update(['email_verified_at' => null]);
        
        return redirect()->back()
            ->with('success', 'Usuário desativado com sucesso.');
    }
    
    /**
     * Encontra um professor pelo ID
     */
    private function encontrarProfessor(int $professorId): Professor
    {
        return Professor::findOrFail($professorId);
    }
    
    /**
     * Encontra um aluno pelo ID
     */
    private function encontrarAluno(int $alunoId): Aluno
    {
        return Aluno::findOrFail($alunoId);
    }
    
    /**
     * Verifica se já possui usuário vinculado
     */
    private function verificarSeJaPossuiUsuario(?User $user): void
    {
        if ($user) {
            throw new \Exception('Este registro já possui um usuário vinculado.');
        }
    }
    
    /**
     * Cria usuário para professor
     */
    private function criarUsuarioParaProfessor(Professor $professor): User
    {
        $email = $this->gerarEmailProfessor($professor);
        $senha = $this->gerarSenhaPadrao($professor->cpf);
        
        return User::create([
            'name' => $professor->nome,
            'email' => $email,
            'password' => Hash::make($senha),
            'tipo_usuario' => 'professor',
            'professor_id' => $professor->id,
            'email_verified_at' => now()
        ]);
    }
    
    /**
     * Cria usuário para aluno
     */
    private function criarUsuarioParaAluno(Aluno $aluno): User
    {
        $email = $this->gerarEmailAluno($aluno);
        $senha = $this->gerarSenhaPadrao($aluno->cpf);
        
        return User::create([
            'name' => $aluno->nome,
            'email' => $email,
            'password' => Hash::make($senha),
            'tipo_usuario' => 'aluno',
            'aluno_id' => $aluno->id,
            'email_verified_at' => now()
        ]);
    }
    
    /**
     * Gera email para professor
     */
    private function gerarEmailProfessor(Professor $professor): string
    {
        $nomeFormatado = $this->formatarNomeParaEmail($professor->nome);
        return "{$nomeFormatado}.professor@classeup.edu.br";
    }
    
    /**
     * Gera email para aluno
     */
    private function gerarEmailAluno(Aluno $aluno): string
    {
        return "{$aluno->numero_matricula}@aluno.classeup.edu.br";
    }
    
    /**
     * Formata nome para uso em email
     */
    private function formatarNomeParaEmail(string $nome): string
    {
        return strtolower(
            str_replace(' ', '.', 
                trim(
                    preg_replace('/[^a-zA-Z\s]/', '', $nome)
                )
            )
        );
    }
    
    /**
     * Gera senha padrão baseada no CPF
     */
    private function gerarSenhaPadrao(string $cpf): string
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }
}
