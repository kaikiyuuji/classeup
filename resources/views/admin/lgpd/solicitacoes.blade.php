<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Solicitações de Exclusão LGPD</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 px-4 py-2 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 px-4 py-2 bg-red-50 text-red-700 rounded">{{ session('error') }}</div>
                @endif

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aluno</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($solicitacoes as $s)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $s->aluno?->nome ?? '—' }} <span class="text-gray-400 text-xs">#{{ $s->aluno_id }}</span></td>
                                <td class="px-4 py-2 text-sm">{{ $s->solicitante?->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm">{{ $s->motivo ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm">{{ $s->status->label() }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $s->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2 text-sm">
                                    @if ($s->isPendente())
                                        <form method="POST" action="{{ route('admin.lgpd.aprovar', $s) }}" class="inline">
                                            @csrf
                                            <button class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Aprovar exclusão</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.lgpd.rejeitar', $s) }}" class="inline">
                                            @csrf
                                            <button class="px-2 py-1 bg-gray-300 text-gray-700 text-xs rounded hover:bg-gray-400">Rejeitar</button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-xs">decidida por #{{ $s->decided_by }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Nenhuma solicitação.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $solicitacoes->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
