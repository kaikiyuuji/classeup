<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Preenche colunas de auditoria (created_by, updated_by, deleted_by)
 * automaticamente baseando-se no usuário autenticado.
 *
 * Models que querem auditoria devem ser registrados no AppServiceProvider:
 * `Aluno::observe(AuditObserver::class)`.
 *
 * Se não houver usuário autenticado (seeders, console commands), as
 * colunas ficam NULL — comportamento aceitável.
 */
class AuditObserver
{
    public function creating(Model $model): void
    {
        $userId = $this->currentUserId();
        if ($userId !== null) {
            $model->setAttribute('created_by', $userId);
            $model->setAttribute('updated_by', $userId);
        }
    }

    public function updating(Model $model): void
    {
        $userId = $this->currentUserId();
        if ($userId !== null) {
            $model->setAttribute('updated_by', $userId);
        }
    }

    public function deleting(Model $model): void
    {
        $userId = $this->currentUserId();
        if ($userId !== null && in_array('deleted_by', $model->getFillable(), strict: true)) {
            $model->setAttribute('deleted_by', $userId);
            $model->saveQuietly();
        }
    }

    private function currentUserId(): ?int
    {
        $user = Auth::user();

        return $user?->getKey();
    }
}
