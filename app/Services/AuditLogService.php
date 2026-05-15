<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Aluno;
use App\Models\Avaliacao;
use App\Models\Disciplina;
use App\Models\Falta;
use App\Models\Professor;
use App\Models\Turma;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Agrega as colunas de auditoria (created_by, updated_by, deleted_by)
 * das 6 entidades de domínio em um stream único ordenado por timestamp.
 *
 * Cada entrada é um array com:
 *   - resource: 'Aluno' | 'Professor' | 'Turma' | 'Disciplina' | 'Avaliacao' | 'Falta'
 *   - resource_id: int
 *   - action: 'created' | 'updated' | 'deleted'
 *   - user_id: int|null (quem fez)
 *   - at: CarbonImmutable
 */
class AuditLogService
{
    private const RESOURCES = [
        'Aluno' => Aluno::class,
        'Professor' => Professor::class,
        'Turma' => Turma::class,
        'Disciplina' => Disciplina::class,
        'Avaliacao' => Avaliacao::class,
        'Falta' => Falta::class,
    ];

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function listar(array $filtros = []): Collection
    {
        $resourcesFiltrados = $this->resolverResources($filtros['resource'] ?? null);
        $actionFiltrada = $filtros['action'] ?? null;
        $userFiltrado = isset($filtros['user']) ? (int) $filtros['user'] : null;
        $from = isset($filtros['from']) ? CarbonImmutable::parse((string) $filtros['from'])->startOfDay() : null;
        $to = isset($filtros['to']) ? CarbonImmutable::parse((string) $filtros['to'])->endOfDay() : null;

        $entradas = new Collection;

        foreach ($resourcesFiltrados as $label => $class) {
            /** @var Collection<int, Model> $registros */
            $registros = $class::withTrashed()->get();

            foreach ($registros as $registro) {
                foreach ($this->extrairEntradas($label, $registro) as $entrada) {
                    if ($actionFiltrada !== null && $entrada['action'] !== $actionFiltrada) {
                        continue;
                    }
                    if ($userFiltrado !== null && $entrada['user_id'] !== $userFiltrado) {
                        continue;
                    }
                    if ($from !== null && $entrada['at']->lt($from)) {
                        continue;
                    }
                    if ($to !== null && $entrada['at']->gt($to)) {
                        continue;
                    }

                    $entradas->push($entrada);
                }
            }
        }

        return $entradas->sortByDesc('at')->values();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extrairEntradas(string $label, Model $registro): array
    {
        $entradas = [];

        $createdBy = $registro->getAttribute('created_by');
        $createdAt = $registro->getAttribute('created_at');
        if ($createdAt !== null) {
            $entradas[] = [
                'resource' => $label,
                'resource_id' => $registro->getKey(),
                'action' => 'created',
                'user_id' => $createdBy !== null ? (int) $createdBy : null,
                'at' => CarbonImmutable::parse($createdAt),
            ];
        }

        $updatedBy = $registro->getAttribute('updated_by');
        $updatedAt = $registro->getAttribute('updated_at');
        if ($updatedAt !== null && $updatedAt != $createdAt) {
            $entradas[] = [
                'resource' => $label,
                'resource_id' => $registro->getKey(),
                'action' => 'updated',
                'user_id' => $updatedBy !== null ? (int) $updatedBy : null,
                'at' => CarbonImmutable::parse($updatedAt),
            ];
        }

        $deletedBy = $registro->getAttribute('deleted_by');
        $deletedAt = $registro->getAttribute('deleted_at');
        if ($deletedAt !== null) {
            $entradas[] = [
                'resource' => $label,
                'resource_id' => $registro->getKey(),
                'action' => 'deleted',
                'user_id' => $deletedBy !== null ? (int) $deletedBy : null,
                'at' => CarbonImmutable::parse($deletedAt),
            ];
        }

        return $entradas;
    }

    /**
     * @return array<string, class-string<Model>>
     */
    private function resolverResources(?string $filtro): array
    {
        if ($filtro === null || ! isset(self::RESOURCES[$filtro])) {
            return self::RESOURCES;
        }

        return [$filtro => self::RESOURCES[$filtro]];
    }
}
