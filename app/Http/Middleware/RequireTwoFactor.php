<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Força admin sem 2FA confirmado a passar pela tela de setup antes de
 * acessar qualquer outra rota.
 *
 * Quando AppConfig 2FA_REQUIRED_FOR_ADMIN=false, é no-op (útil em testes
 * e durante rollout inicial em produção).
 */
class RequireTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isAdmin()) {
            return $next($request);
        }

        if (! config('auth.two_factor_required_for_admin', false)) {
            return $next($request);
        }

        if ($user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        // Permite acessar a tela de setup, logout e profile.
        $rotasPermitidas = ['two-factor.setup', 'two-factor.confirm', 'logout', 'profile.edit'];
        if (in_array($request->route()?->getName(), $rotasPermitidas, strict: true)) {
            return $next($request);
        }

        return redirect()->route('two-factor.setup');
    }
}
