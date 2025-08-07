<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$types
     */
    public function handle(Request $request, Closure $next, ...$types): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!in_array($user->tipo_usuario, $types)) {
            return $this->redirectToDashboard($user->tipo_usuario)
                ->with('error', 'Você não tem permissão para acessar esta área.');
        }

        return $next($request);
    }

    /**
     * Redireciona para o dashboard apropriado baseado no tipo de usuário
     */
    private function redirectToDashboard(string $tipoUsuario)
    {
        return match($tipoUsuario) {
            'admin' => redirect()->route('dashboard'),
            'professor' => redirect()->route('dashboard'),
            'aluno' => redirect()->route('dashboard'),
            default => redirect()->route('dashboard')
        };
    }
}
