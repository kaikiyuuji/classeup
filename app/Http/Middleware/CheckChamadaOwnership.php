<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckChamadaOwnership
{
    /**
     * Verifica se o usuário pode justificar a chamada solicitada.
     * Admin pode justificar qualquer chamada.
     * Aluno só pode justificar suas próprias faltas.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            abort(401, 'Usuário não autenticado.');
        }
        
        // Obtém o ID da chamada da rota
        $chamadaId = $request->route('chamada');
        $chamada = \App\Models\Chamada::findOrFail($chamadaId);
        
        // Admin pode justificar qualquer chamada
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        // Aluno só pode justificar suas próprias faltas
        if ($user->isAluno() && $user->aluno && $user->aluno->numero_matricula == $chamada->matricula) {
            return $next($request);
        }
        
        abort(403, 'Você não tem permissão para justificar esta chamada.');
    }
}
