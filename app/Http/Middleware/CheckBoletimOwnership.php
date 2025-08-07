<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBoletimOwnership
{
    /**
     * Verifica se o usuário pode acessar o boletim solicitado.
     * Admin pode acessar qualquer boletim.
     * Aluno só pode acessar seu próprio boletim.
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
        
        $aluno = $request->route('aluno');
        
        // Admin pode ver qualquer boletim
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        // Aluno só pode ver seu próprio boletim
        if ($user->isAluno() && $user->aluno_id == $aluno->id) {
            return $next($request);
        }
        
        abort(403, 'Você não tem permissão para acessar este boletim.');
    }
}
