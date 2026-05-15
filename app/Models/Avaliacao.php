<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Avaliacao\NotaCalculator;
use App\Enums\SituacaoAvaliacao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Avaliacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'avaliacoes';

    protected $fillable = [
        'aluno_id',
        'disciplina_id',
        'av1',
        'av2',
        'av3',
        'av4',
        'substitutiva',
        'recuperacao_final',
        'nota_final',
        'situacao',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'av1' => 'decimal:2',
        'av2' => 'decimal:2',
        'av3' => 'decimal:2',
        'av4' => 'decimal:2',
        'substitutiva' => 'decimal:2',
        'recuperacao_final' => 'decimal:2',
        'nota_final' => 'decimal:2',
        'situacao' => SituacaoAvaliacao::class,
    ];

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    /**
     * Recalcula a nota final delegando para NotaCalculator e persiste.
     *
     * A lógica de cálculo vive em App\Domain\Avaliacao\NotaCalculator.
     */
    public function calcularNotaFinal(?NotaCalculator $calculator = null): void
    {
        $calculator ??= app(NotaCalculator::class);

        $resultado = $calculator->calcular(
            av1: $this->av1,
            av2: $this->av2,
            av3: $this->av3,
            av4: $this->av4,
            substitutiva: $this->substitutiva,
            recuperacaoFinal: $this->recuperacao_final,
        );

        $this->nota_final = $resultado->media;
        $this->situacao = $resultado->situacao;
        $this->save();
    }

    public function isAprovado(): bool
    {
        return $this->situacao === SituacaoAvaliacao::Aprovado;
    }

    public function isReprovado(): bool
    {
        return $this->situacao === SituacaoAvaliacao::Reprovado;
    }

    public function isEmAndamento(): bool
    {
        return $this->situacao === SituacaoAvaliacao::EmAndamento;
    }
}
