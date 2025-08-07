<?php

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\DisciplinaController;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('alunos', AlunoController::class);
    
    
    Route::resource('professores', ProfessorController::class)->parameters([
        'professores' => 'professor'
    ]);
    Route::post('professores/{professor}/vincular-disciplina', [ProfessorController::class, 'vincularDisciplina'])->name('professores.vincular-disciplina');
    Route::delete('professores/{professor}/desvincular-disciplina', [ProfessorController::class, 'desvincularDisciplina'])->name('professores.desvincular-disciplina');
    Route::resource('disciplinas', DisciplinaController::class);
    Route::resource('turmas', TurmaController::class);
    Route::post('turmas/{turma}/vincular-alunos', [TurmaController::class, 'vincularAlunos'])->name('turmas.vincular-alunos');
    Route::delete('turmas/{turma}/alunos/{aluno}', [TurmaController::class, 'desvincularAluno'])->name('turmas.desvincular-aluno');
    Route::post('turmas/{turma}/vincular-professor', [TurmaController::class, 'vincularProfessor'])->name('turmas.vincular-professor');
    Route::delete('turmas/{turma}/desvincular-professor', [TurmaController::class, 'desvincularProfessor'])->name('turmas.desvincular-professor');
    

});

// Rotas para avaliações dos alunos
Route::middleware('auth')->group(function () {
    Route::get('/alunos/{aluno}/boletim', [AlunoController::class, 'boletim'])->name('alunos.boletim');
    Route::put('/alunos/{aluno}/avaliacoes/{avaliacao}', [AlunoController::class, 'atualizarAvaliacao'])->name('alunos.avaliacoes.update');
});

require __DIR__.'/auth.php';
