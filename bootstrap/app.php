<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            RateLimiter::for('writes', function (Request $request) {
                return $request->user()
                    ? Limit::perMinute(60)->by((string) $request->user()->id)
                    : Limit::perMinute(10)->by((string) $request->ip());
            });

            RateLimiter::for('global', function (Request $request) {
                return Limit::perMinute(120)->by(
                    $request->user()
                        ? (string) $request->user()->id
                        : (string) $request->ip()
                );
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
