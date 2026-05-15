<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusSolicitacaoExclusao;
use App\Models\Aluno;
use App\Models\SolicitacaoExclusao;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Atende requisitos LGPD (Lei 13.709/18):
 * - Direito de portabilidade: exportarDadosDoAluno() devolve JSON com tudo.
 * - Direito de exclusão: solicitarExclusao() + efetivarExclusao() (4-eyes).
 *
 * Após efetivada, a entidade é soft-deleted e os campos PII são anonimizados
 * para que histórico (faltas/notas) permaneça mas sem identificar a pessoa.
 */
class LgpdService
{
    /**
     * @return array<string, mixed>
     */
    public function exportarDadosDoAluno(Aluno $aluno): array
    {
        $aluno->load(['turma', 'avaliacoes.disciplina']);

        return [
            'gerado_em' => CarbonImmutable::now()->toIso8601String(),
            'aluno' => [
                'id' => $aluno->id,
                'numero_matricula' => $aluno->numero_matricula,
                'nome' => $aluno->nome,
                'email' => $aluno->email,
                'cpf' => $aluno->cpf,
                'data_nascimento' => $aluno->data_nascimento?->toDateString(),
                'telefone' => $aluno->telefone,
                'endereco' => $aluno->endereco,
                'status_matricula' => $aluno->status_matricula?->value,
                'data_matricula' => $aluno->data_matricula?->toDateString(),
                'turma' => $aluno->turma?->only(['id', 'nome', 'ano_letivo', 'serie', 'turno']),
            ],
            'avaliacoes' => $aluno->avaliacoes->map(fn ($av) => [
                'disciplina' => $av->disciplina?->nome,
                'av1' => $av->av1,
                'av2' => $av->av2,
                'av3' => $av->av3,
                'av4' => $av->av4,
                'substitutiva' => $av->substitutiva,
                'recuperacao_final' => $av->recuperacao_final,
                'nota_final' => $av->nota_final,
                'situacao' => $av->situacao?->value,
            ])->all(),
            'faltas' => DB::table('faltas')
                ->where('aluno_id', $aluno->id)
                ->orWhere('matricula', $aluno->numero_matricula)
                ->select(['data_falta', 'justificada', 'observacoes'])
                ->orderBy('data_falta', 'desc')
                ->get()
                ->all(),
        ];
    }

    public function solicitarExclusao(Aluno $aluno, User $solicitante, ?string $motivo = null): SolicitacaoExclusao
    {
        $pendenteExistente = SolicitacaoExclusao::where('aluno_id', $aluno->id)
            ->where('status', StatusSolicitacaoExclusao::Pendente)
            ->first();

        if ($pendenteExistente !== null) {
            return $pendenteExistente;
        }

        return SolicitacaoExclusao::create([
            'aluno_id' => $aluno->id,
            'solicitante_user_id' => $solicitante->id,
            'status' => StatusSolicitacaoExclusao::Pendente,
            'motivo' => $motivo,
        ]);
    }

    public function efetivarExclusao(SolicitacaoExclusao $solicitacao, User $aprovador): void
    {
        if (! $solicitacao->isPendente()) {
            throw new RuntimeException('Solicitação já foi decidida.');
        }

        if ($solicitacao->solicitante_user_id === $aprovador->id) {
            throw new RuntimeException('Aprovador não pode ser o mesmo que solicitou (4-eyes principle).');
        }

        DB::transaction(function () use ($solicitacao, $aprovador): void {
            $aluno = $solicitacao->aluno;

            if ($aluno !== null) {
                if ($aluno->foto_perfil && Storage::disk('public')->exists($aluno->foto_perfil)) {
                    Storage::disk('public')->delete($aluno->foto_perfil);
                }

                $aluno->update([
                    'nome' => 'Aluno Excluído',
                    'email' => "deleted_{$aluno->id}@anonimo.local",
                    'cpf' => str_pad((string) $aluno->id, 11, '0', STR_PAD_LEFT),
                    'telefone' => null,
                    'endereco' => null,
                    'foto_perfil' => null,
                ]);

                $aluno->delete(); // soft delete
            }

            $solicitacao->update([
                'status' => StatusSolicitacaoExclusao::Aprovada,
                'decided_by' => $aprovador->id,
                'decided_at' => CarbonImmutable::now(),
            ]);
        });
    }

    public function rejeitarExclusao(SolicitacaoExclusao $solicitacao, User $aprovador): void
    {
        if (! $solicitacao->isPendente()) {
            throw new RuntimeException('Solicitação já foi decidida.');
        }

        $solicitacao->update([
            'status' => StatusSolicitacaoExclusao::Rejeitada,
            'decided_by' => $aprovador->id,
            'decided_at' => CarbonImmutable::now(),
        ]);
    }
}
