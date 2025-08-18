<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Detalhes do Aluno
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Visualize e gerencie as informações do aluno {{ $aluno->nome }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.alunos.boletim', $aluno) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Boletim
                    </a>
                    <a href="{{ route('admin.alunos.edit', $aluno) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                <a href="{{ route('admin.alunos.index') }}" 
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
        <div class="max-w-6xl mx-auto px-8">
            

            <!-- Card Principal -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-8">

                    <!-- Cabeçalho do Perfil -->
                    <div class="flex items-start space-x-8 mb-8">
                        <!-- Foto de Perfil -->
                        <div class="flex-shrink-0">
                            <div class="relative">
                                @if($aluno->foto_perfil_url)
                    <img src="{{ $aluno->foto_perfil_url }}"
                         alt="Foto de {{ $aluno->nome }}"
                                         class="w-40 h-40 rounded-full object-cover border-4 border-white shadow-lg">
                                @else
                                    <div class="w-40 h-40 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center border-4 border-white shadow-lg">
                                        <span class="text-4xl font-bold text-white">
                                            {{ strtoupper(substr($aluno->nome, 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                                
                                <!-- Badge de Status -->
                                
                            </div>
                        </div>

                        <!-- Informações Principais -->
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $aluno->nome }}</h1>
                            <div class="flex items-start space-x-4 text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <a href="mailto:{{ $aluno->email }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                        {{ $aluno->email }}
                                    </a>
                                </div>
                                @if($aluno->telefone)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        <a href="tel:{{ $aluno->telefone }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                            {{ $aluno->telefone }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3 text-sm text-gray-500">
                                Cadastrado em {{ $aluno->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Informações Detalhadas -->
                    <div class="grid grid-cols-2 gap-8">
                        <!-- Informações Pessoais -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informações Pessoais
                            </h3>
                            
                            <dl class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Nome Completo</dt>
                                    <dd class="text-sm text-gray-900 font-medium">{{ $aluno->nome }}</dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Número de Matrícula</dt>
                                    <dd class="text-sm text-gray-900 font-mono font-semibold text-blue-600">{{ $aluno->numero_matricula }}</dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Data de Matrícula</dt>
                                    <dd class="text-sm text-gray-900">{{ $aluno->data_matricula?->format('d/m/Y') }}</dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Status da Matrícula</dt>
                                    <dd>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $aluno->isMatriculaAtiva() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($aluno->status_matricula) }}
                                        </span>
                                    </dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">CPF</dt>
                                    <dd class="text-sm text-gray-900 font-mono" data-format="cpf">{{ $aluno->cpf }}</dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Data de Nascimento</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $aluno->data_nascimento?->format('d/m/Y') }}
                                        @if($aluno->data_nascimento)
                                            <span class="text-gray-500 ml-1">
                                                ({{ $aluno->data_nascimento->age }} anos)
                                            </span>
                                        @endif
                                    </dd>
                                </div>


                            </dl>
                        </div>

                        <!-- Informações de Contato -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Informações de Contato
                            </h3>
                            
                            <dl class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Email</dt>
                                    <dd class="text-sm">
                                        <a href="mailto:{{ $aluno->email }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                            {{ $aluno->email }}
                                        </a>
                                    </dd>
                                </div>

                                @if($aluno->telefone)
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <dt class="text-sm font-medium text-gray-600">Telefone</dt>
                                        <dd class="text-sm">
                                            <a href="tel:{{ $aluno->telefone }}" class="text-blue-600 hover:text-blue-800 transition-colors" data-format="phone">
                                                {{ $aluno->telefone }}
                                            </a>
                                        </dd>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center py-2">
                                    <dt class="text-sm font-medium text-gray-600">Cadastrado em</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $aluno->created_at->format('d/m/Y H:i') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($aluno->endereco)
                        <!-- Endereço -->
                        <div class="mt-8">
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Endereço
                                </h3>
                                <p class="text-gray-700 leading-relaxed">{{ $aluno->endereco }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Turma -->
                    <div class="mt-8">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Turma
                            </h3>
                            
                            @if($aluno->turma)
                                <div class="p-4 bg-white rounded-lg border border-gray-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-semibold text-gray-900">{{ $aluno->turma->nome }}</h4>
                                            <div class="flex items-center space-x-4 text-xs text-gray-500 mt-1">
                                                <span>{{ App\Models\Turma::getNiveisEducacionais()[$aluno->turma->serie] ?? $aluno->turma->serie }}</span>
                                                <span>•</span>
                                                <span>{{ ucfirst($aluno->turma->turno) }}</span>
                                                <span>•</span>
                                                <span>{{ $aluno->turma->ano_letivo }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.turmas.show', $aluno->turma) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-md transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Ver Turma
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <p class="text-gray-500 text-sm">Este aluno não está vinculado a nenhuma turma.</p>
                                </div>
                            @endif
                            

                        </div>
                    </div>

                    <!-- Controle de Chamadas -->
                    <div class="mt-8">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Controle de Chamadas
                            </h3>
                            
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div class="bg-white rounded-lg p-4 border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors" onclick="abrirModalPresencas()">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $totalPresencas }}</p>
                                            <p class="text-xs text-gray-500">Total de Presenças</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $faltasParaJustificar->count() }}</p>
                                            <p class="text-xs text-gray-500">Faltas para Justificar</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <a href="{{ route('admin.chamadas.relatorio-aluno', ['matricula' => $aluno->numero_matricula]) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                                Ver Relatório
                                            </a>
                                            <p class="text-xs text-gray-500">Relatório Completo</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($faltasParaJustificar->isNotEmpty())
                                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-4">
                                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                        <h4 class="text-sm font-semibold text-gray-800">Faltas para Justificar</h4>
                                    </div>
                                    <div class="divide-y divide-gray-200">
                                        @foreach($faltasParaJustificar as $falta)
                                            <div class="px-4 py-3">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <div class="flex items-center text-sm">
                                                            <span class="font-medium text-gray-900">{{ $falta->data_chamada->format('d/m/Y') }}</span>
                                                            <span class="mx-2 text-gray-400">•</span>
                                                            <span class="text-gray-600">{{ $falta->disciplina->nome }}</span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1">Prof. {{ $falta->professor->nome }}</p>
                                                    </div>
                                                    <div class="ml-4">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            Não Justificada
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="bg-white rounded-lg border border-gray-200 p-6 text-center">
                                    <svg class="w-12 h-12 mx-auto text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <p class="text-gray-500 text-sm">Nenhuma falta pendente de justificativa.</p>
                                </div>
                            @endif

                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <!-- Botão de Exclusão -->
                            <form action="{{ route('admin.alunos.destroy', $aluno) }}" 
                                  method="POST" 
                                  onsubmit="return handleDeleteConfirm(event, 'Tem certeza que deseja excluir este aluno?', 'Esta ação não pode ser desfeita.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Excluir Aluno
                                </button>
                            </form>

                            <!-- Botões de Ação -->
                            <div class="flex space-x-3">
                                <a href="{{ route('admin.alunos.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Voltar à Lista
                                </a>
                                <a href="{{ route('admin.alunos.edit', $aluno) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar Aluno
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Presenças -->
    <div id="modalPresencas" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Cabeçalho do Modal -->
                <div class="flex justify-between items-center pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Presenças de {{ $aluno->nome }}</h3>
                    <button onclick="fecharModalPresencas()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Filtros -->
                <div class="py-4 border-b">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                            <input type="date" id="filtroDataInicio" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                            <input type="date" id="filtroDataFim" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Disciplina</label>
                            <select id="filtroDisciplina" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Todas as disciplinas</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button onclick="filtrarPresencas()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Filtrar
                        </button>
                        <button onclick="limparFiltros()" class="ml-2 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Limpar
                        </button>
                    </div>
                </div>

                <!-- Lista de Presenças -->
                <div class="py-4">
                    <div id="listaPresencas" class="space-y-2">
                        <!-- Presenças serão carregadas aqui via AJAX -->
                    </div>
                    
                    <!-- Loading -->
                    <div id="loadingPresencas" class="text-center py-4 hidden">
                        <div class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Carregando presenças...
                        </div>
                    </div>
                    
                    <!-- Paginação -->
                    <div id="paginacaoPresencas" class="mt-4 flex justify-center">
                        <!-- Paginação será inserida aqui -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let paginaAtual = 1;
        let disciplinas = [];
        
        function abrirModalPresencas() {
            document.getElementById('modalPresencas').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            carregarDisciplinas();
            carregarPresencas();
        }
        
        function fecharModalPresencas() {
            document.getElementById('modalPresencas').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function carregarDisciplinas() {
            console.log('Carregando disciplinas...');
            fetch(`{{ route('admin.alunos.disciplinas', $aluno) }}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Dados recebidos:', data);
                    const select = document.getElementById('filtroDisciplina');
                    select.innerHTML = '<option value="">Todas as disciplinas</option>';
                    
                    if (data.disciplinas && data.disciplinas.length > 0) {
                        data.disciplinas.forEach(disciplina => {
                            const option = document.createElement('option');
                            option.value = disciplina.id;
                            option.textContent = disciplina.nome;
                            select.appendChild(option);
                        });
                        console.log(`${data.disciplinas.length} disciplinas carregadas`);
                    } else {
                        console.log('Nenhuma disciplina encontrada');
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Nenhuma disciplina encontrada';
                        select.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar disciplinas:', error);
                    const select = document.getElementById('filtroDisciplina');
                    select.innerHTML = '<option value="">Erro ao carregar disciplinas</option>';
                });
        }
        
        function carregarPresencas(pagina = 1) {
            const loadingElement = document.getElementById('loadingPresencas');
            const listaElement = document.getElementById('listaPresencas');
            
            loadingElement.classList.remove('hidden');
            
            const dataInicio = document.getElementById('filtroDataInicio').value;
            const dataFim = document.getElementById('filtroDataFim').value;
            const disciplinaId = document.getElementById('filtroDisciplina').value;
            
            const params = new URLSearchParams({
                page: pagina,
                ...(dataInicio && { data_inicio: dataInicio }),
                ...(dataFim && { data_fim: dataFim }),
                ...(disciplinaId && { disciplina_id: disciplinaId })
            });
            
            fetch(`{{ route('admin.alunos.presencas', $aluno) }}?${params}`)
                .then(response => response.json())
                .then(data => {
                    loadingElement.classList.add('hidden');
                    renderizarPresencas(data.presencas);
                    renderizarPaginacao(data.pagination);
                })
                .catch(error => {
                    loadingElement.classList.add('hidden');
                    console.error('Erro ao carregar presenças:', error);
                    listaElement.innerHTML = '<p class="text-red-600 text-center">Erro ao carregar presenças.</p>';
                });
        }
        
        function renderizarPresencas(presencas) {
            const listaElement = document.getElementById('listaPresencas');
            
            if (presencas.length === 0) {
                listaElement.innerHTML = '<p class="text-gray-500 text-center py-4">Nenhuma presença encontrada.</p>';
                return;
            }
            
            const html = presencas.map(presenca => {
                const data = new Date(presenca.data_chamada).toLocaleDateString('pt-BR');
                return `
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-900">${data}</div>
                            <div class="text-sm text-gray-600">${presenca.disciplina.nome}</div>
                            <div class="text-xs text-gray-500">Prof. ${presenca.professor.nome}</div>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Presente
                            </span>
                        </div>
                    </div>
                `;
            }).join('');
            
            listaElement.innerHTML = html;
        }
        
        function renderizarPaginacao(pagination) {
            const paginacaoElement = document.getElementById('paginacaoPresencas');
            
            if (pagination.last_page <= 1) {
                paginacaoElement.innerHTML = '';
                return;
            }
            
            let html = '<div class="flex space-x-2">';
            
            // Botão anterior
            if (pagination.current_page > 1) {
                html += `<button onclick="carregarPresencas(${pagination.current_page - 1})" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Anterior</button>`;
            }
            
            // Números das páginas
            for (let i = 1; i <= pagination.last_page; i++) {
                const isActive = i === pagination.current_page;
                const classes = isActive ? 'px-3 py-1 bg-blue-600 text-white rounded' : 'px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300';
                html += `<button onclick="carregarPresencas(${i})" class="${classes}">${i}</button>`;
            }
            
            // Botão próximo
            if (pagination.current_page < pagination.last_page) {
                html += `<button onclick="carregarPresencas(${pagination.current_page + 1})" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Próximo</button>`;
            }
            
            html += '</div>';
            paginacaoElement.innerHTML = html;
        }
        
        function filtrarPresencas() {
            paginaAtual = 1;
            carregarPresencas();
        }
        
        function limparFiltros() {
            document.getElementById('filtroDataInicio').value = '';
            document.getElementById('filtroDataFim').value = '';
            document.getElementById('filtroDisciplina').value = '';
            paginaAtual = 1;
            carregarPresencas();
        }
        
        // Fechar modal ao clicar fora
        document.getElementById('modalPresencas').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalPresencas();
            }
        });
    </script>
</x-app-layout>