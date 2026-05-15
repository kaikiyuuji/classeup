<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusSolicitacaoExclusao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitacaoExclusao extends Model
{
    protected $table = 'solicitacoes_exclusao';

    protected $fillable = [
        'aluno_id',
        'solicitante_user_id',
        'status',
        'motivo',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'status' => StatusSolicitacaoExclusao::class,
        'decided_at' => 'datetime',
    ];

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_user_id');
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function isPendente(): bool
    {
        return $this->status === StatusSolicitacaoExclusao::Pendente;
    }
}
