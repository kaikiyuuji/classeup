<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Professor extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'professores';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'cpf',
        'data_nascimento',
        'telefone',
        'endereco',
        'especialidade',
        'formacao',
        'foto_perfil',
        'ativo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_nascimento' => 'date',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento many-to-many com Disciplina através da tabela pivot direta
     */
    public function disciplinas(): BelongsToMany
    {
        return $this->belongsToMany(Disciplina::class, 'professor_disciplina')
                    ->withTimestamps();
    }

    /**
     * Relacionamento many-to-many com Disciplina através da tabela pivot com turma
     */
    public function disciplinasComTurma(): BelongsToMany
    {
        return $this->belongsToMany(Disciplina::class, 'professor_disciplina_turma')
                    ->withPivot('turma_id')
                    ->withTimestamps();
    }

    /**
     * Relacionamento many-to-many com Turma através da tabela pivot
     */
    public function turmas(): BelongsToMany
    {
        return $this->belongsToMany(Turma::class, 'professor_disciplina_turma')
                    ->withPivot('disciplina_id')
                    ->withTimestamps();
    }

    /**
     * Get the URL for the professor's profile photo.
     *
     * @return string|null
     */
    public function getFotoPerfilUrlAttribute(): ?string
    {
        return $this->foto_perfil ? asset('storage/' . $this->foto_perfil) : null;
    }

    /**
     * Scope a query to only include active professors.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope a query to only include inactive professors.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInativo($query)
    {
        return $query->where('ativo', false);
    }

    /**
     * Relacionamento com User
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Relacionamento one-to-many com Chamada
     */
    public function chamadas()
    {
        return $this->hasMany(Chamada::class);
    }
}
