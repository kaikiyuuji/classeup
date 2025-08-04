<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
