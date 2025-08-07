<?php

/**
 * Exemplos de uso dos middlewares de autorização criados
 * 
 * Este arquivo demonstra como aplicar os middlewares nas rotas
 * para controlar o acesso baseado no tipo de usuário.
 * 
 * Para usar estes exemplos, adicione as rotas ao arquivo web.php ou api.php
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\DashboardController;

// ========================================
// ROTAS PROTEGIDAS POR MIDDLEWARE
// ========================================

// 1. MIDDLEWARE CheckAdmin - Apenas administradores
Route::middleware(['auth', 'check.admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::resource('/admin/professores', ProfessorController::class);
    Route::resource('/admin/alunos', AlunoController::class);
});

// 2. MIDDLEWARE CheckProfessor - Professores e Administradores
Route::middleware(['auth', 'check.professor'])->group(function () {
    Route::get('/professor/dashboard', [ProfessorController::class, 'dashboard'])->name('professor.dashboard');
    Route::get('/professor/turmas', [ProfessorController::class, 'turmas'])->name('professor.turmas');
    Route::get('/professor/notas', [ProfessorController::class, 'notas'])->name('professor.notas');
    Route::post('/professor/notas/{aluno}', [ProfessorController::class, 'atribuirNota'])->name('professor.atribuir-nota');
});

// 3. MIDDLEWARE CheckAluno - Alunos e Administradores
Route::middleware(['auth', 'check.aluno'])->group(function () {
    Route::get('/aluno/dashboard', [AlunoController::class, 'dashboard'])->name('aluno.dashboard');
    Route::get('/aluno/notas', [AlunoController::class, 'notas'])->name('aluno.notas');
    Route::get('/aluno/perfil', [AlunoController::class, 'perfil'])->name('aluno.perfil');
    Route::put('/aluno/perfil', [AlunoController::class, 'atualizarPerfil'])->name('aluno.atualizar-perfil');
});

// 4. MIDDLEWARE CheckUserType - Múltiplos tipos permitidos
Route::middleware(['auth', 'check.user.type:admin,professor'])->group(function () {
    Route::get('/gestao/relatorios', [DashboardController::class, 'relatorios'])->name('gestao.relatorios');
    Route::get('/gestao/estatisticas', [DashboardController::class, 'estatisticas'])->name('gestao.estatisticas');
});

// Área compartilhada entre professores e alunos
Route::middleware(['auth', 'check.user.type:professor,aluno'])->group(function () {
    Route::get('/biblioteca', [DashboardController::class, 'biblioteca'])->name('biblioteca');
    Route::get('/calendario', [DashboardController::class, 'calendario'])->name('calendario');
});

// ========================================
// EXEMPLOS DE USO EM CONTROLLERS
// ========================================

/**
 * Exemplo de uso direto em métodos de controller:
 * 
 * class ExemploController extends Controller
 * {
 *     public function __construct()
 *     {
 *         // Aplicar middleware a todos os métodos
 *         $this->middleware('check.admin');
 *         
 *         // Aplicar middleware apenas a métodos específicos
 *         $this->middleware('check.professor')->only(['create', 'store']);
 *         
 *         // Aplicar middleware exceto a métodos específicos
 *         $this->middleware('check.aluno')->except(['index', 'show']);
 *         
 *         // Aplicar middleware com parâmetros
 *         $this->middleware('check.user.type:admin,professor')->only(['edit', 'update']);
 *     }
 * }
 */

// ========================================
// REDIRECIONAMENTOS AUTOMÁTICOS
// ========================================

/**
 * Os middlewares implementam redirecionamentos inteligentes:
 * 
 * 1. Usuário não autenticado → /login
 * 2. Usuário sem permissão → Dashboard apropriado com mensagem de erro
 *    - Admin → /admin/dashboard
 *    - Professor → /professor/dashboard  
 *    - Aluno → /aluno/dashboard
 * 
 * As mensagens de erro são automaticamente adicionadas à sessão
 * e podem ser exibidas usando @if(session('error')) no Blade.
 */

// ========================================
// TESTANDO OS MIDDLEWARES
// ========================================

/**
 * Para testar os middlewares:
 * 
 * 1. Execute: php artisan test tests/Feature/MiddlewareAuthorizationTest.php
 * 2. Crie usuários de teste:
 *    - Admin: User::factory()->admin()->create()
 *    - Professor: User::factory()->professor()->create()
 *    - Aluno: User::factory()->aluno()->create()
 * 
 * 3. Teste manualmente fazendo login e acessando rotas protegidas
 */