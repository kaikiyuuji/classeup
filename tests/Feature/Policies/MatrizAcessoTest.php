<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use Tests\TestCase;

/**
 * Matriz de acesso: para cada rota crítica, verifica o status esperado
 * para admin, professor e aluno. Garante que reorganizações futuras de
 * rotas não vazem permissões sem que a build falhe.
 *
 * 200/302/3xx = acessível (302 cobre redirect após auth/role check ok).
 * 403 = bloqueado por role/policy.
 */
class MatrizAcessoTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: string, 2: int, 3: int, 4: int}>
     */
    public static function rotasProvider(): array
    {
        return [
            // [método HTTP, nome da rota, esperado admin, esperado professor, esperado aluno]
            'GET /alunos' => ['GET', 'alunos.index', 200, 200, 403],
            'GET /alunos/create' => ['GET', 'alunos.create', 200, 403, 403],
            'GET /professores' => ['GET', 'professores.index', 200, 200, 403],
            'GET /professores/create' => ['GET', 'professores.create', 200, 403, 403],
            'GET /disciplinas' => ['GET', 'disciplinas.index', 200, 200, 403],
            'GET /disciplinas/create' => ['GET', 'disciplinas.create', 200, 403, 403],
            'GET /turmas' => ['GET', 'turmas.index', 200, 200, 403],
            'GET /turmas/create' => ['GET', 'turmas.create', 200, 403, 403],
            'GET /faltas' => ['GET', 'faltas.index', 200, 200, 403],
            'GET /faltas/relatorio-aluno' => ['GET', 'faltas.relatorio-aluno', 200, 200, 403],
        ];
    }

    /**
     * @dataProvider rotasProvider
     */
    public function test_matriz_acesso(string $metodo, string $rota, int $admin, int $professor, int $aluno): void
    {
        $url = route($rota);

        $this->actingAsAdmin();
        $this->call($metodo, $url)->assertStatus($admin);

        $this->actingAsProfessor();
        $this->call($metodo, $url)->assertStatus($professor);

        $this->actingAsAluno();
        $this->call($metodo, $url)->assertStatus($aluno);
    }

    public function test_guest_redireciona_para_login_em_rota_autenticada(): void
    {
        $this->get(route('alunos.index'))->assertRedirect(route('login'));
    }
}
