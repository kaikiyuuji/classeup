<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Detalhes do Aluno
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Visualize e gerencie as informações do aluno {{ $aluno->nome }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('alunos.edit', $aluno) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('alunos.index') }}" 
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

                    <!-- Cabeçalho do Perfil -->
                    <div class="flex flex-col lg:flex-row items-center lg:items-start space-y-6 lg:space-y-0 lg:space-x-8 mb-8">
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
                        <div class="flex-1 text-center lg:text-left">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $aluno->nome }}</h1>
                            <div class="flex flex-col sm:flex-row items-center lg:items-start space-y-2 sm:space-y-0 sm:space-x-4 text-gray-600">
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
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
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
                                    <dd class="text-sm text-gray-900 font-mono">{{ $aluno->cpf }}</dd>
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
                                            <a href="tel:{{ $aluno->telefone }}" class="text-blue-600 hover:text-blue-800 transition-colors">
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
                                            <a href="{{ route('turmas.show', $aluno->turma) }}" 
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

                    <!-- Ações -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                            <!-- Botão de Exclusão -->
                            <form action="{{ route('alunos.destroy', $aluno) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Tem certeza que deseja excluir este aluno? Esta ação não pode ser desfeita.')">
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
                                <a href="{{ route('alunos.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Voltar à Lista
                                </a>
                                <a href="{{ route('alunos.edit', $aluno) }}" 
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
</x-app-layout>