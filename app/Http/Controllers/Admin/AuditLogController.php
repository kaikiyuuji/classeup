<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct(
        private readonly AuditLogService $service,
    ) {}

    public function __invoke(Request $request): View
    {
        $entradas = $this->service->listar($request->only(['resource', 'action', 'user', 'from', 'to']));

        return view('admin.audit-log.index', [
            'entradas' => $entradas,
            'filtros' => $request->only(['resource', 'action', 'user', 'from', 'to']),
        ]);
    }
}
