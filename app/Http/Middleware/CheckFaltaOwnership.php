<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFaltaOwnership
{
    /**
     * Verifica se o usuário pode justificar a falta solicitada.
     * Admin pode justificar qualquer falta.
     * Aluno só pode justificar suas próprias faltas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $faltaId = $request->route('falta');
        $falta = \App\Models\Falta::findOrFail($faltaId);
        
        // Admin pode justificar qualquer falta
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        // Aluno só pode justificar suas próprias faltas
        if ($user->isAluno() && $user->aluno && $user->aluno->numero_matricula == $falta->matricula) {
            return $next($request);
        }
        
        abort(403, 'Você não tem permissão para justificar esta falta.');
    }
}
