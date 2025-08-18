<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Disciplina extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'carga_horaria',
        'ativo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ativo' => 'boolean',
        'carga_horaria' => 'integer',
    ];

    /**
     * Relacionamento many-to-many com Professor através da tabela pivot direta
     */
    public function professores(): BelongsToMany
    {
        return $this->belongsToMany(Professor::class, 'professor_disciplina')
                    ->withTimestamps();
    }

    /**
     * Relacionamento many-to-many com Professor através da tabela pivot com turma
     */
    public function professoresComTurma(): BelongsToMany
    {
        return $this->belongsToMany(Professor::class, 'professor_disciplina_turma')
                    ->withPivot('turma_id')
                    ->withTimestamps();
    }

    /**
     * Relacionamento many-to-many com Turma através da tabela pivot
     */
    public function turmas(): BelongsToMany
    {
        return $this->belongsToMany(Turma::class, 'professor_disciplina_turma')
                    ->withPivot('professor_id')
                    ->withTimestamps();
    }

    /**
     * Relacionamento one-to-many com Avaliacao
     * Uma disciplina tem muitas avaliações
     */
    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }

    /**
     * Relacionamento one-to-many com Chamada
     * Uma disciplina tem muitas chamadas
     */
    public function chamadas(): HasMany
    {
        return $this->hasMany(Chamada::class);
    }

    /**
     * Mutator para o campo 'ativo'
     * Garante que o valor seja sempre tratado como boolean
     */
    protected function ativo(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $this->converterParaBoolean($value)
        );
    }

    /**
     * Scope para filtrar disciplinas por busca textual
     */
    public function scopeComBusca(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nome', 'like', "%{$search}%")
              ->orWhere('codigo', 'like', "%{$search}%")
              ->orWhere('descricao', 'like', "%{$search}%");
        });
    }

    /**
     * Scope para filtrar disciplinas por status
     */
    public function scopeComStatus(Builder $query, ?string $status): Builder
    {
        if (empty($status)) {
            return $query;
        }

        return match ($status) {
            'ativo' => $query->where('ativo', true),
            'inativo' => $query->where('ativo', false),
            default => $query
        };
    }

    /**
     * Scope para ordenação com validação de campos permitidos
     */
    public function scopeComOrdenacao(Builder $query, ?string $campo = 'nome', ?string $direcao = 'asc'): Builder
    {
        $camposPermitidos = ['nome', 'descricao', 'carga_horaria', 'ativo'];
        $direcoesPermitidas = ['asc', 'desc'];

        $campoValidado = in_array($campo, $camposPermitidos) ? $campo : 'nome';
        $direcaoValidada = in_array($direcao, $direcoesPermitidas) ? $direcao : 'asc';

        return $query->orderBy($campoValidado, $direcaoValidada);
    }

    /**
     * Scope para disciplinas ativas
     */
    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    /**
     * Verifica se a disciplina possui relacionamentos que impedem exclusão
     */
    public function possuiRelacionamentosAtivos(): bool
    {
        return $this->professores()->exists();
    }

    /**
     * Converte valor para boolean de forma consistente
     */
    private function converterParaBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'on', 'yes']);
        }

        return (bool) $value;
    }
}
