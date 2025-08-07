<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Relatório de Faltas por Aluno') }}
        </h2>
    </x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Relatório de Faltas por Aluno</h1>
                <a href="{{ route('faltas.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Formulário de Busca -->
            <form method="GET" action="{{ route('faltas.relatorio-aluno') }}" class="mb-6">
                <div class="grid md:grid-cols-4 gap-4">
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
                                class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </div>
                </div>
            </form>

            @if($matricula && !$aluno)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-red-700">
                        <i class="fas fa-exclamation-circle mr-2"></i>
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
                                <i class="fas fa-user text-gray-600 text-xl"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $aluno->nome }}</h3>
                            <p class="text-gray-600">Matrícula: {{ $aluno->numero_matricula }}</p>
                            <p class="text-gray-600">Turma: {{ $aluno->turma->nome ?? 'Não informada' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Resumo de Faltas -->
                <div class="grid md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-times-circle text-red-500 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Total de Faltas</p>
                                <p class="text-2xl font-bold text-red-600">{{ $faltas->count() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Faltas Não Justificadas</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ $faltas->where('justificada', false)->count() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Faltas Justificadas</p>
                                <p class="text-2xl font-bold text-green-600">{{ $faltas->where('justificada', true)->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Faltas -->
                @if($faltas->isEmpty())
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-700">
                            <i class="fas fa-check-circle mr-2"></i>
                            Nenhuma falta encontrada no período selecionado.
                        </p>
                    </div>
                @else
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <h4 class="font-semibold text-gray-800">Detalhes das Faltas</h4>
                        </div>
                        
                        <div class="divide-y divide-gray-200">
                            @foreach($faltas as $falta)
                                <div class="p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-2">
                                                <span class="text-sm font-medium text-gray-900">
                                                    {{ $falta->data_falta->format('d/m/Y') }}
                                                </span>
                                                <span class="mx-2 text-gray-400">•</span>
                                                <span class="text-sm text-gray-600">{{ $falta->disciplina->nome }}</span>
                                                <span class="mx-2 text-gray-400">•</span>
                                                <span class="text-sm text-gray-600">Prof. {{ $falta->professor->nome }}</span>
                                            </div>
                                            
                                            <div class="flex items-center">
                                                @if($falta->justificada)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check mr-1"></i>Justificada
                                                    </span>
                                                    @if($falta->observacoes)
                                                        <span class="ml-2 text-sm text-gray-600">{{ $falta->observacoes }}</span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-times mr-1"></i>Não Justificada
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="flex gap-2 ml-4">
                                            @if(!$falta->justificada)
                                                <a href="{{ route('faltas.justificar', $falta->id) }}" 
                                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                                    <i class="fas fa-edit mr-1"></i>Justificar
                                                </a>
                                            @else
                                                <form action="{{ route('faltas.remover-justificativa', $falta->id) }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      onsubmit="return confirm('Tem certeza que deseja remover a justificativa?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 text-sm">
                                                        <i class="fas fa-times mr-1"></i>Remover Justificativa
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
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