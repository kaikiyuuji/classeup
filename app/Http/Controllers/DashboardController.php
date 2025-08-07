<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Falta;

class DashboardController extends Controller
{
    /**
     * Redireciona para o dashboard apropriado baseado no tipo de usuário
     */
    public function index(): RedirectResponse
    {
        $tipoUsuario = auth()->user()->tipo_usuario;
        
        return match ($tipoUsuario) {
            'admin' => redirect()->route('dashboard.admin'),
            'professor' => redirect()->route('dashboard.professor'),
            'aluno' => redirect()->route('dashboard.aluno'),
            default => abort(403, 'Tipo de usuário não reconhecido')
        };
    }

    /**
     * Dashboard para administradores
     */
    public function admin(): View
    {
        // Dados específicos para o dashboard administrativo
        $totalAlunos = \App\Models\Aluno::count();
        $totalProfessores = \App\Models\Professor::count();
        $totalTurmas = \App\Models\Turma::count();
        $totalDisciplinas = \App\Models\Disciplina::count();
        
        return view('admin.dashboard', compact(
            'totalAlunos',
            'totalProfessores', 
            'totalTurmas',
            'totalDisciplinas'
        ));
    }

    /**
     * Dashboard para professores
     */
    public function professor(): View
    {
        $professor = auth()->user()->professor;
        
        if (!$professor) {
            abort(403, 'Professor não encontrado');
        }
        
        // Dados específicos para o dashboard do professor
        $turmas = $professor->turmas()->with('alunos')->get();
        $disciplinas = $professor->disciplinas;
        $totalAlunos = $turmas->sum(fn($turma) => $turma->alunos->count());
        
        return view('professor.dashboard', compact(
            'professor',
            'turmas',
            'disciplinas',
            'totalAlunos'
        ));
    }

    /**
     * Dashboard para alunos
     */
    public function aluno(): View
    {
        $aluno = auth()->user()->aluno;
        
        if (!$aluno) {
            abort(403, 'Aluno não encontrado');
        }
        
        // Dados específicos para o dashboard do aluno
        $turma = $aluno->turma;
        $avaliacoes = $aluno->avaliacoes()->with('disciplina')->latest()->take(5)->get();
        $faltas = Falta::porAluno($aluno->numero_matricula)
            ->with(['disciplina', 'professor'])
            ->latest('data_falta')
            ->limit(5)
            ->get();
        
        return view('aluno.dashboard', compact(
            'aluno',
            'turma',
            'avaliacoes',
            'faltas'
        ));
    }
}
