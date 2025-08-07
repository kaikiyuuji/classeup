<?php

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\FaltaController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::resource('alunos', AlunoController::class)->except(['show']);
    
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
    
    // Relatórios Administrativos
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/', [AlunoController::class, 'relatoriosAdmin'])->name('index');
        Route::get('/frequencia', [FaltaController::class, 'relatorioFrequencia'])->name('frequencia');
        Route::get('/notas', [AlunoController::class, 'relatorioNotas'])->name('notas');
        Route::get('/turmas', [TurmaController::class, 'relatorioTurmas'])->name('turmas');
        Route::get('/professores', [ProfessorController::class, 'relatorioProfessores'])->name('professores');
    });
});

// ========================================
// ROTAS PARA PROFESSORES (E ADMINISTRADORES)
// ========================================

Route::middleware(['auth', 'check.professor'])->prefix('professor')->name('professor.')->group(function () {
    // Dashboard do Professor
    Route::get('/dashboard', [ProfessorController::class, 'dashboard'])->name('dashboard');
    
    // Visualização de Turmas e Alunos
    Route::get('/turmas', [ProfessorController::class, 'minhasTurmas'])->name('turmas.index');
    Route::get('/turmas/{turma}', [TurmaController::class, 'show'])->name('turmas.show');
    Route::get('/alunos/{aluno}', [AlunoController::class, 'show'])->name('alunos.show');
    
    // Lançamento de Notas
    Route::get('/notas', [AlunoController::class, 'lancamentoNotas'])->name('notas.index');
    Route::get('/notas/{turma}/{disciplina}', [AlunoController::class, 'editarNotas'])->name('notas.editar');
    Route::put('/alunos/{aluno}/avaliacoes/{avaliacao}', [AlunoController::class, 'atualizarAvaliacao'])->name('avaliacoes.update');
    
    // Lançamento de Faltas
    Route::prefix('faltas')->name('faltas.')->group(function () {
        Route::get('/', [FaltaController::class, 'index'])->name('index');
        Route::get('/chamada/{turma}/{disciplina}', [FaltaController::class, 'chamada'])->name('chamada');
        Route::post('/chamada', [FaltaController::class, 'store'])->name('store');
        Route::get('/relatorio', [FaltaController::class, 'relatorioProfessor'])->name('relatorio');
    });
    
    // Relatórios do Professor
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/minhas-turmas', [ProfessorController::class, 'relatorioMinhasTurmas'])->name('turmas');
        Route::get('/frequencia-turma/{turma}', [FaltaController::class, 'relatorioFrequenciaTurma'])->name('frequencia');
    });
});

// ========================================
// ROTAS PARA ALUNOS (E ADMINISTRADORES)
// ========================================

Route::middleware(['auth', 'check.aluno'])->prefix('aluno')->name('aluno.')->group(function () {
    // Dashboard do Aluno
    Route::get('/dashboard', [AlunoController::class, 'dashboardAluno'])->name('dashboard');
    
    // Boletim Individual (com verificação adicional de propriedade)
    Route::get('/boletim', [AlunoController::class, 'meuBoletim'])->name('boletim');
    Route::get('/boletim/{aluno}', [AlunoController::class, 'boletim'])
        ->name('boletim.show')
        ->middleware('check.boletim.ownership');
    
    // Consulta de Faltas
    Route::prefix('faltas')->name('faltas.')->group(function () {
        Route::get('/', [FaltaController::class, 'minhasFaltas'])->name('index');
        Route::get('/justificar/{falta}', [FaltaController::class, 'justificar'])
            ->name('justificar')
            ->middleware('check.falta.ownership');
        Route::post('/justificar/{falta}', [FaltaController::class, 'processarJustificativa'])->name('processar-justificativa');
        Route::delete('/justificar/{falta}', [FaltaController::class, 'removerJustificativa'])->name('remover-justificativa');
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
