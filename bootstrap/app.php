<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registrar middlewares de autorização personalizados
        $middleware->alias([
            'check.user.type' => \App\Http\Middleware\CheckUserType::class,
            'check.admin' => \App\Http\Middleware\CheckAdmin::class,
            'check.professor' => \App\Http\Middleware\CheckProfessor::class,
            'check.aluno' => \App\Http\Middleware\CheckAluno::class,
            'check.boletim.ownership' => \App\Http\Middleware\CheckBoletimOwnership::class,
            'check.falta.ownership' => \App\Http\Middleware\CheckFaltaOwnership::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
