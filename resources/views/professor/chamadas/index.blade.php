<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Controle de Chamadas') }}
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
                            <h1 class="text-3xl font-bold text-gray-900">Controle de Chamadas</h1>
                            <p class="text-gray-600 mt-2">Gerencie a presença dos alunos e visualize relatórios</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('professor.chamadas.relatorio') }}" 
                               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-medium shadow-sm">
                                <x-heroicon-o-chart-bar class="w-5 h-5 mr-2" />
                                Ver Relatórios
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Turmas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-building-office class="w-5 h-5 mr-2 text-blue-600" />
                        Minhas Turmas
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Selecione uma turma para fazer a chamada</p>
                </div>
                
                @if($turmasComVinculo->count() > 0)
                    <div class="p-6">
                        <div class="grid gap-6">
                            @foreach($turmasComVinculo as $turma)
                                <div class="border border-gray-200 rounded-lg p-6 hover:border-blue-300 transition-colors">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="text-xl font-semibold text-gray-900">{{ $turma->turma_nome }}</h4>
                                            <p class="text-gray-600">{{ $turma->serie }} • {{ ucfirst($turma->turno) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                {{ $turma->disciplinas->count() }} disciplina(s)
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        @foreach($turma->disciplinas as $disciplina)
                                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center mr-4">
                                                        <x-heroicon-o-book-open class="w-5 h-5 text-white" />
                                                    </div>
                                                    <div>
                                                        <h5 class="font-medium text-gray-900">{{ $disciplina->disciplina_codigo }} - {{ $disciplina->disciplina_nome }}</h5>
                                                        <p class="text-sm text-gray-500">Disciplina</p>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex gap-2">
                                    <a href="{{ route('professor.chamada.fazer', ['turma' => $turma->turma_id, 'disciplina' => $disciplina->disciplina_id]) }}"
                                       class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm">
                                        <x-heroicon-o-clipboard-document-check class="w-5 h-5 mr-2" />
                                        Fazer Chamada
                                    </a>
                                    
                                    <a href="{{ route('professor.chamadas.gerenciar', ['turma' => $turma->turma_id, 'disciplina' => $disciplina->disciplina_id]) }}"
                                        class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm">
                                         <x-heroicon-o-cog-6-tooth class="w-5 h-5 mr-2" />
                                         Gerenciar
                                     </a>
                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <x-heroicon-o-building-office class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma turma vinculada</h3>
                        <p class="text-gray-500">Você não possui turmas vinculadas para fazer chamadas.</p>
                        <p class="text-sm text-gray-400 mt-1">Entre em contato com a administração para vincular turmas.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>