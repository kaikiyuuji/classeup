<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Audit Log</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <select name="resource" class="rounded border-gray-300">
                        <option value="">Todos os recursos</option>
                        @foreach (['Aluno', 'Professor', 'Turma', 'Disciplina', 'Avaliacao', 'Falta'] as $r)
                            <option value="{{ $r }}" @selected(($filtros['resource'] ?? null) === $r)>{{ $r }}</option>
                        @endforeach
                    </select>

                    <select name="action" class="rounded border-gray-300">
                        <option value="">Todas as ações</option>
                        @foreach (['created', 'updated', 'deleted'] as $a)
                            <option value="{{ $a }}" @selected(($filtros['action'] ?? null) === $a)>{{ ucfirst($a) }}</option>
                        @endforeach
                    </select>

                    <input type="number" name="user" placeholder="User ID" value="{{ $filtros['user'] ?? '' }}" class="rounded border-gray-300">
                    <input type="date" name="from" value="{{ $filtros['from'] ?? '' }}" class="rounded border-gray-300">
                    <input type="date" name="to" value="{{ $filtros['to'] ?? '' }}" class="rounded border-gray-300">

                    <button type="submit" class="md:col-span-5 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Filtrar
                    </button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quando</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Recurso</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ação</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($entradas as $entrada)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 font-mono">{{ $entrada['at']->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $entrada['resource'] }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">#{{ $entrada['resource_id'] }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <span class="px-2 py-1 rounded text-xs
                                            @if ($entrada['action'] === 'created') bg-green-100 text-green-800
                                            @elseif ($entrada['action'] === 'updated') bg-blue-100 text-blue-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $entrada['action'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $entrada['user_id'] ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Nenhuma entrada encontrada com esses filtros.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
