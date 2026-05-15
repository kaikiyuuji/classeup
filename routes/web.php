<?php

declare(strict_types=1);

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\FaltaController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TurmaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil próprio: qualquer usuário autenticado
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Mutações admin (definidas ANTES das rotas com {param} para evitar
// que /alunos/create caia em /alunos/{aluno}).
Route::middleware(['auth', 'role:admin', 'throttle:writes'])->group(function () {
    // Alunos
    Route::get('alunos/create', [AlunoController::class, 'create'])->name('alunos.create');
    Route::post('alunos', [AlunoController::class, 'store'])->name('alunos.store');
    Route::get('alunos/{aluno}/edit', [AlunoController::class, 'edit'])->name('alunos.edit');
    Route::put('alunos/{aluno}', [AlunoController::class, 'update'])->name('alunos.update');
    Route::patch('alunos/{aluno}', [AlunoController::class, 'update']);
    Route::delete('alunos/{aluno}', [AlunoController::class, 'destroy'])->name('alunos.destroy');

    // Professores
    Route::get('professores/create', [ProfessorController::class, 'create'])->name('professores.create');
    Route::post('professores', [ProfessorController::class, 'store'])->name('professores.store');
    Route::get('professores/{professor}/edit', [ProfessorController::class, 'edit'])->name('professores.edit');
    Route::put('professores/{professor}', [ProfessorController::class, 'update'])->name('professores.update');
    Route::patch('professores/{professor}', [ProfessorController::class, 'update']);
    Route::delete('professores/{professor}', [ProfessorController::class, 'destroy'])->name('professores.destroy');
    Route::post('professores/{professor}/vincular-disciplina', [ProfessorController::class, 'vincularDisciplina'])->name('professores.vincular-disciplina');
    Route::delete('professores/{professor}/desvincular-disciplina', [ProfessorController::class, 'desvincularDisciplina'])->name('professores.desvincular-disciplina');

    // Disciplinas
    Route::get('disciplinas/create', [DisciplinaController::class, 'create'])->name('disciplinas.create');
    Route::post('disciplinas', [DisciplinaController::class, 'store'])->name('disciplinas.store');
    Route::get('disciplinas/{disciplina}/edit', [DisciplinaController::class, 'edit'])->name('disciplinas.edit');
    Route::put('disciplinas/{disciplina}', [DisciplinaController::class, 'update'])->name('disciplinas.update');
    Route::patch('disciplinas/{disciplina}', [DisciplinaController::class, 'update']);
    Route::delete('disciplinas/{disciplina}', [DisciplinaController::class, 'destroy'])->name('disciplinas.destroy');

    // Turmas
    Route::get('turmas/create', [TurmaController::class, 'create'])->name('turmas.create');
    Route::post('turmas', [TurmaController::class, 'store'])->name('turmas.store');
    Route::get('turmas/{turma}/edit', [TurmaController::class, 'edit'])->name('turmas.edit');
    Route::put('turmas/{turma}', [TurmaController::class, 'update'])->name('turmas.update');
    Route::patch('turmas/{turma}', [TurmaController::class, 'update']);
    Route::delete('turmas/{turma}', [TurmaController::class, 'destroy'])->name('turmas.destroy');
    Route::post('turmas/{turma}/vincular-alunos', [TurmaController::class, 'vincularAlunos'])->name('turmas.vincular-alunos');
    Route::delete('turmas/{turma}/alunos/{aluno}', [TurmaController::class, 'desvincularAluno'])->name('turmas.desvincular-aluno');
    Route::post('turmas/{turma}/vincular-professor', [TurmaController::class, 'vincularProfessor'])->name('turmas.vincular-professor');
    Route::delete('turmas/{turma}/desvincular-professor', [TurmaController::class, 'desvincularProfessor'])->name('turmas.desvincular-professor');
});

// Leitura admin+professor.
Route::middleware(['auth', 'role:admin,professor'])->group(function () {
    Route::get('alunos', [AlunoController::class, 'index'])->name('alunos.index');
    Route::get('alunos/{aluno}', [AlunoController::class, 'show'])->name('alunos.show');

    Route::get('professores', [ProfessorController::class, 'index'])->name('professores.index');
    Route::get('professores/{professor}', [ProfessorController::class, 'show'])->name('professores.show');

    Route::get('disciplinas', [DisciplinaController::class, 'index'])->name('disciplinas.index');
    Route::get('disciplinas/{disciplina}', [DisciplinaController::class, 'show'])->name('disciplinas.show');

    Route::get('turmas', [TurmaController::class, 'index'])->name('turmas.index');
    Route::get('turmas/{turma}', [TurmaController::class, 'show'])->name('turmas.show');

    Route::prefix('faltas')->name('faltas.')->group(function () {
        Route::get('/', [FaltaController::class, 'index'])->name('index');
        Route::get('/chamada/{turma}/{disciplina}', [FaltaController::class, 'chamada'])->name('chamada');
        Route::post('/chamada', [FaltaController::class, 'store'])->middleware('throttle:writes')->name('store');
        Route::get('/relatorio-aluno', [FaltaController::class, 'relatorioAluno'])->name('relatorio-aluno');
        Route::get('/justificar/{falta}', [FaltaController::class, 'justificar'])->name('justificar');
        Route::post('/justificar/{falta}', [FaltaController::class, 'processarJustificativa'])->middleware('throttle:writes')->name('processar-justificativa');
        Route::delete('/justificar/{falta}', [FaltaController::class, 'removerJustificativa'])->middleware('throttle:writes')->name('remover-justificativa');
    });
});

// Boletim: admin/professor têm acesso amplo; aluno só ao próprio.
// Policy `view` em AlunoPolicy controla isso.
Route::middleware('auth')->group(function () {
    Route::get('/alunos/{aluno}/boletim', [AlunoController::class, 'boletim'])
        ->middleware('can:view,aluno')
        ->name('alunos.boletim');

    Route::put('/alunos/{aluno}/avaliacoes/{avaliacao}', [AlunoController::class, 'atualizarAvaliacao'])
        ->middleware(['can:update,avaliacao', 'throttle:writes'])
        ->name('alunos.avaliacoes.update');
});

require __DIR__.'/auth.php';
