<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard do Professor') }}
        </h2>
    </x-slot>

    @section('content')
    <div class="container mx-auto px-4 py-6">
        <x-session-messages />
        
        <!-- Header com informações do professor -->
        <div class="mb-8 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ $professor->nome }}</h1>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="text-blue-100">Professor</span>
                            <div class="flex items-center space-x-1">
                                @if($professor->ativo)
                                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                    <span class="text-green-200 text-sm">Ativo</span>
                                @else
                                    <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                                    <span class="text-red-200 text-sm">Inativo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-blue-100">{{ now()->format('d/m/Y') }}</p>
                    <p class="text-blue-200 text-sm">{{ now()->format('H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <x-heroicon-o-academic-cap class="w-6 h-6" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Minhas Turmas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $turmasComDisciplinas->count() }}</p>
                        <p class="text-xs text-gray-500">Turmas ativas</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <x-heroicon-o-users class="w-6 h-6" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total de Alunos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalAlunos }}</p>
                        <p class="text-xs text-gray-500">Estudantes ativos</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <x-heroicon-o-hand-thumb-up class="w-6 h-6" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Presenças</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalPresencas }}</p>
                        <p class="text-xs text-gray-500">Registros positivos</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <x-heroicon-o-user-minus class="w-6 h-6" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Faltas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalFaltas }}</p>
                        <p class="text-xs text-gray-500">{{ $percentualFrequencia }}% frequência</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas de Frequência -->
        <div class="bg-white rounded-lg shadow mb-8 border-l-4 border-indigo-500">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Estatísticas de Frequência</h3>
                <p class="text-sm text-gray-600">Resumo geral das suas turmas</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $totalPresencas }}</div>
                        <div class="text-sm text-gray-600">Total de Presenças</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-red-600">{{ $totalFaltas }}</div>
                        <div class="text-sm text-gray-600">Total de Faltas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $percentualFrequencia }}%</div>
                        <div class="text-sm text-gray-600">Percentual de Frequência</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ $disciplinas->count() }}</div>
                        <div class="text-sm text-gray-600">Disciplinas Lecionadas</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção de Turmas e Disciplinas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Minhas Turmas com Disciplinas -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Minhas Turmas e Disciplinas</h3>
                </div>
                <div class="p-6">
                    @if($turmasComDisciplinas->count() > 0)
                        <div class="space-y-4">
                            @foreach($turmasComDisciplinas as $turma)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="mb-3">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $turma['nome'] }}</h4>
                                        <p class="text-sm text-gray-500">{{ ucfirst($turma['serie']) }} - {{ ucfirst($turma['turno']) }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $alunosPorTurma->get($turma['id'])->total ?? 0 }} alunos
                                        </p>
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-xs font-medium text-gray-600 mb-1">Disciplinas:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($turma['disciplinas'] as $disciplina)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $disciplina['nome'] }}
                                                </span>
                                            @endforeach
                                        </div>                           
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Ver Alunos
                                        </a>
                                        <a href="#" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                            Lançar Chamada
                                        </a>
                                        <a href="#" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                            Lançar Notas
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-heroicon-o-building-office class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma turma atribuída</h3>
                            <p class="mt-1 text-sm text-gray-500">Entre em contato com o administrador para vincular suas turmas.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Atalhos Rápidos -->
            <div class="bg-white rounded-lg shadow border-l-4 border-green-500">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ações Rápidas</h3>
                    <p class="text-sm text-gray-600">Acesso rápido às principais funcionalidades</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('professor.chamadas.index') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                            <div class="p-3 bg-blue-100 group-hover:bg-blue-200 rounded-xl">
                                <x-heroicon-o-clipboard-document-check class="w-6 h-6 text-blue-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Nova Chamada</p>
                                <p class="text-xs text-gray-600">Registrar presença</p>
                            </div>
                        </a>

                        <a href="{{ route('professor.turmas.index') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                            <div class="p-3 bg-green-100 group-hover:bg-green-200 rounded-xl">
                                <x-heroicon-o-user-group class="w-6 h-6 text-green-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Minhas Turmas</p>
                                <p class="text-xs text-gray-600">Gerenciar turmas</p>
                            </div>
                        </a>

                        <a href="{{ route('professor.turmas.index') }}" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                            <div class="p-3 bg-purple-100 group-hover:bg-purple-200 rounded-xl">
                                <x-heroicon-o-book-open class="w-6 h-6 text-purple-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Disciplinas</p>
                                <p class="text-xs text-gray-600">Ver matérias</p>
                            </div>
                        </a>

                        <a href="{{ route('professor.relatorios.frequencia', ['turma' => 1]) }}" class="flex items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors group">
                            <div class="p-3 bg-orange-100 group-hover:bg-orange-200 rounded-xl">
                                <x-heroicon-o-chart-bar class="w-6 h-6 text-orange-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Relatórios</p>
                                <p class="text-xs text-gray-600">Ver estatísticas</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>




        <!-- Alunos com Mais Faltas -->
        @if($alunosComMaisFaltas->count() > 0)
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Alunos com Mais Faltas</h3>
                <p class="text-sm text-gray-600">Nas suas disciplinas</p>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($alunosComMaisFaltas as $aluno)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $aluno['nome'] }}</h4>
                                    <p class="text-sm text-gray-500">Matrícula: {{ $aluno['matricula'] }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $aluno['total_faltas'] }} faltas
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Chamadas Recentes -->
        <div class="bg-white rounded-lg shadow mb-8 border-l-4 border-indigo-500">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Chamadas Recentes</h3>
                        <p class="text-sm text-gray-600">Últimas 4 chamadas realizadas</p>
                    </div>
                    <a href="{{ route('professor.chamadas.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        Ver todas →
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($chamadasRecentes->count() > 0)
                    <div class="space-y-4">
                        @foreach($chamadasRecentes as $chamada)
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg hover:from-indigo-50 hover:to-indigo-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <x-heroicon-o-calendar-days class="w-6 h-6 text-indigo-600" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $chamada->data_chamada->format('d/m/Y') }}</p>
                                        <p class="text-sm text-gray-600">
                                            @if($chamada->disciplina && $chamada->disciplina->codigo)
                                                {{ $chamada->disciplina->codigo }} - {{ $chamada->disciplina->nome }}
                                            @elseif($chamada->disciplina)
                                                {{ $chamada->disciplina->nome }}
                                            @else
                                                Disciplina não encontrada
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $chamada->data_chamada->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="flex space-x-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                            {{ $chamada->presencas ?? 0 }} P
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                            {{ $chamada->faltas ?? 0 }} F
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <x-heroicon-o-clipboard-document-list class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma chamada realizada</h3>
                        <p class="mt-1 text-sm text-gray-500">Comece criando sua primeira chamada.</p>
                        <div class="mt-6">
                            <a href="{{ route('professor.chamadas.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <x-heroicon-o-plus class="-ml-1 mr-2 h-5 w-5" />
                                Nova Chamada
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
    @endsection
</x-app-layout>