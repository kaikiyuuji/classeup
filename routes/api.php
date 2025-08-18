<?php

use App\Http\Controllers\Api\AlunoApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ========================================
// ROTAS DE API PARA ADMINISTRADORES
// ========================================

Route::middleware(['auth', 'check.admin'])->prefix('admin')->name('api.admin.')->group(function () {
    // API de Alunos
    Route::prefix('alunos')->name('alunos.')->group(function () {
        Route::get('{aluno}/presencas', [AlunoApiController::class, 'presencasAluno'])->name('presencas');
        Route::get('{aluno}/disciplinas', [AlunoApiController::class, 'disciplinasAluno'])->name('disciplinas');
        Route::get('{aluno}/estatisticas', [AlunoApiController::class, 'estatisticas'])->name('estatisticas');
    });
});

// ========================================
// ROTAS DE API PARA PROFESSORES
// ========================================

Route::middleware(['auth', 'check.professor'])->prefix('professor')->name('api.professor.')->group(function () {
    // API de Alunos (visualização apenas)
    Route::prefix('alunos')->name('alunos.')->group(function () {
        Route::get('{aluno}/presencas', [AlunoApiController::class, 'presencasAluno'])->name('presencas');
        Route::get('{aluno}/disciplinas', [AlunoApiController::class, 'disciplinasAluno'])->name('disciplinas');
        Route::get('{aluno}/estatisticas', [AlunoApiController::class, 'estatisticas'])->name('estatisticas');
    });
});