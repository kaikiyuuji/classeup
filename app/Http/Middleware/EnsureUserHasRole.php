<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Garante que o usuário autenticado tem um dos papéis (Role) permitidos.
 *
 * Uso em rotas: ->middleware('role:admin,professor')
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            throw new AccessDeniedHttpException('Autenticação requerida.');
        }

        $rolesPermitidos = array_map(static fn (string $r) => Role::from($r), $roles);

        if (! in_array($user->role, $rolesPermitidos, strict: true)) {
            throw new AccessDeniedHttpException('Você não tem permissão para acessar este recurso.');
        }

        return $next($request);
    }
}
