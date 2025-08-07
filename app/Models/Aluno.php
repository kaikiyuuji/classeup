<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'numero_matricula',
        'data_matricula',
        'status_matricula',
        'nome',
        'email',
        'cpf',
        'data_nascimento',
        'telefone',
        'endereco',
        'foto_perfil',
        'turma_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_matricula' => 'date',
        'data_nascimento' => 'date',
    ];

    /**
     * Relacionamento many-to-one com Turma
     * Um aluno pertence a uma única turma
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
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

    /**
     * Gera um número de matrícula único baseado no ano
     */
    public static function gerarNumeroMatricula(int $ano = null): string
    {
        $ano = $ano ?? date('Y');
        
        // Busca o último número de matrícula do ano
        $ultimaMatricula = static::where('numero_matricula', 'like', $ano . '%')
            ->orderBy('numero_matricula', 'desc')
            ->first();
        
        if ($ultimaMatricula) {
            // Extrai o número sequencial e incrementa
            $ultimoNumero = (int) substr($ultimaMatricula->numero_matricula, 4);
            $proximoNumero = $ultimoNumero + 1;
        } else {
            // Primeiro aluno do ano
            $proximoNumero = 1;
        }
        
        return $ano . str_pad($proximoNumero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Verifica se a matrícula está ativa
     */
    public function isMatriculaAtiva(): bool
    {
        return $this->status_matricula === 'ativa';
    }

    /**
     * Ativa a matrícula do aluno
     */
    public function ativarMatricula(): void
    {
        $this->update([
            'status_matricula' => 'ativa'
        ]);
    }

    /**
     * Inativa a matrícula do aluno
     */
    public function inativarMatricula(): void
    {
        $this->update([
            'status_matricula' => 'inativa'
        ]);
    }

    /**
     * Verifica se o aluno está ativo (baseado no status da matrícula)
     */
    public function isAtivo(): bool
    {
        return $this->isMatriculaAtiva();
    }

    /**
     * Relacionamento one-to-many com Avaliacao
     * Um aluno tem muitas avaliações
     */
    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }

    /**
     * Relacionamento com User
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
