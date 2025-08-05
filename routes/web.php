<?php

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\MatriculaController;
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
    Route::resource('disciplinas', DisciplinaController::class);
    Route::resource('turmas', TurmaController::class);
    
    // Rotas de Matrículas
    Route::resource('matriculas', MatriculaController::class);
    Route::get('turmas/{turma}/matriculas', [MatriculaController::class, 'porTurma'])->name('matriculas.por-turma');
    Route::get('alunos/{aluno}/matriculas', [MatriculaController::class, 'porAluno'])->name('matriculas.por-aluno');
});

require __DIR__.'/auth.php';
