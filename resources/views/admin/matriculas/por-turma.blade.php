<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    Matrículas da Turma: {{ $turma->nome }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $turma->ano_letivo }} - {{ ucfirst($turma->periodo) }}</p>
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
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Matrículas da Turma</h1>
                    <div class="mt-2">
                        <h2 class="text-lg font-semibold text-blue-600">{{ $turma->nome }}</h2>
                        <p class="text-gray-600">{{ $turma->ano_letivo }} - {{ ucfirst($turma->periodo) }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('matriculas.index') }}" 
                       class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </a>
                    <a href="{{ route('matriculas.create', ['turma_id' => $turma->id]) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nova Matrícula
                    </a>
                </div>
            </div>
        </div>

        <!-- Informações da Turma -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg border">
                    <div class="text-sm text-gray-600">Total de Matrículas</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $matriculas->total() }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg border">
                    <div class="text-sm text-gray-600">Matrículas Ativas</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ $turma->matriculas()->where('status', 'ativa')->count() }}
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg border">
                    <div class="text-sm text-gray-600">Capacidade Máxima</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $turma->capacidade_maxima }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg border">
                    <div class="text-sm text-gray-600">Vagas Disponíveis</div>
                    @php
                        $vagasDisponiveis = $turma->capacidade_maxima - $turma->matriculas()->where('status', 'ativa')->count();
                    @endphp
                    <div class="text-2xl font-bold {{ $vagasDisponiveis > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $vagasDisponiveis }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('matriculas.por-turma', $turma) }}" 
                   class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Todas
                </a>
                <a href="{{ route('matriculas.por-turma', [$turma, 'status' => 'ativa']) }}" 
                   class="px-3 py-2 text-sm bg-green-100 text-green-800 border border-green-300 rounded-md hover:bg-green-200">
                    Ativas
                </a>
                <a href="{{ route('matriculas.por-turma', [$turma, 'status' => 'inativa']) }}" 
                   class="px-3 py-2 text-sm bg-red-100 text-red-800 border border-red-300 rounded-md hover:bg-red-200">
                    Inativas
                </a>
            </div>
        </div>

        <!-- Lista de Alunos -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aluno
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data Matrícula
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
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
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if($matricula->aluno->foto_perfil)
                                            <img class="h-12 w-12 rounded-full object-cover" 
                                                 src="{{ Storage::url($matricula->aluno->foto_perfil) }}" 
                                                 alt="{{ $matricula->aluno->nome }}">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600 text-lg"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $matricula->aluno->nome }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $matricula->aluno->email }}
                                        </div>
                                        @if($matricula->aluno->telefone)
                                            <div class="text-sm text-gray-500">
                                                <i class="fas fa-phone text-xs mr-1"></i>{{ $matricula->aluno->telefone }}
                                            </div>
                                        @endif
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
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('alunos.show', $matricula->aluno) }}" 
                                       class="text-blue-600 hover:text-blue-900 p-1" title="Ver Aluno">
                                        <i class="fas fa-user"></i>
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
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">Nenhum aluno matriculado</p>
                                    <p class="text-sm">Esta turma ainda não possui alunos matriculados</p>
                                    <a href="{{ route('matriculas.create', ['turma_id' => $turma->id]) }}" 
                                       class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Matricular Primeiro Aluno
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