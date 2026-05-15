<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SolicitacaoExclusao;
use App\Services\LgpdService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class SolicitacaoExclusaoController extends Controller
{
    public function __construct(
        private readonly LgpdService $service,
    ) {}

    public function index(): View
    {
        $solicitacoes = SolicitacaoExclusao::with(['aluno', 'solicitante', 'decidedBy'])
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.lgpd.solicitacoes', compact('solicitacoes'));
    }

    public function aprovar(Request $request, SolicitacaoExclusao $solicitacao): RedirectResponse
    {
        try {
            $this->service->efetivarExclusao($solicitacao, $request->user());
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.lgpd.solicitacoes')
            ->with('success', 'Solicitação aprovada e aluno anonimizado.');
    }

    public function rejeitar(Request $request, SolicitacaoExclusao $solicitacao): RedirectResponse
    {
        try {
            $this->service->rejeitarExclusao($solicitacao, $request->user());
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.lgpd.solicitacoes')->with('success', 'Solicitação rejeitada.');
    }
}
