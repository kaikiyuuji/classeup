<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aluno extends Model
{
    use HasFactory;
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
    ];

    /**
     * Relacionamento many-to-many com Turma através da tabela Matricula
     */
    public function turmas(): BelongsToMany
    {
        return $this->belongsToMany(Turma::class, 'matriculas')
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

    /**
     * Get the full URL for the profile photo.
     */
    public function getFotoPerfilUrlAttribute(): ?string
    {
        if (!$this->foto_perfil) {
            return null;
        }

        // If it's already a full URL, return as is
        if (filter_var($this->foto_perfil, FILTER_VALIDATE_URL)) {
            return $this->foto_perfil;
        }

        // Otherwise, generate storage URL
        return asset('storage/' . $this->foto_perfil);
    }
}
