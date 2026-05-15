<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Services\LgpdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LgpdController extends Controller
{
    public function __construct(
        private readonly LgpdService $service,
    ) {}

    /**
     * Aluno baixa os próprios dados.
     */
    public function meusDados(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user === null || $user->aluno_id === null) {
            throw new AccessDeniedHttpException('Usuário não está vinculado a um aluno.');
        }

        $aluno = Aluno::findOrFail($user->aluno_id);

        return response()->json($this->service->exportarDadosDoAluno($aluno));
    }

    /**
     * Admin/aluno (próprio) exporta dados do aluno.
     */
    public function exportarAluno(Request $request, Aluno $aluno): JsonResponse
    {
        $user = $request->user();

        if ($user === null) {
            throw new AccessDeniedHttpException;
        }

        if (! $user->can('view', $aluno)) {
            throw new AccessDeniedHttpException;
        }

        return response()->json($this->service->exportarDadosDoAluno($aluno));
    }

    /**
     * Aluno solicita a própria exclusão.
     */
    public function solicitarExclusao(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null || $user->aluno_id === null) {
            throw new AccessDeniedHttpException;
        }

        $aluno = Aluno::findOrFail($user->aluno_id);
        $request->validate(['motivo' => 'nullable|string|max:1000']);

        $this->service->solicitarExclusao($aluno, $user, $request->string('motivo')->toString() ?: null);

        return redirect()->back()->with('success', 'Solicitação de exclusão registrada. Aguardando aprovação.');
    }
}
