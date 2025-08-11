<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Chamada extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'matricula',
        'disciplina_id',
        'professor_id',
        'data_chamada',
        'status',
        'justificada',
        'observacoes'
    ];

    protected $casts = [
        'data_chamada' => 'date',
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
    public function marcarPresenca(): void
    {
        $this->update(['status' => 'presente']);
    }

    public function marcarFalta(): void
    {
        $this->update(['status' => 'falta']);
    }

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

    public function estaPresente(): bool
    {
        return $this->status === 'presente';
    }

    public function estauFalta(): bool
    {
        return $this->status === 'falta';
    }

    public function estaJustificada(): bool
    {
        return $this->justificada;
    }

    public function foiRegistradaHoje(): bool
    {
        return $this->data_chamada->isToday();
    }

    public function podeSerEditada(): bool
    {
        return $this->data_chamada->gte(now()->subDays(7));
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
        return $query->whereBetween('data_chamada', [$dataInicio, $dataFim]);
    }

    public function scopeJustificadas($query)
    {
        return $query->where('justificada', true);
    }

    public function scopeNaoJustificadas($query)
    {
        return $query->where('justificada', false);
    }

    public function scopePresencas($query)
    {
        return $query->where('status', 'presente');
    }

    public function scopeFaltas($query)
    {
        return $query->where('status', 'falta');
    }
}
