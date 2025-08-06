<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Detalhes da Turma
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Visualize e gerencie as informações da turma {{ $turma->nome }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('turmas.edit', $turma) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('turmas.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Mensagem de Sucesso -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Card Principal -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-8">

                    <!-- Cabeçalho da Turma -->
                    <div class="flex flex-col lg:flex-row items-center lg:items-start space-y-6 lg:space-y-0 lg:space-x-8 mb-8">
                        <!-- Ícone da Turma -->
                        <div class="flex-shrink-0">
                            <div class="relative">
                                <div class="w-40 h-40 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center border-4 border-white shadow-lg">
                                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                
                                <!-- Badge de Status -->
                                <div class="absolute -bottom-2 -right-2">
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full shadow-sm
                                        {{ $turma->ativo ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                        {{ $turma->ativo ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Informações Principais -->
                        <div class="flex-1 text-center lg:text-left">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $turma->nome }}</h1>
                            <div class="flex flex-col sm:flex-row items-center lg:items-start space-y-2 sm:space-y-0 sm:space-x-4 text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-2 2m8-2l2 2m-2-2v6a2 2 0 01-2 2H10a2 2 0 01-2-2v-6"></path>
                                    </svg>
                                    <span>{{ $turma->serie }}ª Série</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ ucfirst($turma->turno) }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span>Capacidade: {{ $turma->capacidade_maxima }} alunos</span>
                                </div>
                            </div>
                            <div class="mt-3 text-sm text-gray-500">
                                Criada em {{ $turma->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Informações Detalhadas -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Informações da Turma -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Informações da Turma
                            </h3>
                            
                            <dl class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Nome da Turma</dt>
                                    <dd class="text-sm text-gray-900 font-medium">{{ $turma->nome }}</dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Ano Letivo</dt>
                                    <dd class="text-sm text-gray-900 font-medium">{{ $turma->ano_letivo }}</dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Série</dt>
                                    <dd class="text-sm text-gray-900">{{ $turma->serie }}ª Série</dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Turno</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($turma->turno) }}</dd>
                                </div>

                                <div class="flex justify-between items-center py-2">
                                    <dt class="text-sm font-medium text-gray-600">Status</dt>
                                    <dd>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $turma->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $turma->ativo ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Informações de Capacidade -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Capacidade e Ocupação
                            </h3>
                            
                            <dl class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Capacidade Máxima</dt>
                                    <dd class="text-sm text-gray-900 font-medium">{{ $turma->capacidade_maxima }} alunos</dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Alunos Matriculados</dt>
                                    <dd class="text-sm text-gray-900">{{ $turma->alunos->count() }} alunos</dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Vagas Disponíveis</dt>
                                    <dd class="text-sm text-gray-900">{{ $turma->capacidade_maxima - $turma->alunos->count() }} vagas</dd>
                                </div>

                                <div class="py-2">
                                    <dt class="text-sm font-medium text-gray-600 mb-2">Taxa de Ocupação</dt>
                                    <dd>
                                        @php
                                            $ocupacao = $turma->capacidade_maxima > 0 ? ($turma->alunos->count() / $turma->capacidade_maxima) * 100 : 0;
                                        @endphp
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $ocupacao }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 mt-1">{{ number_format($ocupacao, 1) }}% ocupada</span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Seção de Alunos Matriculados -->
                    @if($turma->alunos->count() > 0)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <img src="{{ asset('icons/alunos.svg') }}" alt="Ícone Alunos" class="w-5 h-5 mr-2">
                                Alunos Matriculados ({{ $turma->alunos->count() }})
                            </h3>
                            
                            <div class="space-y-2">
                                @foreach($turma->alunos as $aluno)
                                    <div class="bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow duration-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    @if($aluno->foto_perfil)
                                                        <img class="w-8 h-8 rounded-full object-cover" 
                                                             src="{{ Storage::url($aluno->foto_perfil) }}" 
                                                             alt="{{ $aluno->nome }}">
                                                    @else
                                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $aluno->nome }}</p>
                                                    <p class="text-xs text-gray-500">Matrícula: {{ $aluno->numero_matricula }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('alunos.show', $aluno) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900 text-xs font-medium px-2 py-1 rounded">
                                                    Ver
                                                </a>
                                                <form action="{{ route('turmas.desvincular-aluno', [$turma, $aluno]) }}" method="POST" class="inline" 
                                                      onsubmit="return confirm('Tem certeza que deseja desvincular {{ $aluno->nome }} desta turma?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900 text-xs font-medium px-2 py-1 rounded hover:bg-red-50">
                                                        Desvincular
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="text-center py-8">
                                <img src="{{ asset('icons/alunos.svg') }}" alt="Ícone Alunos" class="mx-auto h-12 w-12 opacity-40">
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum aluno matriculado</h3>
                                <p class="mt-1 text-sm text-gray-500">Esta turma ainda não possui alunos matriculados.</p>
                                <div class="mt-6">
                                    <a href="{{ route('alunos.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Cadastrar Primeiro Aluno
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Seção de Vinculação de Alunos -->
                    @if($alunosDisponiveis->count() > 0)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Vincular Alunos à Turma</h3>
                                <span class="text-sm text-gray-500">{{ $alunosDisponiveis->count() }} aluno(s) disponível(is)</span>
                            </div>
                            
                            @if($errors->has('capacidade'))
                                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $errors->first('capacidade') }}
                                </div>
                            @endif
                            
                            @if($errors->has('alunos_indisponiveis'))
                                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $errors->first('alunos_indisponiveis') }}
                                </div>
                            @endif
                            
                            @if($errors->has('alunos'))
                                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $errors->first('alunos') }}
                                </div>
                            @endif
                            
                            <!-- Campo de Busca -->
                            <div class="mb-4">
                                <input type="text" id="buscaAluno" placeholder="Buscar por nome ou matrícula..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <form action="{{ route('turmas.vincular-alunos', $turma) }}" method="POST" id="vincularAlunosForm">
                                @csrf
                                
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="text-sm font-medium text-gray-700">Selecione os alunos para vincular:</label>
                                        <div class="flex space-x-2">
                                            <button type="button" onclick="selecionarTodos()" 
                                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">Selecionar Todos</button>
                                            <span class="text-gray-300">|</span>
                                            <button type="button" onclick="desmarcarTodos()" 
                                                    class="text-xs text-gray-600 hover:text-gray-800 font-medium">Desmarcar Todos</button>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-1 max-h-80 overflow-y-auto" id="listaAlunos" style="max-height: calc(5 * 3.5rem);">
                                        @foreach($alunosDisponiveis as $aluno)
                                            <label class="aluno-item flex items-center p-2 bg-white rounded border border-gray-200 hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition-colors duration-200" 
                                                   data-nome="{{ strtolower($aluno->nome) }}" data-matricula="{{ $aluno->numero_matricula }}">
                                                <input type="checkbox" name="alunos[]" value="{{ $aluno->id }}" 
                                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <div class="ml-3 flex-1 flex items-center justify-between">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $aluno->nome }}</div>
                                                        <div class="text-xs text-gray-500">Matrícula: {{ $aluno->numero_matricula }}</div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    
                                    <div id="nenhumResultado" class="hidden text-center py-4 text-gray-500 text-sm">
                                        Nenhum aluno encontrado com os critérios de busca.
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-600">
                                        <span id="alunosSelecionados">0</span> aluno(s) selecionado(s)
                                        <span class="mx-2">•</span>
                                        Vagas disponíveis: <span class="font-medium">{{ $turma->capacidade_maxima - $turma->alunos->count() }}</span>
                                    </div>
                                    
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors duration-200"
                                            id="btnVincular" disabled>
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Vincular Alunos Selecionados
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.09"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum aluno disponível</h3>
                                <p class="mt-1 text-sm text-gray-500">Todos os alunos já estão matriculados em turmas ou não há alunos cadastrados.</p>
                                <div class="mt-4">
                                    <a href="{{ route('alunos.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Cadastrar Novo Aluno
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Seção de Ações Rápidas -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ações Rápidas</h3>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('turmas.edit', $turma) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar Turma
                            </a>
                            
                            <a href="{{ route('alunos.create', ['turma_id' => $turma->id]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Matricular Aluno
                            </a>
                            
                            <button type="button" 
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Gerar Relatório
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selecionarTodos() {
            const checkboxes = document.querySelectorAll('input[name="alunos[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            atualizarContador();
        }
        
        function desmarcarTodos() {
            const checkboxes = document.querySelectorAll('input[name="alunos[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            atualizarContador();
        }
        
        function atualizarContador() {
            const checkboxes = document.querySelectorAll('input[name="alunos[]"]:checked');
            const contador = document.getElementById('alunosSelecionados');
            const btnVincular = document.getElementById('btnVincular');
            
            const selecionados = checkboxes.length;
            contador.textContent = selecionados;
            
            // Habilitar/desabilitar botão baseado na seleção
            if (selecionados > 0) {
                btnVincular.disabled = false;
                btnVincular.classList.remove('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
                btnVincular.classList.add('bg-green-600', 'hover:bg-green-700');
            } else {
                btnVincular.disabled = true;
                btnVincular.classList.add('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
                btnVincular.classList.remove('bg-green-600', 'hover:bg-green-700');
            }
        }
        
        // Funcionalidade de busca
        function filtrarAlunos() {
            const busca = document.getElementById('buscaAluno').value.toLowerCase();
            const alunosItems = document.querySelectorAll('.aluno-item');
            const nenhumResultado = document.getElementById('nenhumResultado');
            let alunosVisiveis = 0;
            
            alunosItems.forEach(item => {
                const nome = item.getAttribute('data-nome');
                const matricula = item.getAttribute('data-matricula');
                
                if (nome.includes(busca) || matricula.includes(busca)) {
                    item.style.display = 'flex';
                    alunosVisiveis++;
                } else {
                    item.style.display = 'none';
                    // Desmarcar checkbox se estiver oculto
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox.checked) {
                        checkbox.checked = false;
                    }
                }
            });
            
            // Mostrar/ocultar mensagem de "nenhum resultado"
            if (alunosVisiveis === 0 && busca !== '') {
                nenhumResultado.classList.remove('hidden');
            } else {
                nenhumResultado.classList.add('hidden');
            }
            
            // Atualizar contador após filtrar
            atualizarContador();
        }
        
        // Adicionar event listeners aos checkboxes
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="alunos[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', atualizarContador);
            });
            
            // Event listener para busca
            const buscaInput = document.getElementById('buscaAluno');
            if (buscaInput) {
                buscaInput.addEventListener('input', filtrarAlunos);
            }
            
            // Inicializar contador
            atualizarContador();
        });
    </script>
</x-app-layout>