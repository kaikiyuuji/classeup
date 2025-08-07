<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avaliacao extends Model
{
    use HasFactory;
    
    protected $table = 'avaliacoes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'aluno_id',
        'disciplina_id',
        'av1',
        'av2',
        'av3',
        'av4',
        'substitutiva',
        'recuperacao_final',
        'nota_final',
        'situacao',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'av1' => 'decimal:2',
        'av2' => 'decimal:2',
        'av3' => 'decimal:2',
        'av4' => 'decimal:2',
        'substitutiva' => 'decimal:2',
        'recuperacao_final' => 'decimal:2',
        'nota_final' => 'decimal:2',
    ];

    /**
     * Relacionamento com Aluno
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    /**
     * Relacionamento com Disciplina
     */
    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    /**
     * Calcula a nota final baseada nas regras de negócio
     */
    public function calcularNotaFinal(): void
    {
        $notas = $this->obterNotasParaCalculo();
        $this->nota_final = $this->calcularMedia($notas);
        $this->situacao = $this->determinarSituacao();
        $this->save();
    }

    /**
     * Obtém as notas para cálculo considerando substitutiva e recuperação
     */
    private function obterNotasParaCalculo(): array
    {
        $notas = [
            $this->av1 ?? 0,
            $this->av2 ?? 0,
            $this->av3 ?? 0,
            $this->av4 ?? 0,
        ];

        if ($this->temSubstitutiva()) {
            $notas = $this->aplicarSubstitutiva($notas);
        }

        if ($this->temRecuperacaoFinal()) {
            $notas = $this->aplicarRecuperacaoFinal($notas);
        }

        return $notas;
    }

    /**
     * Verifica se há nota substitutiva
     */
    private function temSubstitutiva(): bool
    {
        return !is_null($this->substitutiva);
    }

    /**
     * Verifica se há recuperação final
     */
    private function temRecuperacaoFinal(): bool
    {
        return !is_null($this->recuperacao_final);
    }

    /**
     * Aplica a nota substitutiva substituindo a menor nota
     */
    private function aplicarSubstitutiva(array $notas): array
    {
        $indiceMenorNota = $this->encontrarIndiceMenorNota($notas);
        $notas[$indiceMenorNota] = $this->substitutiva;
        return $notas;
    }

    /**
     * Aplica a recuperação final substituindo a menor nota
     */
    private function aplicarRecuperacaoFinal(array $notas): array
    {
        $indiceMenorNota = $this->encontrarIndiceMenorNota($notas);
        $notas[$indiceMenorNota] = $this->recuperacao_final;
        return $notas;
    }

    /**
     * Encontra o índice da menor nota no array
     */
    private function encontrarIndiceMenorNota(array $notas): int
    {
        return array_search(min($notas), $notas);
    }

    /**
     * Calcula a média das notas
     */
    private function calcularMedia(array $notas): float
    {
        return array_sum($notas) / count($notas);
    }

    /**
     * Determina a situação do aluno baseada na nota final
     */
    private function determinarSituacao(): string
    {
        if ($this->nota_final >= 6.0) {
            return 'aprovado';
        }
        
        return 'reprovado';
    }

    /**
     * Verifica se o aluno está aprovado
     */
    public function isAprovado(): bool
    {
        return $this->situacao === 'aprovado';
    }

    /**
     * Verifica se o aluno está reprovado
     */
    public function isReprovado(): bool
    {
        return $this->situacao === 'reprovado';
    }

    /**
     * Verifica se a avaliação está em andamento
     */
    public function isEmAndamento(): bool
    {
        return $this->situacao === 'em_andamento';
    }
}
