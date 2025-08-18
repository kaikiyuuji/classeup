<?php

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\ChamadaController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ========================================
// ROTAS PÚBLICAS
// ========================================

Route::get('/', function () {
    return view('welcome');
});

// ========================================
// ROTAS AUTENTICADAS GERAIS
// ========================================

// Rota principal do dashboard que redireciona baseado no tipo de usuário
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rotas específicas para cada tipo de dashboard
Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
    ->middleware(['auth', 'check.admin'])
    ->name('dashboard.admin');
    
Route::get('/professor/dashboard', [DashboardController::class, 'professor'])
    ->middleware(['auth', 'check.professor'])
    ->name('dashboard.professor');
    
Route::get('/aluno/dashboard', [DashboardController::class, 'aluno'])
    ->middleware(['auth', 'check.aluno'])
    ->name('dashboard.aluno');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========================================
// ROTAS EXCLUSIVAS PARA ADMINISTRADORES
// ========================================

Route::middleware(['auth', 'check.admin'])->prefix('admin')->name('admin.')->group(function () {
    // CRUD de Alunos
    Route::resource('alunos', AlunoController::class);
    Route::get('alunos/{aluno}/boletim', [AlunoController::class, 'boletim'])->name('alunos.boletim');
    Route::post('alunos/{aluno}/notas', [AlunoController::class, 'atualizarNotas'])->name('alunos.atualizar-notas');
    Route::put('alunos/{aluno}/avaliacoes/{avaliacao}', [AlunoController::class, 'atualizarAvaliacao'])->name('alunos.avaliacoes.update');
    Route::get('alunos/{aluno}/presencas', [AlunoController::class, 'presencasAluno'])->name('alunos.presencas');
    Route::get('alunos/{aluno}/disciplinas', [AlunoController::class, 'disciplinasAluno'])->name('alunos.disciplinas');
    
    // CRUD de Professores
    Route::resource('professores', ProfessorController::class)->parameters([
        'professores' => 'professor'
    ]);
    Route::post('professores/{professor}/vincular-disciplina', [ProfessorController::class, 'vincularDisciplina'])->name('professores.vincular-disciplina');
    Route::delete('professores/{professor}/desvincular-disciplina', [ProfessorController::class, 'desvincularDisciplina'])->name('professores.desvincular-disciplina');
    
    // CRUD de Disciplinas
    Route::resource('disciplinas', DisciplinaController::class);
    
    // CRUD de Turmas
    Route::resource('turmas', TurmaController::class);
    Route::post('turmas/{turma}/vincular-alunos', [TurmaController::class, 'vincularAlunos'])->name('turmas.vincular-alunos');
    Route::delete('turmas/{turma}/alunos/{aluno}', [TurmaController::class, 'desvincularAluno'])->name('turmas.desvincular-aluno');
    Route::post('turmas/{turma}/vincular-professor', [TurmaController::class, 'vincularProfessor'])->name('turmas.vincular-professor');
    Route::delete('turmas/{turma}/desvincular-professor', [TurmaController::class, 'desvincularProfessor'])->name('turmas.desvincular-professor');
    
    // Matrículas (Vinculação de Alunos às Turmas)
    Route::get('matriculas', [TurmaController::class, 'matriculas'])->name('matriculas.index');
    Route::post('matriculas/{aluno}', [TurmaController::class, 'matricularAluno'])->name('matriculas.store');
    Route::delete('matriculas/{aluno}', [TurmaController::class, 'desmatricularAluno'])->name('matriculas.destroy');
    
    // Gerenciamento de Usuários
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/professores', [UserController::class, 'professores'])->name('professores');
        Route::get('/alunos', [UserController::class, 'alunos'])->name('alunos');
        Route::post('/vincular-professor/{professor}', [UserController::class, 'vinculaProfessor'])->name('vincular-professor');
        Route::post('/vincular-aluno/{aluno}', [UserController::class, 'vinculaAluno'])->name('vincular-aluno');
        Route::patch('/ativar/{user}', [UserController::class, 'ativarUsuario'])->name('ativar');
        Route::patch('/desativar/{user}', [UserController::class, 'desativarUsuario'])->name('desativar');
    });
    
    // Gerenciamento de Chamadas Administrativo
    Route::prefix('chamadas')->name('chamadas.')->group(function () {
        Route::get('/', [ChamadaController::class, 'index'])->name('index');
        Route::get('/chamada/{turma}/{disciplina}', [ChamadaController::class, 'chamada'])->name('chamada');
        Route::post('/chamada', [ChamadaController::class, 'store'])->name('store');
        Route::get('/gerenciar/{turma}/{disciplina}', [ChamadaController::class, 'gerenciar'])->name('gerenciar');
        Route::put('/editar/{chamada}', [ChamadaController::class, 'editarChamada'])->name('editar');
        Route::delete('/excluir/{data}/{turma}/{disciplina}', [ChamadaController::class, 'excluirChamadaDia'])->name('excluir-dia');
        Route::get('/relatorio', [ChamadaController::class, 'relatorioProfessor'])->name('relatorio');
        Route::get('/relatorio-aluno/{matricula?}', [ChamadaController::class, 'relatorioAluno'])->name('relatorio-aluno');
        Route::get('/justificar/{chamada}', [ChamadaController::class, 'justificar'])->name('justificar');
        Route::post('/justificar/{chamada}', [ChamadaController::class, 'processarJustificativa'])->name('processar-justificativa');
        Route::delete('/justificar/{chamada}', [ChamadaController::class, 'removerJustificativa'])->name('remover-justificativa');
        Route::get('/presencas-aluno/{matricula}', [ChamadaController::class, 'presencasAluno'])->name('presencas-aluno');
    });

    // Relatórios Administrativos
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/', [AlunoController::class, 'relatoriosAdmin'])->name('index');
        Route::get('/frequencia', [ChamadaController::class, 'relatorioFrequencia'])->name('frequencia');
        Route::get('/notas', [AlunoController::class, 'relatorioNotas'])->name('notas');
        Route::get('/turmas', [TurmaController::class, 'relatorioTurmas'])->name('turmas');
        Route::get('/professores', [ProfessorController::class, 'relatorioProfessores'])->name('professores');
    });
});

