<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
