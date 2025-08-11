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
    public function professores(Request $request): View
    {
        $query = Professor::with('user');

        // Filtro de busca por nome, email ou especialidade
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('especialidade', 'like', "%{$search}%")
                  ->orWhere('formacao', 'like', "%{$search}%");
            });
        }

        // Filtro por status de usuário
        if ($request->filled('status_usuario')) {
            $statusUsuario = $request->get('status_usuario');
            if ($statusUsuario === 'com_usuario') {
                $query->whereHas('user');
            } elseif ($statusUsuario === 'sem_usuario') {
                $query->whereDoesntHave('user');
            } elseif ($statusUsuario === 'ativo') {
                $query->whereHas('user', function ($q) {
                    $q->whereNotNull('email_verified_at');
                });
            } elseif ($statusUsuario === 'inativo') {
                $query->whereHas('user', function ($q) {
                    $q->whereNull('email_verified_at');
                });
            }
        }

        // Lógica de ordenação
        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        
        // Validar campos de ordenação permitidos
        $allowedSortFields = ['nome', 'email', 'especialidade', 'formacao', 'ativo'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'nome';
        }
        
        // Validar direção de ordenação
        $allowedSortDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $allowedSortDirections)) {
            $sortDirection = 'asc';
        }

        $professores = $query->orderBy($sortField, $sortDirection)
            ->paginate(15)
            ->withQueryString();
            
        return view('admin.usuarios.professores', compact('professores', 'sortField', 'sortDirection'));
    }
    
    /**
     * Exibe lista de alunos com status de usuário
     */
    public function alunos(Request $request): View
    {
        $query = Aluno::with(['user', 'turma']);

        // Filtro de busca por nome, email ou matrícula
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('numero_matricula', 'like', "%{$search}%");
            });
        }

        // Filtro por status de usuário
        if ($request->filled('status_usuario')) {
            $statusUsuario = $request->get('status_usuario');
            if ($statusUsuario === 'com_usuario') {
                $query->whereHas('user');
            } elseif ($statusUsuario === 'sem_usuario') {
                $query->whereDoesntHave('user');
            } elseif ($statusUsuario === 'ativo') {
                $query->whereHas('user', function ($q) {
                    $q->whereNotNull('email_verified_at');
                });
            } elseif ($statusUsuario === 'inativo') {
                $query->whereHas('user', function ($q) {
                    $q->whereNull('email_verified_at');
                });
            }
        }

        // Filtro por turma
        if ($request->filled('turma_id')) {
            $query->where('turma_id', $request->get('turma_id'));
        }

        // Lógica de ordenação
        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        
        // Validar campos de ordenação permitidos
        $allowedSortFields = ['nome', 'numero_matricula', 'email', 'turma'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'nome';
        }
        
        // Validar direção de ordenação
        $allowedSortDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $allowedSortDirections)) {
            $sortDirection = 'asc';
        }

        // Ordenação especial para turma (relacionamento)
        if ($sortField === 'turma') {
            $query->leftJoin('turmas', 'alunos.turma_id', '=', 'turmas.id')
                  ->select('alunos.*')
                  ->orderBy('turmas.nome', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $alunos = $query->paginate(15)
            ->withQueryString();
            
        // Buscar turmas para o filtro
        $turmas = \App\Models\Turma::orderBy('nome')->get();
            
        return view('admin.usuarios.alunos', compact('alunos', 'turmas', 'sortField', 'sortDirection'));
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
        return "{$nomeFormatado}.professor@classeup.br";
    }
    
    /**
     * Gera email para aluno
     */
    private function gerarEmailAluno(Aluno $aluno): string
    {
        return "{$aluno->numero_matricula}@aluno.classeup.br";
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
