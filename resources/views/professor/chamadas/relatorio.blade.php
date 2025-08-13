<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Relatórios de Chamadas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-session-messages />
            
            <!-- Cabeçalho -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Relatórios de Chamadas</h1>
                            <p class="text-gray-600 mt-2">Visualize estatísticas e dados de presença das suas turmas</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('professor.chamadas.index') }}" 
                               class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors font-medium shadow-sm">
                                <x-heroicon-o-arrow-left class="w-5 h-5 mr-2" />
                                Voltar às Chamadas
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Filtros</h3>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('professor.chamadas.relatorio') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-2">Data Início</label>
                            <input type="date" id="data_inicio" name="data_inicio" value="{{ $dataInicio }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-2">Data Fim</label>
                            <input type="date" id="data_fim" name="data_fim" value="{{ $dataFim }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="turma_id" class="block text-sm font-medium text-gray-700 mb-2">Turma</label>
                            <select id="turma_id" name="turma_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todas as turmas</option>
                                @foreach($turmasComVinculo->unique('turma_id') as $turma)
                                    <option value="{{ $turma->turma_id }}" {{ $turmaId == $turma->turma_id ? 'selected' : '' }}>
                                        {{ $turma->turma_nome }} - {{ $turma->serie }} ({{ ucfirst($turma->turno) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="disciplina_id" class="block text-sm font-medium text-gray-700 mb-2">Disciplina</label>
                            <select id="disciplina_id" name="disciplina_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todas as disciplinas</option>
                                @foreach($turmasComVinculo->unique('disciplina_id') as $disciplina)
                                    <option value="{{ $disciplina->disciplina_id }}" {{ $disciplinaId == $disciplina->disciplina_id ? 'selected' : '' }}>
                                        {{ $disciplina->disciplina_codigo }} - {{ $disciplina->disciplina_nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                
                        <div>
                            <label for="busca_aluno" class="block text-sm font-medium text-gray-700 mb-2">Buscar Aluno</label>
                            <input type="text" id="busca_aluno" name="busca_aluno" value="{{ request('busca_aluno') }}" 
                                   placeholder="Nome ou matrícula do aluno"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="md:col-span-2 lg:col-span-5">
                            <button type="submit" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-medium shadow-sm">
                                <x-heroicon-o-funnel class="w-5 h-5 mr-2" />
                                Aplicar Filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total de Chamadas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalChamadas }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <x-heroicon-o-check-circle class="w-6 h-6 text-green-600" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Presenças</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalPresencas }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                            <x-heroicon-o-x-circle class="w-6 h-6 text-red-600" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Faltas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalFaltas }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <x-heroicon-o-chart-bar class="w-6 h-6 text-purple-600" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">% Presença</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $percentualPresenca }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Chamadas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Detalhes das Chamadas</h3>
                    <p class="text-sm text-gray-600 mt-1">Período: {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</p>
                </div>
                
                @if($chamadas->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aluno</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disciplina</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($chamadas->sortByDesc('data_chamada') as $chamada)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($chamada->data_chamada)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $chamada->aluno->nome ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $chamada->matricula }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $chamada->disciplina->nome }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($chamada->status === 'presente')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                                                    Presente
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <x-heroicon-o-x-circle class="w-4 h-4 mr-1" />
                                                    Falta
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $chamada->observacoes ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <x-heroicon-o-clipboard-document-list class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma chamada encontrada</h3>
                        <p class="text-gray-500">Não há registros de chamadas para o período e filtros selecionados.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>