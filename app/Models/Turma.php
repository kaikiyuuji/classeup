<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turma extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'ano_letivo',
        'serie',
        'turno',
        'capacidade_maxima',
        'ativo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ano_letivo' => 'integer',
        'capacidade_maxima' => 'integer',
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento many-to-many com Professor através da tabela pivot
     */
    public function professores(): BelongsToMany
    {
        return $this->belongsToMany(Professor::class, 'professor_disciplina_turma')
                    ->withPivot('disciplina_id')
                    ->withTimestamps();
    }

    /**
     * Relacionamento many-to-many com Disciplina através da tabela pivot
     */
    public function disciplinas(): BelongsToMany
    {
        return $this->belongsToMany(Disciplina::class, 'professor_disciplina_turma')
                    ->withPivot('professor_id')
                    ->withTimestamps();
    }

    /**
     * Relacionamento many-to-many com Aluno através da tabela Matricula
     */
    public function alunos(): BelongsToMany
    {
        return $this->belongsToMany(Aluno::class, 'matriculas')
                    ->withPivot('data_matricula', 'status')
                    ->withTimestamps();
    }

    /**
     * Relacionamento one-to-many com Matricula
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }
}
