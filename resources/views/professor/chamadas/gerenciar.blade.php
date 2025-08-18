<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gerenciar Chamadas') }}
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
                            <h1 class="text-3xl font-bold text-gray-900">Gerenciar Chamadas</h1>
                            <p class="text-gray-600 mt-2">
                                {{ $turma->nome }} - {{ $turma->serie }} ({{ ucfirst($turma->turno) }}) | {{ $disciplina->nome }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                Professor: {{ $professor->nome }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('professor.chamada.fazer', ['turma' => $turma->id, 'disciplina' => $disciplina->id]) }}" 
                               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-medium shadow-sm">
                                <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                                Nova Chamada
                            </a>
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
                    <h3 class="text-lg font-semibold text-gray-900">Filtros de Período</h3>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('professor.chamadas.gerenciar', ['turma' => $turma->id, 'disciplina' => $disciplina->id]) }}" class="flex flex-col sm:flex-row sm:items-end space-y-4 sm:space-y-0 sm:space-x-4">
                        <div class="flex-1">
                            <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-2">Data Início</label>
                            <input type="date" id="data_inicio" name="data_inicio" value="{{ $dataInicio }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="flex-1">
                            <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-2">Data Fim</label>
                            <input type="date" id="data_fim" name="data_fim" value="{{ $dataFim }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="submit" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm">
                                <x-heroicon-o-funnel class="w-4 h-4 mr-2" />
                                Filtrar
                            </button>
                            <a href="{{ route('professor.chamadas.gerenciar', ['turma' => $turma->id, 'disciplina' => $disciplina->id]) }}" 
                               class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm">
                                <x-heroicon-o-x-mark class="w-4 h-4 mr-2" />
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Chamadas por Dia -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Chamadas Registradas</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Período: {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
                    </p>
                </div>
                
                @if($chamadasPorDia->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($chamadasPorDia->sortByDesc('data_chamada') as $data => $chamadas)
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                            <x-heroicon-o-calendar-days class="w-6 h-6 text-blue-600" />
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">
                                                {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($data)->locale('pt_BR')->isoFormat('dddd') }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4">
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $chamadas->where('status', 'presente')->count() }} presenças
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ $chamadas->where('status', 'falta')->count() }} faltas
                                            </p>
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            <a href="{{ route('professor.chamada.fazer', ['turma' => $turma->id, 'disciplina' => $disciplina->id]) }}?data={{ $data }}"
                                               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm text-sm">
                                                <x-heroicon-o-pencil class="w-4 h-4 mr-2" />
                                                Editar
                                            </a>
                                            
                                            <form method="POST" action="{{ route('admin.chamadas.excluir-dia', ['data' => $data, 'turma' => $turma->id, 'disciplina' => $disciplina->id]) }}" 
                                                  class="inline-block" 
                                                  onsubmit="return handleDeleteCallsConfirm(event)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm text-sm">
                                                    <x-heroicon-o-trash class="w-4 h-4 mr-2" />
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Lista de Alunos -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($chamadas->sortBy('aluno.nome') as $chamada)
                                            <div class="flex items-center justify-between bg-white rounded-lg p-3 shadow-sm">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 {{ $chamada->status === 'presente' ? 'bg-green-100' : 'bg-red-100' }}">
                                                        @if($chamada->status === 'presente')
                                                            <x-heroicon-o-check class="w-4 h-4 text-green-600" />
                                                        @else
                                                            <x-heroicon-o-x-mark class="w-4 h-4 text-red-600" />
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $chamada->aluno->nome ?? 'N/A' }}</p>
                                                        <p class="text-xs text-gray-500">{{ $chamada->matricula }}</p>
                                                    </div>
                                                </div>
                                                
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $chamada->status === 'presente' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $chamada->status === 'presente' ? 'P' : 'F' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <x-heroicon-o-clipboard-document-list class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma chamada encontrada</h3>
                        <p class="text-gray-500 mb-6">Não há registros de chamadas para o período selecionado.</p>
                        
                        <a href="{{ route('professor.chamada.fazer', ['turma' => $turma->id, 'disciplina' => $disciplina->id]) }}"
                           class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-medium shadow-sm">
                            <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                            Fazer Primeira Chamada
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>