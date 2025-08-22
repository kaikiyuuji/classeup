<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Minhas Turmas') }}
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
                            <h1 class="text-3xl font-bold text-gray-900">Minhas Turmas</h1>
                            <p class="text-gray-600 mt-2">Visualize suas turmas, disciplinas e alunos vinculados</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Professor</p>
                                <p class="font-semibold text-gray-900">{{ $professor->nome }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <x-heroicon-o-building-office class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total de Turmas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $turmasComVinculo->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <x-heroicon-o-book-open class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Disciplinas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $turmasComVinculo->sum(function($turma) { return $turma->disciplinas->count(); }) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <x-heroicon-o-users class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Turmas Ativas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $turmasComVinculo->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Turmas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-building-office class="w-5 h-5 mr-2 text-blue-600" />
                        Turmas Vinculadas
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Clique em uma turma para ver os alunos e gerenciar atividades</p>
                </div>
                
                @if($turmasComVinculo->count() > 0)
                    <div class="p-6">
                        <div class="grid gap-6">
                            @foreach($turmasComVinculo as $turma)
                                <div class="border border-gray-200 rounded-lg p-6 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                                <x-heroicon-o-building-office class="w-6 h-6 text-white" />
                                            </div>
                                            <div>
                                                <h4 class="text-xl font-semibold text-gray-900">{{ $turma->turma_nome }}</h4>
                                                <div class="flex items-center space-x-4 mt-1">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <x-heroicon-o-academic-cap class="w-3 h-3 mr-1" />
                                                        {{ $turma->serie }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                                        {{ ucfirst($turma->turno) }}
                                                    </span>
                                                    <span class="text-sm text-gray-500">
                                                        Capacidade: {{ $turma->capacidade_maxima }} alunos
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('professor.turmas.show', $turma->turma_id) }}" 
                                               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm">
                                                <x-heroicon-o-eye class="w-4 h-4 mr-2" />
                                                Ver Turma
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Disciplinas da Turma -->
                                    <div class="space-y-3">
                                        <h5 class="text-sm font-medium text-gray-700 flex items-center">
                                            <x-heroicon-o-book-open class="w-4 h-4 mr-1" />
                                            Disciplinas Ministradas
                                        </h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($turma->disciplinas as $disciplina)
                                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                                                    <div class="flex items-center">
                                                        <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center mr-3">
                                                            <x-heroicon-o-book-open class="w-4 h-4 text-white" />
                                                        </div>
                                                        <div>
                                                            <h6 class="font-medium text-gray-900">{{ $disciplina->disciplina_codigo }}</h6>
                                                            <p class="text-sm text-gray-600">{{ $disciplina->disciplina_nome }}</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="flex gap-2">
                                                        <a href="{{ route('professor.chamada.fazer', ['turma' => $turma->turma_id, 'disciplina' => $disciplina->disciplina_id]) }}"
                                                           class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md transition-colors text-sm font-medium">
                                                            <x-heroicon-o-clipboard-document-check class="w-4 h-4 mr-1" />
                                                            Chamada
                                                        </a>
                                                        
                                                        <a href="{{ route('professor.chamadas.gerenciar', ['turma' => $turma->turma_id, 'disciplina' => $disciplina->disciplina_id]) }}"
                                                           class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md transition-colors text-sm font-medium">
                                                            <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-1" />
                                                            Gerenciar
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <x-heroicon-o-building-office class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma turma vinculada</h3>
                        <p class="text-gray-500">Você não possui turmas vinculadas no momento.</p>
                        <p class="text-sm text-gray-400 mt-1">Entre em contato com a administração para vincular turmas e disciplinas.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>