// ========================================
// ROTAS PARA PROFESSORES (E ADMINISTRADORES)
// ========================================

Route::middleware(['auth', 'check.professor'])->prefix('professor')->name('professor.')->group(function () {
    
    // API para Dashboard do Professor
    Route::get('/api/turmas', [DashboardController::class, 'turmasProfessor'])->name('api.turmas');
    Route::get('/api/turmas/{turma_id}/alunos', [DashboardController::class, 'alunosTurma'])->name('api.turmas.alunos');
    
    // Visualização de Turmas e Alunos
    Route::get('/turmas', [ProfessorController::class, 'minhasTurmas'])->name('turmas.index');
    Route::get('/turmas/{turma}', [TurmaController::class, 'show'])->name('turmas.show');
    Route::get('/alunos/{aluno}', [AlunoController::class, 'show'])->name('alunos.show');
    
    // Lançamento de Chamadas
    Route::post('/chamada/lancar', [\App\Http\Controllers\Professor\ChamadaController::class, 'lancarChamada'])->name('chamada.lancar');
    Route::get('/chamada/{turma}/{disciplina}', [\App\Http\Controllers\Professor\ChamadaController::class, 'chamada'])->name('chamada.fazer');
    Route::post('/chamada/salvar', [\App\Http\Controllers\Professor\ChamadaController::class, 'salvarChamada'])->name('chamada.salvar');
    
    // Lançamento de Notas
    Route::get('/notas', [AlunoController::class, 'lancamentoNotas'])->name('notas.index');
    Route::get('/notas/{turma}/{disciplina}', [AlunoController::class, 'editarNotas'])->name('notas.editar');
    Route::put('/alunos/{aluno}/avaliacoes/{avaliacao}', [AlunoController::class, 'atualizarAvaliacao'])->name('avaliacoes.update');
    
    // Gerenciamento de Chamadas
    Route::prefix('chamadas')->name('chamadas.')->group(function () {
        Route::get('/', [ChamadaController::class, 'indexProfessor'])->name('index');
        Route::get('/relatorio', [ChamadaController::class, 'relatorioProfessor'])->name('relatorio');
        Route::get('/gerenciar/{turma}/{disciplina}', [ChamadaController::class, 'gerenciarProfessor'])->name('gerenciar');
    });
    
    // Relatórios do Professor
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/minhas-turmas', [ProfessorController::class, 'relatorioMinhasTurmas'])->name('turmas');
        Route::get('/frequencia-turma/{turma}', [ChamadaController::class, 'relatorioFrequenciaTurma'])->name('frequencia');
    });
});

// ========================================
// ROTAS PARA ALUNOS (E ADMINISTRADORES)
// ========================================

Route::middleware(['auth', 'check.aluno'])->prefix('aluno')->name('aluno.')->group(function () {
    
    // Boletim Individual (com verificação adicional de propriedade)
    Route::get('/boletim', [AlunoController::class, 'meuBoletim'])->name('boletim');
    Route::get('/boletim/{aluno}', [AlunoController::class, 'boletim'])
        ->name('boletim.show')
        ->middleware('check.boletim.ownership');
    
    // Consulta de Chamadas
    Route::prefix('chamadas')->name('chamadas.')->group(function () {
        Route::get('/', [ChamadaController::class, 'minhasChamadas'])->name('index');
        Route::get('/justificar/{chamada}', [ChamadaController::class, 'justificar'])
            ->name('justificar')
            ->middleware('check.chamada.ownership');
        Route::post('/justificar/{chamada}', [ChamadaController::class, 'processarJustificativa'])->name('processar-justificativa');
        Route::delete('/justificar/{chamada}', [ChamadaController::class, 'removerJustificativa'])->name('remover-justificativa');
    });
    
    // Perfil do Aluno
    Route::get('/perfil', [AlunoController::class, 'meuPerfil'])->name('perfil');
    Route::put('/perfil', [AlunoController::class, 'atualizarMeuPerfil'])->name('perfil.update');
});

// ========================================
// ROTAS COMPARTILHADAS (MÚLTIPLOS TIPOS)
// ========================================

// Biblioteca e Recursos Educacionais (Professor + Aluno)
Route::middleware(['auth', 'check.user.type:professor,aluno'])->group(function () {
    Route::get('/biblioteca', function () {
        return view('biblioteca.index');
    })->name('biblioteca');
    
    Route::get('/calendario', function () {
        return view('calendario.index');
    })->name('calendario');
});

// Gestão e Estatísticas (Admin + Professor)
Route::middleware(['auth', 'check.user.type:admin,professor'])->prefix('gestao')->name('gestao.')->group(function () {
    Route::get('/estatisticas', [TurmaController::class, 'estatisticas'])->name('estatisticas');
    Route::get('/dashboard-gestao', function () {
        return view('gestao.dashboard');
    })->name('dashboard');
});

require __DIR__.'/auth.php';
