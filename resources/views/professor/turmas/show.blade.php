<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes da Turma') }} - {{ $turma->nome }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-session-messages />
            
            <!-- Navegação -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('professor.turmas.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <x-heroicon-o-building-office class="w-4 h-4 mr-2" />
                                Minhas Turmas
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-400" />
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $turma->nome }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Informações da Turma -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                <div class="px-6 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                <x-heroicon-o-building-office class="w-8 h-8 text-white" />
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">{{ $turma->nome }}</h1>
                                <div class="flex items-center space-x-4 mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <x-heroicon-o-academic-cap class="w-4 h-4 mr-1" />
                                        {{ $turma->serie }}
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                                        {{ ucfirst($turma->turno) }}
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        Capacidade: {{ $turma->capacidade_maxima }} alunos
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Professor</p>
                            <p class="font-semibold text-gray-900">{{ $professor->nome }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas da Turma -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <x-heroicon-o-users class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total de Alunos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $alunos->count() }}</p>
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
                            <p class="text-2xl font-bold text-gray-900">{{ $disciplinas->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <x-heroicon-o-clipboard-document-check class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ocupação</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format(($alunos->count() / $turma->capacidade_maxima) * 100, 1) }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <x-heroicon-o-calendar-days class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Vagas Restantes</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $turma->capacidade_maxima - $alunos->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disciplinas Ministradas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-book-open class="w-5 h-5 mr-2 text-green-600" />
                        Disciplinas Ministradas
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Disciplinas que você ministra nesta turma</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($disciplinas as $disciplina)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center mr-3">
                                        <x-heroicon-o-book-open class="w-5 h-5 text-white" />
                                    </div>
                                    <div>
                                        <h6 class="font-medium text-gray-900">{{ $disciplina->codigo }}</h6>
                                        <p class="text-sm text-gray-600">{{ $disciplina->nome }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('professor.chamada.fazer', ['turma' => $turma->id, 'disciplina' => $disciplina->id]) }}"
                                       class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md transition-colors text-sm font-medium">
                                        <x-heroicon-o-clipboard-document-check class="w-4 h-4 mr-1" />
                                        Chamada
                                    </a>
                                    
                                    <a href="{{ route('professor.chamadas.gerenciar', ['turma' => $turma->id, 'disciplina' => $disciplina->id]) }}"
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

            <!-- Lista de Alunos -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <x-heroicon-o-users class="w-5 h-5 mr-2 text-blue-600" />
                                Alunos Matriculados
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Lista completa dos alunos matriculados nesta turma</p>
                        </div>
                        <div class="text-sm text-gray-500">
                            <span id="alunos-count">{{ $alunos->count() }}</span> aluno(s) encontrado(s)
                        </div>
                    </div>
                </div>
                
                @if($alunos->count() > 0)
                    <!-- Filtros e Busca -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="space-y-4">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <!-- Campo de Busca -->
                                <div class="flex-1">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input type="text" id="busca-aluno" placeholder="Buscar por nome, email ou matrícula..." 
                                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                </div>
                                
                                <!-- Filtro por Status -->
                                <div class="sm:w-48">
                                    <select id="filtro-status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">Todos os status</option>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>                        
                                    </select>
                                </div>
                                
                                <!-- Botão Limpar Filtros -->
                                <button id="limpar-filtros" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="ordenarTabela('nome')">
                                        Aluno
                                        <span id="sort-nome" class="ml-1 text-gray-400">↕</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="ordenarTabela('matricula')">
                                        Matrícula
                                        <span id="sort-matricula" class="ml-1 text-gray-400">↕</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="ordenarTabela('nascimento')">
                                        Data de Nascimento
                                        <span id="sort-nascimento" class="ml-1 text-gray-400">↕</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="ordenarTabela('status')">
                                         Status
                                         <span id="sort-status" class="ml-1 text-gray-400">↕</span>
                                     </th>
                                     <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                         Desempenho
                                     </th>
                                     <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="ordenarTabela('data_matricula')">
                                         Data de Matrícula
                                         <span id="sort-data_matricula" class="ml-1 text-gray-400">↕</span>
                                     </th>
                                </tr>
                            </thead>
                            <tbody id="tabela-alunos" class="bg-white divide-y divide-gray-200">
                                @foreach($alunos as $aluno)
                                    <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-200 aluno-row" 
                                        onclick="abrirModalAluno({{ $aluno->id }})"
                                        data-nome="{{ strtolower($aluno->nome) }}"
                                        data-email="{{ strtolower($aluno->email) }}"
                                        data-matricula="{{ $aluno->numero_matricula }}"
                                        data-status="ativo"
                                        data-nascimento="{{ $aluno->data_nascimento ? \Carbon\Carbon::parse($aluno->data_nascimento)->format('Y-m-d') : '' }}"
                                        data-data-matricula="{{ $aluno->created_at ? $aluno->created_at->format('Y-m-d') : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center">
                                                    <span class="text-white font-medium text-sm">
                                                        {{ strtoupper(substr($aluno->nome, 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $aluno->nome }}</div>
                                                    <div class="text-sm text-gray-500">{{ $aluno->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $aluno->numero_matricula }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $aluno->data_nascimento ? \Carbon\Carbon::parse($aluno->data_nascimento)->format('d/m/Y') : 'Não informado' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                                Ativo
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <!-- Indicador de Frequência -->
                                                @php
                                                    $frequencia = rand(70, 100); // Simulação - será substituído por dados reais
                                                    $frequenciaClass = $frequencia >= 90 ? 'bg-green-100 text-green-800' : 
                                                                      ($frequencia >= 75 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                                    $frequenciaIcon = $frequencia >= 90 ? '✓' : ($frequencia >= 75 ? '!' : '✗');
                                                @endphp
                                                <div class="flex items-center">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $frequenciaClass }}">
                                                        <span class="mr-1">{{ $frequenciaIcon }}</span>
                                                        {{ $frequencia }}%
                                                    </span>
                                                </div>
                                                
                                                <!-- Indicador de Notas -->
                                                @php
                                                    $media = rand(50, 100) / 10; // Simulação - será substituído por dados reais
                                                    $mediaClass = $media >= 8.0 ? 'bg-blue-100 text-blue-800' : 
                                                                 ($media >= 6.0 ? 'bg-green-100 text-green-800' : 
                                                                 ($media >= 4.0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'));
                                                    $mediaIcon = $media >= 8.0 ? '★' : ($media >= 6.0 ? '●' : ($media >= 4.0 ? '◐' : '○'));
                                                @endphp
                                                <div class="flex items-center">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $mediaClass }}">
                                                        <span class="mr-1">{{ $mediaIcon }}</span>
                                                        {{ number_format($media, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Barra de Progresso Geral -->
                                            @php
                                                $desempenhoGeral = ($frequencia + ($media * 10)) / 2;
                                                $progressClass = $desempenhoGeral >= 85 ? 'bg-green-500' : 
                                                                ($desempenhoGeral >= 70 ? 'bg-yellow-500' : 'bg-red-500');
                                            @endphp
                                            <div class="mt-1">
                                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                    <div class="{{ $progressClass }} h-1.5 rounded-full transition-all duration-300" 
                                                         style="width: {{ $desempenhoGeral }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $aluno->created_at ? $aluno->created_at->format('d/m/Y') : 'Não informado' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mensagem quando nenhum resultado é encontrado -->
                    <div id="sem-resultados" class="hidden p-12 text-center">
                        <div class="text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <p class="text-lg font-medium">Nenhum aluno encontrado</p>
                            <p class="text-sm">Tente ajustar os filtros ou termos de busca</p>
                        </div>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <x-heroicon-o-users class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum aluno matriculado</h3>
                        <p class="text-gray-500">Esta turma ainda não possui alunos matriculados.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Detalhes do Aluno -->
    <div id="modalAlunoDetalhes" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header do Modal -->
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitulo">Detalhes do Aluno</h3>
                    <button onclick="fecharModalAluno()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Loading State -->
                <div id="modalLoading" class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-2 text-gray-600">Carregando dados do aluno...</span>
                </div>

                <!-- Conteúdo do Modal -->
                <div id="modalConteudo" class="hidden">
                    <!-- Informações Pessoais -->
                    <div class="mt-4">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 h-16 w-16 mr-4">
                                <img id="modalFotoAluno" class="h-16 w-16 rounded-full object-cover" src="" alt="">
                            </div>
                            <div>
                                <h4 id="modalNomeAluno" class="text-xl font-semibold text-gray-900"></h4>
                                <p id="modalEmailAluno" class="text-gray-600"></p>
                                <p id="modalMatriculaAluno" class="text-sm text-gray-500"></p>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8">
                                <button onclick="mostrarTab('informacoes')" id="tabInformacoes" class="tab-button py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">Informações</button>
                                <button onclick="mostrarTab('frequencia')" id="tabFrequencia" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">Frequência</button>
                                <button onclick="mostrarTab('avaliacoes')" id="tabAvaliacoes" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">Avaliações</button>
                            </nav>
                        </div>

                        <!-- Conteúdo das Tabs -->
                        <div class="mt-4">
                            <!-- Tab Informações -->
                            <div id="tabConteudoInformacoes" class="tab-content">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                                        <p id="modalDataNascimento" class="mt-1 text-sm text-gray-900"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Idade</label>
                                        <p id="modalIdade" class="mt-1 text-sm text-gray-900"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Telefone</label>
                                        <p id="modalTelefone" class="mt-1 text-sm text-gray-900"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <p id="modalStatus" class="mt-1 text-sm text-gray-900"></p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Endereço</label>
                                        <p id="modalEndereco" class="mt-1 text-sm text-gray-900"></p>
                                    </div>
                                </div>

                                <!-- Estatísticas de Frequência Geral -->
                                <div class="mt-6">
                                    <h5 class="text-lg font-medium text-gray-900 mb-3">Estatísticas Gerais</h5>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="bg-blue-50 p-3 rounded-lg">
                                            <div class="text-2xl font-bold text-blue-600" id="modalTotalChamadas">0</div>
                                            <div class="text-sm text-blue-600">Total de Chamadas</div>
                                        </div>
                                        <div class="bg-green-50 p-3 rounded-lg">
                                            <div class="text-2xl font-bold text-green-600" id="modalTotalPresencas">0</div>
                                            <div class="text-sm text-green-600">Presenças</div>
                                        </div>
                                        <div class="bg-red-50 p-3 rounded-lg">
                                            <div class="text-2xl font-bold text-red-600" id="modalTotalFaltas">0</div>
                                            <div class="text-sm text-red-600">Faltas</div>
                                        </div>
                                        <div class="bg-purple-50 p-3 rounded-lg">
                                            <div class="text-2xl font-bold text-purple-600" id="modalPercentualFrequencia">0%</div>
                                            <div class="text-sm text-purple-600">Frequência</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Frequência -->
                            <div id="tabConteudoFrequencia" class="tab-content hidden">
                                <div class="mb-4">
                                    <h5 class="text-lg font-medium text-gray-900 mb-3">Últimas Presenças</h5>
                                    <div id="listaUltimasPresencas" class="space-y-2">
                                        <!-- Será preenchido via JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Avaliações -->
                            <div id="tabConteudoAvaliacoes" class="tab-content hidden">
                                <div class="mb-4">
                                    <h5 class="text-lg font-medium text-gray-900 mb-3">Avaliações</h5>
                                    <div id="listaAvaliacoes" class="space-y-3">
                                        <!-- Será preenchido via JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer do Modal -->
                <div class="flex justify-end pt-4 border-t mt-6">
                    <button onclick="fecharModalAluno()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let alunoAtual = null;
        let ordenacaoAtual = { campo: null, direcao: 'asc' };

        function abrirModalAluno(alunoId) {
            alunoAtual = alunoId;
            document.getElementById('modalAlunoDetalhes').classList.remove('hidden');
            document.getElementById('modalLoading').classList.remove('hidden');
            document.getElementById('modalConteudo').classList.add('hidden');
            
            // Resetar tabs
            mostrarTab('informacoes');
            
            // Carregar dados do aluno
            carregarDadosAluno(alunoId);
        }

        function fecharModalAluno() {
            document.getElementById('modalAlunoDetalhes').classList.add('hidden');
            alunoAtual = null;
        }

        function mostrarTab(tabName) {
            // Esconder todas as tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Resetar estilos dos botões
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Mostrar tab ativa
            document.getElementById('tabConteudo' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).classList.remove('hidden');
            
            // Ativar botão da tab
            const activeButton = document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1));
            activeButton.classList.add('border-blue-500', 'text-blue-600');
            activeButton.classList.remove('border-transparent', 'text-gray-500');
        }

        async function carregarDadosAluno(alunoId) {
            try {
                const response = await fetch(`/professor/api/alunos/${alunoId}`);
                
                if (!response.ok) {
                    throw new Error('Erro ao carregar dados do aluno');
                }
                
                const data = await response.json();
                
                if (data.success) {
                    preencherModalAluno(data.data);
                    document.getElementById('modalLoading').classList.add('hidden');
                    document.getElementById('modalConteudo').classList.remove('hidden');
                } else {
                    throw new Error('Erro nos dados retornados');
                }
            } catch (error) {
                console.error('Erro ao carregar dados do aluno:', error);
                alert('Erro ao carregar dados do aluno. Tente novamente.');
                fecharModalAluno();
            }
        }

        function preencherModalAluno(aluno) {
            // Informações básicas
            document.getElementById('modalTitulo').textContent = `Detalhes - ${aluno.nome}`;
            document.getElementById('modalNomeAluno').textContent = aluno.nome;
            document.getElementById('modalEmailAluno').textContent = aluno.email;
            document.getElementById('modalMatriculaAluno').textContent = `Matrícula: ${aluno.numero_matricula}`;
            
            // Foto do aluno
            const fotoElement = document.getElementById('modalFotoAluno');
            if (aluno.foto_perfil_url) {
                fotoElement.src = aluno.foto_perfil_url;
                fotoElement.alt = aluno.nome;
            } else {
                fotoElement.src = 'data:image/svg+xml;base64,' + btoa(`<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"><rect width="64" height="64" fill="#6B7280"/><text x="32" y="40" font-family="Arial" font-size="24" fill="white" text-anchor="middle">${aluno.nome.substring(0, 2).toUpperCase()}</text></svg>`);
                fotoElement.alt = aluno.nome;
            }
            
            // Informações pessoais
            document.getElementById('modalDataNascimento').textContent = aluno.data_nascimento || 'Não informado';
            document.getElementById('modalIdade').textContent = aluno.idade ? `${aluno.idade} anos` : 'Não informado';
            document.getElementById('modalTelefone').textContent = aluno.telefone || 'Não informado';
            document.getElementById('modalStatus').textContent = aluno.status_matricula || 'Não informado';
            document.getElementById('modalEndereco').textContent = aluno.endereco || 'Não informado';
            
            // Estatísticas de frequência
            const stats = aluno.estatisticas_frequencia;
            document.getElementById('modalTotalChamadas').textContent = stats.total_chamadas;
            document.getElementById('modalTotalPresencas').textContent = stats.total_presencas;
            document.getElementById('modalTotalFaltas').textContent = stats.total_faltas;
            document.getElementById('modalPercentualFrequencia').textContent = `${stats.percentual_frequencia}%`;
            
            // Últimas presenças
            const listaPresencas = document.getElementById('listaUltimasPresencas');
            listaPresencas.innerHTML = '';
            
            if (aluno.ultimas_presencas && aluno.ultimas_presencas.length > 0) {
                aluno.ultimas_presencas.forEach(presenca => {
                    const div = document.createElement('div');
                    div.className = 'flex justify-between items-center p-3 bg-gray-50 rounded-lg';
                    
                    const statusClass = presenca.status === 'presente' ? 'text-green-600' : 'text-red-600';
                    const statusIcon = presenca.status === 'presente' ? '✓' : '✗';
                    
                    div.innerHTML = `
                        <div>
                            <div class="font-medium">${presenca.disciplina}</div>
                            <div class="text-sm text-gray-500">${presenca.data}</div>
                        </div>
                        <div class="${statusClass} font-medium">
                            ${statusIcon} ${presenca.status.charAt(0).toUpperCase() + presenca.status.slice(1)}
                        </div>
                    `;
                    
                    listaPresencas.appendChild(div);
                });
            } else {
                listaPresencas.innerHTML = '<p class="text-gray-500 text-center py-4">Nenhuma chamada registrada ainda.</p>';
            }
        }

        // Função de busca e filtro
         function filtrarAlunos() {
             const busca = document.getElementById('busca-aluno').value.toLowerCase();
             const statusFiltro = document.getElementById('filtro-status').value.toLowerCase();
             const linhas = document.querySelectorAll('.aluno-row');
             let alunosVisiveis = 0;

             linhas.forEach(linha => {
                 const nome = linha.dataset.nome;
                 const email = linha.dataset.email;
                 const matricula = linha.dataset.matricula;
                 const status = linha.dataset.status;

                 const matchBusca = !busca || 
                     nome.includes(busca) || 
                     email.includes(busca) || 
                     matricula.includes(busca);

                 const matchStatus = !statusFiltro || status === statusFiltro;

                 if (matchBusca && matchStatus) {
                     linha.style.display = '';
                     alunosVisiveis++;
                 } else {
                     linha.style.display = 'none';
                 }
             });

             // Atualizar contador
             document.getElementById('alunos-count').textContent = alunosVisiveis;

             // Mostrar/esconder mensagem de sem resultados
             const semResultados = document.getElementById('sem-resultados');
             const tabelaContainer = document.querySelector('#tabela-alunos').closest('.overflow-x-auto');
             
             if (alunosVisiveis === 0 && document.querySelectorAll('.aluno-row').length > 0) {
                 tabelaContainer.style.display = 'none';
                 semResultados.classList.remove('hidden');
             } else {
                 tabelaContainer.style.display = '';
                 semResultados.classList.add('hidden');
             }
         }

         // Função de ordenação
         function ordenarTabela(campo) {
             const tabela = document.getElementById('tabela-alunos');
             const linhas = Array.from(tabela.querySelectorAll('.aluno-row'));
             
             // Determinar direção da ordenação
             if (ordenacaoAtual.campo === campo) {
                 ordenacaoAtual.direcao = ordenacaoAtual.direcao === 'asc' ? 'desc' : 'asc';
             } else {
                 ordenacaoAtual.campo = campo;
                 ordenacaoAtual.direcao = 'asc';
             }

             // Atualizar indicadores visuais
             document.querySelectorAll('[id^="sort-"]').forEach(el => {
                 el.textContent = '↕';
                 el.className = 'ml-1 text-gray-400';
             });
             
             const indicador = document.getElementById(`sort-${campo}`);
             if (indicador) {
                 indicador.textContent = ordenacaoAtual.direcao === 'asc' ? '↑' : '↓';
                 indicador.className = 'ml-1 text-blue-600';
             }

             // Ordenar linhas
             linhas.sort((a, b) => {
                 let valorA, valorB;
                 
                 switch(campo) {
                     case 'nome':
                         valorA = a.dataset.nome || '';
                         valorB = b.dataset.nome || '';
                         break;
                     case 'matricula':
                         valorA = a.dataset.matricula || '';
                         valorB = b.dataset.matricula || '';
                         break;
                     case 'nascimento':
                         valorA = a.dataset.nascimento || '0000-00-00';
                         valorB = b.dataset.nascimento || '0000-00-00';
                         break;
                     case 'status':
                         valorA = a.dataset.status || '';
                         valorB = b.dataset.status || '';
                         break;
                     case 'data_matricula':
                         valorA = a.dataset.dataMatricula || '0000-00-00';
                         valorB = b.dataset.dataMatricula || '0000-00-00';
                         break;
                     default:
                         return 0;
                 }

                 if (ordenacaoAtual.direcao === 'asc') {
                     return valorA.localeCompare(valorB);
                 } else {
                     return valorB.localeCompare(valorA);
                 }
             });

             // Reordenar no DOM
             linhas.forEach(linha => tabela.appendChild(linha));
         }

         // Event listeners
         document.addEventListener('DOMContentLoaded', function() {
             // Verificar se os elementos existem antes de adicionar listeners
             const buscaInput = document.getElementById('busca-aluno');
             const filtroStatus = document.getElementById('filtro-status');
             const limparBtn = document.getElementById('limpar-filtros');
             
             if (buscaInput) {
                 buscaInput.addEventListener('input', filtrarAlunos);
             }
             
             if (filtroStatus) {
                 filtroStatus.addEventListener('change', filtrarAlunos);
             }
             
             if (limparBtn) {
                 limparBtn.addEventListener('click', function() {
                     if (buscaInput) buscaInput.value = '';
                     if (filtroStatus) filtroStatus.value = '';
                     filtrarAlunos();
                 });
             }
         });

        // Fechar modal ao clicar fora dele
        document.getElementById('modalAlunoDetalhes').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalAluno();
            }
        });

        // Fechar modal com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && alunoAtual) {
                fecharModalAluno();
            }
        });
    </script>
</x-app-layout>