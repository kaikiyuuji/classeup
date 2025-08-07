<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Falta extends Model
{
    use HasFactory;
    protected $fillable = [
        'matricula',
        'disciplina_id',
        'professor_id',
        'data_falta',
        'justificada',
        'observacoes'
    ];

    protected $casts = [
        'data_falta' => 'date',
        'justificada' => 'boolean'
    ];

    // Relacionamentos
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, 'matricula', 'numero_matricula');
    }

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class);
    }

    // Métodos de comportamento seguindo Object Calisthenics
    public function justificar(?string $observacao = null): void
    {
        $this->update([
            'justificada' => true,
            'observacoes' => $observacao
        ]);
    }

    public function removerJustificativa(): void
    {
        $this->update([
            'justificada' => false,
            'observacoes' => null
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

    // Scopes para consultas otimizadas
    public function scopePorAluno($query, string $matricula)
    {
        return $query->where('matricula', $matricula);
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
