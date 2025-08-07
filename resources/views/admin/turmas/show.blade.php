<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Detalhes da Turma
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Visualize e gerencie as informações da turma {{ $turma->nome }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('turmas.edit', $turma) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                    <x-icons.edit class="w-4 h-4 mr-2" />
                    Editar
                </a>
                <a href="{{ route('turmas.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                    <x-icons.back class="w-4 h-4 mr-2" />
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-8">
            <!-- Mensagem de Sucesso -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                    <x-icons.check-circle class="w-5 h-5 mr-2" />
                    {{ session('success') }}
                </div>
            @endif

            <!-- Card Principal -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-8">

                    <!-- Cabeçalho da Turma -->
                    <div class="flex items-start space-x-8 mb-8">


                        <!-- Informações Principais -->
                        <div class="flex-1 text-left">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $turma->nome }}</h1>
                            <div class="flex items-start space-x-4 text-gray-600">
                                <div class="flex items-center">
                                    <span>{{ App\Models\Turma::getNiveisEducacionais()[$turma->serie] ?? $turma->serie }}</span>
                                </div>
                                <div class="flex items-center">
                                    <x-icons.clock class="w-4 h-4 mr-2" />
                                    <span>{{ ucfirst($turma->turno) }}</span>
                                </div>

                            </div>
                            <div class="mt-3 text-sm text-gray-500">
                                Criada em {{ $turma->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Informações Detalhadas -->
                    <div class="grid grid-cols-2 gap-8">
                        <!-- Informações da Turma -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-icons.book class="w-5 h-5 mr-2 text-indigo-600" />
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
                                    <dt class="text-sm font-medium text-gray-600">Nível Educacional</dt>
                        <dd class="text-sm text-gray-900">{{ App\Models\Turma::getNiveisEducacionais()[$turma->serie] ?? $turma->serie }}</dd>
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
                                <x-icons.users class="w-5 h-5 mr-2 text-indigo-600" />
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
                                        <x-icons.plus class="w-4 h-4 mr-2" />
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
                                    <x-icons.exclamation class="w-5 h-5 mr-2 text-red-600" />
                                    {{ $errors->first('capacidade') }}
                                </div>
                            @endif
                            
                            @if($errors->has('alunos_indisponiveis'))
                                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                                    <x-icons.exclamation class="w-5 h-5 mr-2 text-red-600" />
                                    {{ $errors->first('alunos_indisponiveis') }}
                                </div>
                            @endif
                            
                            @if($errors->has('alunos'))
                                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                                    <x-icons.exclamation class="w-5 h-5 mr-2 text-red-600" />
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
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors duration-200"
                                            id="btnVincular" disabled>
                                        Vincular Alunos Selecionados
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="text-center py-6">
                                <img src="{{ asset('icons/alunos.svg') }}" alt="Ícone Alunos" class="mx-auto h-12 w-12 text-gray-400">  
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum aluno disponível</h3>
                                <p class="mt-1 text-sm text-gray-500">Todos os alunos já estão matriculados em turmas ou não há alunos cadastrados.</p>
                                <div class="mt-4">
                                    <a href="{{ route('alunos.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <x-icons.plus class="w-4 h-4 mr-2" />
                                        Cadastrar Novo Aluno
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Seção de Professores Vinculados -->
                    @if($turma->professores->count() > 0)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-icons.academic-cap class="w-5 h-5 mr-2 text-indigo-600" />
                                Professores Vinculados ({{ $turma->professores->count() }})
                            </h3>
                            
                            <div class="space-y-3">
                                @foreach($turma->professores as $professor)
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                        <x-icons.user class="w-5 h-5 text-blue-600" />
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $professor->nome }}</p>
                                                    <p class="text-xs text-gray-500">{{ $professor->email }}</p>
                                                    @if($professor->pivot->disciplina_id)
                                                        @php
                                                            $disciplina = $turma->disciplinas->where('id', $professor->pivot->disciplina_id)->first();
                                                        @endphp
                                                        @if($disciplina)
                                                            <p class="text-xs text-blue-600 font-medium mt-1">Disciplina: {{ $disciplina->nome }}</p>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('professores.show', $professor) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900 text-xs font-medium px-2 py-1 rounded">
                                                    Ver
                                                </a>
                                                @if($professor->pivot->disciplina_id)
                                                    <form action="{{ route('turmas.desvincular-professor', $turma) }}" method="POST" class="inline" 
                                                          onsubmit="return confirm('Tem certeza que deseja desvincular {{ $professor->nome }} desta turma?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="professor_id" value="{{ $professor->id }}">
                                                        <input type="hidden" name="disciplina_id" value="{{ $professor->pivot->disciplina_id }}">
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900 text-xs font-medium px-2 py-1 rounded hover:bg-red-50">
                                                            Desvincular
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="text-center py-8">
                                <x-icons.academic-cap class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum professor vinculado</h3>
                                <p class="mt-1 text-sm text-gray-500">Esta turma ainda não possui professores vinculados.</p>
                                <div class="mt-6">
                                    <a href="{{ route('professores.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <x-icons.plus class="w-4 h-4 mr-2" />
                                        Cadastrar Primeiro Professor
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Seção de Vinculação de Professores -->
                    @if($professoresDisponiveis->count() > 0)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Vincular Professor à Turma</h3>
                                <span class="text-sm text-gray-500">{{ $professoresDisponiveis->count() }} professor(es) disponível(is)</span>
                            </div>
                            
                            @if($errors->has('professor_id'))
                                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                                    <x-icons.exclamation class="w-5 h-5 mr-2 text-red-600" />
                                    {{ $errors->first('professor_id') }}
                                </div>
                            @endif
                            
                            @if($errors->has('disciplina_id'))
                                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                                    <x-icons.exclamation class="w-5 h-5 mr-2 text-red-600" />
                                    {{ $errors->first('disciplina_id') }}
                                </div>
                            @endif
                            
                            <form action="{{ route('turmas.vincular-professor', $turma) }}" method="POST" id="vincularProfessorForm">
                                @csrf
                                
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Seleção de Professor -->
                                        <div>
                                            <label for="professor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                                Selecione o Professor:
                                            </label>
                                            <select name="professor_id" id="professor_id" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    onchange="atualizarDisciplinas()" required>
                                                <option value="">Escolha um professor...</option>
                                                @foreach($professoresDisponiveis as $professor)
                                                    <option value="{{ $professor->id }}" 
                                                            data-disciplinas="{{ $professor->disciplinas->pluck('id')->toJson() }}"
                                                            {{ old('professor_id') == $professor->id ? 'selected' : '' }}>
                                                        {{ $professor->nome }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <!-- Seleção de Disciplina -->
                                        <div>
                                            <label for="disciplina_id" class="block text-sm font-medium text-gray-700 mb-2">
                                                Selecione a Disciplina:
                                            </label>
                                            <select name="disciplina_id" id="disciplina_id" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    required disabled>
                                                <option value="">Primeiro selecione um professor...</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Lista de disciplinas para JavaScript -->
                                    <script type="application/json" id="disciplinasData">
                                        {
                                            @foreach($professoresDisponiveis as $professor)
                                                "{{ $professor->id }}": [
                                                    @foreach($professor->disciplinas as $disciplina)
                                                        {
                                                            "id": {{ $disciplina->id }},
                                                            "nome": "{{ $disciplina->nome }}"
                                                        }@if(!$loop->last),@endif
                                                    @endforeach
                                                ]@if(!$loop->last),@endif
                                            @endforeach
                                        }
                                    </script>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-600">
                                        <x-icons.info class="w-4 h-4 inline mr-1" />
                                        Apenas disciplinas que o professor leciona serão exibidas
                                    </div>
                                    
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors duration-200"
                                            id="btnVincularProfessor" disabled>
                                        <x-icons.plus class="w-4 h-4 mr-2" />
                                        Vincular Professor
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="text-center py-6">
                                <x-icons.user class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum professor disponível</h3>
                                <p class="mt-1 text-sm text-gray-500">Todos os professores já estão vinculados ou não há professores cadastrados.</p>
                                <div class="mt-4">
                                    <a href="{{ route('professores.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <x-icons.plus class="w-4 h-4 mr-2" />
                                        Cadastrar Novo Professor
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
                        <x-icons.edit class="w-4 h-4 mr-2" />
                        Editar Turma
                    </a>
                            
                            <a href="{{ route('alunos.create', ['turma_id' => $turma->id]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <x-icons.plus class="w-4 h-4 mr-2" />
                        Matricular Aluno
                    </a>
                            

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
        
        // Função para atualizar disciplinas baseado no professor selecionado
        function atualizarDisciplinas() {
            const professorSelect = document.getElementById('professor_id');
            const disciplinaSelect = document.getElementById('disciplina_id');
            const btnVincular = document.getElementById('btnVincularProfessor');
            
            // Limpar disciplinas
            disciplinaSelect.innerHTML = '<option value="">Selecione uma disciplina...</option>';
            disciplinaSelect.disabled = true;
            btnVincular.disabled = true;
            
            if (professorSelect.value) {
                // Obter dados das disciplinas do JSON
                const disciplinasData = JSON.parse(document.getElementById('disciplinasData').textContent);
                const disciplinasProfessor = disciplinasData[professorSelect.value] || [];
                
                if (disciplinasProfessor.length > 0) {
                    disciplinaSelect.disabled = false;
                    
                    disciplinasProfessor.forEach(disciplina => {
                        const option = document.createElement('option');
                        option.value = disciplina.id;
                        option.textContent = disciplina.nome;
                        disciplinaSelect.appendChild(option);
                    });
                } else {
                    disciplinaSelect.innerHTML = '<option value="">Este professor não possui disciplinas cadastradas</option>';
                }
            }
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
            
            // Event listener para disciplina select
            const disciplinaSelect = document.getElementById('disciplina_id');
            const btnVincular = document.getElementById('btnVincularProfessor');
            
            if (disciplinaSelect && btnVincular) {
                disciplinaSelect.addEventListener('change', function() {
                    btnVincular.disabled = !this.value;
                });
            }
            
            // Inicializar contador
            atualizarContador();
        });
    </script>
</x-app-layout>