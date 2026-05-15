<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Falta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'aluno_id',
        'matricula',
        'disciplina_id',
        'professor_id',
        'data_falta',
        'justificada',
        'observacoes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'data_falta' => 'date',
        'justificada' => 'boolean',
    ];

    /**
     * Relacionamento canônico via FK aluno_id. Faz fallback para resolver
     * via numero_matricula caso a FK não esteja preenchida (linhas legacy
     * que ainda não passaram pelo backfill da migration).
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
    }

    /**
     * Resolve o aluno mesmo quando aluno_id é null, usando matricula.
     */
    public function getAlunoResolvidoAttribute(): ?Aluno
    {
        if ($this->aluno_id !== null && $this->aluno !== null) {
            return $this->aluno;
        }

        if ($this->matricula !== null) {
            return Aluno::where('numero_matricula', $this->matricula)->first();
        }

        return null;
    }

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class);
    }

    public function justificar(?string $observacao = null): void
    {
        $this->update([
            'justificada' => true,
            'observacoes' => $observacao,
        ]);
    }

    public function removerJustificativa(): void
    {
        $this->update([
            'justificada' => false,
            'observacoes' => null,
        ]);
    }

    public function estaJustificada(): bool
    {
        return $this->justificada;
    }

    public function foiRegistradaHoje(): bool
    {
        return $this->data_falta->isToday();
    }

    public function podeSerEditada(): bool
    {
        return $this->data_falta->gte(now()->subDays(7));
    }

    /**
     * Aceita ID numérico do aluno ou string de matrícula.
     */
    public function scopePorAluno($query, int|string $alunoOuMatricula)
    {
        if (is_int($alunoOuMatricula)) {
            return $query->where('aluno_id', $alunoOuMatricula);
        }

        return $query->where('matricula', $alunoOuMatricula);
    }

    public function scopePorDisciplina($query, int $disciplinaId)
    {
        return $query->where('disciplina_id', $disciplinaId);
    }

    public function scopePorProfessor($query, int $professorId)
    {
        return $query->where('professor_id', $professorId);
    }

    public function scopePorPeriodo($query, Carbon $dataInicio, Carbon $dataFim)
    {
        return $query->whereBetween('data_falta', [$dataInicio, $dataFim]);
    }

    public function scopeJustificadas($query)
    {
        return $query->where('justificada', true);
    }

    public function scopeNaoJustificadas($query)
    {
        return $query->where('justificada', false);
    }
}
