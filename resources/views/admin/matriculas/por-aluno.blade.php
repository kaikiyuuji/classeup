<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    Matrículas do Aluno: {{ $aluno->nome }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $aluno->email }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('matriculas.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nova Matrícula
                </a>
                <a href="{{ route('matriculas.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-16 w-16 mr-4">
                        @if($aluno->foto_perfil)
                            <img class="h-16 w-16 rounded-full object-cover" 
                                 src="{{ Storage::url($aluno->foto_perfil) }}" 
                                 alt="{{ $aluno->nome }}">
                        @else
                            <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                <i class="fas fa-user text-gray-600 text-2xl"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Matrículas do Aluno</h1>
                        <div class="mt-1">
                            <h2 class="text-lg font-semibold text-blue-600">{{ $aluno->nome }}</h2>
                            <p class="text-gray-600">{{ $aluno->email }}</p>
                            @if($aluno->telefone)
                                <p class="text-gray-600">
                                    <i class="fas fa-phone text-xs mr-1"></i>{{ $aluno->telefone }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('matriculas.index') }}" 
                       class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </a>
                    <a href="{{ route('alunos.show', $aluno) }}" 
                       class="text-blue-600 hover:text-blue-900 px-3 py-2 rounded-lg border border-blue-300 hover:bg-blue-50">
                        <i class="fas fa-user mr-2"></i>Ver Perfil
                    </a>
                    <a href="{{ route('matriculas.create', ['aluno_id' => $aluno->id]) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nova Matrícula
                    </a>
                </div>
            </div>
        </div>

        <!-- Informações do Aluno -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg border">
                    <div class="text-sm text-gray-600">Total de Matrículas</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $matriculas->total() }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg border">
                    <div class="text-sm text-gray-600">Matrículas Ativas</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ $aluno->matriculas()->where('status', 'ativa')->count() }}
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg border">
                    <div class="text-sm text-gray-600">Transferências</div>
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ $aluno->matriculas()->where('status', 'transferida')->count() }}
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg border">
                    <div class="text-sm text-gray-600">Data de Nascimento</div>
                    <div class="text-lg font-bold text-blue-600">
                        {{ $aluno->data_nascimento ? $aluno->data_nascimento->format('d/m/Y') : 'Não informado' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('matriculas.por-aluno', $aluno) }}" 
                   class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Todas
                </a>
                <a href="{{ route('matriculas.por-aluno', [$aluno, 'status' => 'ativa']) }}" 
                   class="px-3 py-2 text-sm bg-green-100 text-green-800 border border-green-300 rounded-md hover:bg-green-200">
                    Ativas
                </a>
                <a href="{{ route('matriculas.por-aluno', [$aluno, 'status' => 'inativa']) }}" 
                   class="px-3 py-2 text-sm bg-red-100 text-red-800 border border-red-300 rounded-md hover:bg-red-200">
                    Inativas
                </a>
                <a href="{{ route('matriculas.por-aluno', [$aluno, 'status' => 'transferida']) }}" 
                   class="px-3 py-2 text-sm bg-yellow-100 text-yellow-800 border border-yellow-300 rounded-md hover:bg-yellow-200">
                    Transferidas
                </a>
            </div>
        </div>

        <!-- Histórico de Matrículas -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Turma
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data Matrícula
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Período
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($matriculas as $matricula)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-chalkboard-teacher text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $matricula->turma->nome }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $matricula->turma->ano_letivo }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $matricula->data_matricula->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $matricula->data_matricula->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'ativa' => 'bg-green-100 text-green-800',
                                        'inativa' => 'bg-red-100 text-red-800',
                                        'transferida' => 'bg-yellow-100 text-yellow-800',
                                        'cancelada' => 'bg-gray-100 text-gray-800'
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$matricula->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($matricula->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ ucfirst($matricula->turma->periodo) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Capacidade: {{ $matricula->turma->capacidade_maxima }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('turmas.show', $matricula->turma) }}" 
                                       class="text-blue-600 hover:text-blue-900 p-1" title="Ver Turma">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </a>
                                    <a href="{{ route('matriculas.show', $matricula) }}" 
                                       class="text-green-600 hover:text-green-900 p-1" title="Ver Matrícula">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('matriculas.edit', $matricula) }}" 
                                       class="text-yellow-600 hover:text-yellow-900 p-1" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('matriculas.destroy', $matricula) }}" 
                                          method="POST" class="inline" 
                                          onsubmit="return confirm('Tem certeza que deseja remover esta matrícula?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 p-1" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-graduation-cap text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">Nenhuma matrícula encontrada</p>
                                    <p class="text-sm">Este aluno ainda não possui matrículas</p>
                                    <a href="{{ route('matriculas.create', ['aluno_id' => $aluno->id]) }}" 
                                       class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Criar Primeira Matrícula
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($matriculas->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $matriculas->links() }}
            </div>
        @endif
    </div>
</div>
</x-app-layout>