<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    {{ __('Gerenciar Turmas') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Visualize e gerencie todas as turmas cadastradas</p>
            </div>
            <a href="{{ route('turmas.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <x-icons.plus class="w-4 h-4 mr-2" />
                Nova Turma
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                    <x-icons.check-circle class="w-5 h-5 mr-2 text-green-600" />
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">

                @if($turmas->count() > 0)
                    <!-- Table View -->
                    <div>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Turma
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Ano Letivo
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Nível Educacional
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Turno
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Ocupação
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($turmas as $turma)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <x-icons.building class="w-5 h-5 text-blue-600" />
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $turma->nome }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $turma->ano_letivo }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ App\Models\Turma::getNiveisEducacionais()[$turma->serie] ?? $turma->serie }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                            @if($turma->turno === 'matutino')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Matutino
                                                </span>
                                            @elseif($turma->turno === 'vespertino')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    Vespertino
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    Noturno
                                                </span>
                                            @endif
                                        </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @php
                                                    $alunosMatriculados = $turma->alunos->count();
                                                    $capacidade = $turma->capacidade_maxima;
                                                    $porcentagem = $capacidade > 0 ? round(($alunosMatriculados / $capacidade) * 100, 1) : 0;
                                                @endphp
                                                <div class="flex items-center">
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between text-xs mb-1">
                                                            <span class="text-gray-600">{{ $alunosMatriculados }}/{{ $capacidade }}</span>
                                                            <span class="font-medium
                                                                @if($porcentagem >= 90) text-red-600
                                                                @elseif($porcentagem >= 75) text-yellow-600
                                                                @else text-green-600
                                                                @endif">{{ $porcentagem }}%</span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                                            <div class="h-2 rounded-full
                                                                @if($porcentagem >= 90) bg-red-500
                                                                @elseif($porcentagem >= 75) bg-yellow-500
                                                                @else bg-green-500
                                                                @endif" style="width: {{ min($porcentagem, 100) }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($turma->ativo)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <x-icons.circle class="w-1.5 h-1.5 mr-1.5" />
                                                        Ativa
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <x-icons.circle class="w-1.5 h-1.5 mr-1.5" />
                                                        Inativa
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <a href="{{ route('turmas.show', $turma) }}" 
                                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                                        <x-icons.eye class="w-3 h-3 mr-1" />
                                                        Ver
                                                    </a>
                                                    <a href="{{ route('turmas.edit', $turma) }}" 
                                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200">
                                                        <x-icons.edit class="w-3 h-3 mr-1" />
                                                        Editar
                                                    </a>
                                                    <form action="{{ route('turmas.destroy', $turma) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('Tem certeza que deseja excluir esta turma?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200">
                                                            <x-icons.trash class="w-3 h-3 mr-1" />
                                                            Excluir
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>



                    <!-- Pagination -->
                    @if($turmas->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $turmas->links() }}
                        </div>
                    @endif

                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <x-icons.building class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma turma encontrada</h3>
                        <p class="mt-1 text-sm text-gray-500">Comece criando uma nova turma.</p>
                        <div class="mt-6">
                            <a href="{{ route('turmas.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                                <x-icons.plus class="w-4 h-4 mr-2" />
                                Nova Turma
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>