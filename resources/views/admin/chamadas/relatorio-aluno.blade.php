<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Relatório de Chamadas por Aluno') }}
        </h2>
    </x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Relatório de Chamadas por Aluno</h1>
                <a href="{{ route('admin.chamadas.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />Voltar
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Formulário de Busca -->
            <form method="GET" action="{{ route('admin.chamadas.relatorio-aluno') }}" class="mb-6">
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label for="matricula" class="block text-sm font-medium text-gray-700 mb-2">
                            Matrícula do Aluno:
                        </label>
                        <input type="text" 
                               id="matricula" 
                               name="matricula" 
                               value="{{ $matricula }}"
                               placeholder="Digite a matrícula"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                            Data Início:
                        </label>
                        <input type="date" 
                               id="data_inicio" 
                               name="data_inicio" 
                               value="{{ $dataInicio }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-2">
                            Data Fim:
                        </label>
                        <input type="date" 
                               id="data_fim" 
                               name="data_fim" 
                               value="{{ $dataFim }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-medium shadow-sm flex items-center justify-center">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-2" />
                            Buscar Relatório
                        </button>
                    </div>
                </div>
            </form>

            @if($matricula && !$aluno)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-red-700">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 mr-2" />
                        Aluno com matrícula "{{ $matricula }}" não encontrado.
                    </p>
                </div>
            @endif

            @if($aluno)
                <!-- Informações do Aluno -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        @if($aluno->foto_perfil)
                            <img src="{{ Storage::url($aluno->foto_perfil) }}" 
                                 alt="{{ $aluno->nome }}" 
                                 class="w-16 h-16 rounded-full object-cover mr-4">
                        @else
                            <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                                <x-heroicon-o-user class="w-5 h-5 text-gray-600" />
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $aluno->nome }}</h3>
                            <p class="text-gray-600">Matrícula: {{ $aluno->numero_matricula }}</p>
                            <p class="text-gray-600">Turma: {{ $aluno->turma->nome ?? 'Não informada' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Resumo de Chamadas -->
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-clipboard-document-list class="w-8 h-8 text-blue-500 mr-3" />
                            <div>
                                <p class="text-sm text-gray-600">Total de Chamadas</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $chamadas->count() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-check-circle class="w-8 h-8 text-green-500 mr-3" />
                            <div>
                                <p class="text-sm text-gray-600">Presenças</p>
                                <p class="text-2xl font-bold text-green-600">{{ $chamadas->where('status', 'presente')->count() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-x-mark class="w-8 h-8 text-red-500 mr-3" />
                            <div>
                                <p class="text-sm text-gray-600">Faltas</p>
                                <p class="text-2xl font-bold text-red-600">{{ $chamadas->where('status', 'falta')->count() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-yellow-500 mr-3" />
                            <div>
                                <p class="text-sm text-gray-600">Faltas Não Justificadas</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ $chamadas->where('status', 'falta')->where('justificada', false)->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Chamadas -->
                @if($chamadas->isEmpty())
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-700">
                            <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                            Nenhuma chamada encontrada no período selecionado.
                        </p>
                    </div>
                @else
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <h4 class="font-semibold text-gray-800">Detalhes das Chamadas</h4>
                        </div>
                        
                        <div class="divide-y divide-gray-200">
                            @foreach($chamadas as $chamada)
                                <div class="p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-2">
                                                <span class="text-sm font-medium text-gray-900">
                                                    {{ $chamada->data_chamada->format('d/m/Y') }}
                                                </span>
                                                <span class="mx-2 text-gray-400">•</span>
                                                <span class="text-sm text-gray-600">{{ $chamada->disciplina->nome }}</span>
                                                <span class="mx-2 text-gray-400">•</span>
                                                <span class="text-sm text-gray-600">Prof. {{ $chamada->professor->nome }}</span>
                                            </div>
                                            
                                            <div class="flex items-center">
                                                @if($chamada->status === 'presente')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <x-heroicon-o-check class="w-3 h-3 mr-1" />Presente
                                                    </span>
                                                @else
                                                    @if($chamada->justificada)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <x-heroicon-o-check class="w-3 h-3 mr-1" />Falta Justificada
                                                        </span>
                                                        @if($chamada->observacoes)
                                                            <span class="ml-2 text-sm text-gray-600">{{ $chamada->observacoes }}</span>
                                                        @endif
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <x-heroicon-o-x-mark class="w-3 h-3 mr-1" />Falta Não Justificada
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($chamada->status === 'falta')
                                            <div class="flex gap-2 ml-4">
                                                @if(!$chamada->justificada)
                                                    <a href="{{ route('admin.chamadas.justificar', $chamada->id) }}" 
                                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                                        <x-heroicon-o-pencil class="w-4 h-4 mr-1" />Justificar
                                                    </a>
                                                @else
                                                    <form action="{{ route('admin.chamadas.remover-justificativa', $chamada->id) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('Tem certeza que deseja remover a justificativa?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-800 text-sm">
                                                            <x-heroicon-o-x-mark class="w-4 h-4 mr-1" />Remover Justificativa
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
</x-app-layout>