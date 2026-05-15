<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Disciplina;
use App\Models\Falta;
use App\Models\Professor;
use App\Models\Turma;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        foreach ([Aluno::class, Avaliacao::class, Disciplina::class, Falta::class, Professor::class, Turma::class] as $model) {
            $model::observe(AuditObserver::class);
        }
    }
}
