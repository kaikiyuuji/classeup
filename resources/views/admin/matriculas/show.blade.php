<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    Detalhes da Matrícula
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $matricula->aluno->nome }} - {{ $matricula->turma->nome }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('matriculas.edit', $matricula) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('matriculas.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md">
            <!-- Header -->
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detalhes da Matrícula</h1>
                        <p class="text-gray-600 mt-1">Informações completas da matrícula</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('matriculas.index') }}" 
                           class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-2"></i>Voltar
                        </a>
                        <a href="{{ route('matriculas.edit', $matricula) }}" 
                           class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-edit mr-2"></i>Editar
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Informações do Aluno -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user mr-2 text-blue-600"></i>
                            Informações do Aluno
                        </h2>
                        
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 h-16 w-16 mr-4">
                                @if($matricula->aluno->foto_perfil)
                                    <img class="h-16 w-16 rounded-full object-cover" 
                                         src="{{ Storage::url($matricula->aluno->foto_perfil) }}" 
                                         alt="{{ $matricula->aluno->nome }}">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600 text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $matricula->aluno->nome }}</h3>
                                <p class="text-gray-600">{{ $matricula->aluno->email }}</p>
                                @if($matricula->aluno->telefone)
                                    <p class="text-gray-600">
                                        <i class="fas fa-phone text-xs mr-1"></i>{{ $matricula->aluno->telefone }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Data de Nascimento:</span>
                                <span class="font-medium">
                                    {{ $matricula->aluno->data_nascimento ? $matricula->aluno->data_nascimento->format('d/m/Y') : 'Não informado' }}
                                </span>
                            </div>
                            @if($matricula->aluno->endereco)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Endereço:</span>
                                    <span class="font-medium text-right">{{ $matricula->aluno->endereco }}</span>
                                </div>
                            @endif
                            <div class="pt-3 border-t border-gray-200">
                                <a href="{{ route('alunos.show', $matricula->aluno) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-external-link-alt mr-1"></i>Ver perfil completo
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Informações da Turma -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-chalkboard-teacher mr-2 text-green-600"></i>
                            Informações da Turma
                        </h2>
                        
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $matricula->turma->nome }}</h3>
                                <p class="text-gray-600">{{ $matricula->turma->ano_letivo }}</p>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Período:</span>
                                    <span class="font-medium">{{ ucfirst($matricula->turma->periodo) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Capacidade Máxima:</span>
                                    <span class="font-medium">{{ $matricula->turma->capacidade_maxima }} alunos</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Alunos Matriculados:</span>
                                    <span class="font-medium">
                                        {{ $matricula->turma->matriculas()->where('status', 'ativa')->count() }} alunos
                                    </span>
                                </div>
                                @if($matricula->turma->descricao)
                                    <div>
                                        <span class="text-gray-600">Descrição:</span>
                                        <p class="font-medium mt-1">{{ $matricula->turma->descricao }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-3 border-t border-gray-200">
                                <a href="{{ route('turmas.show', $matricula->turma) }}" 
                                   class="text-green-600 hover:text-green-800 font-medium">
                                    <i class="fas fa-external-link-alt mr-1"></i>Ver detalhes da turma
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações da Matrícula -->
                <div class="mt-8 bg-blue-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-graduation-cap mr-2 text-blue-600"></i>
                        Detalhes da Matrícula
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-sm text-gray-600 mb-1">Data da Matrícula</div>
                            <div class="text-xl font-bold text-gray-900">
                                {{ $matricula->data_matricula->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $matricula->data_matricula->diffForHumans() }}
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <div class="text-sm text-gray-600 mb-1">Status</div>
                            @php
                                $statusColors = [
                                    'ativa' => 'bg-green-100 text-green-800',
                                    'inativa' => 'bg-red-100 text-red-800',
                                    'transferida' => 'bg-yellow-100 text-yellow-800',
                                    'cancelada' => 'bg-gray-100 text-gray-800'
                                ];
                            @endphp
                            <div class="inline-flex px-3 py-2 text-sm font-semibold rounded-full {{ $statusColors[$matricula->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($matricula->status) }}
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <div class="text-sm text-gray-600 mb-1">Criada em</div>
                            <div class="text-lg font-bold text-gray-900">
                                {{ $matricula->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $matricula->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="mt-8 flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('matriculas.por-aluno', $matricula->aluno) }}" 
                       class="px-4 py-2 text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-lg font-medium transition-colors">
                        <i class="fas fa-list mr-2"></i>Outras matrículas do aluno
                    </a>
                    <a href="{{ route('matriculas.por-turma', $matricula->turma) }}" 
                       class="px-4 py-2 text-green-700 bg-green-100 hover:bg-green-200 rounded-lg font-medium transition-colors">
                        <i class="fas fa-users mr-2"></i>Alunos da turma
                    </a>
                    <form action="{{ route('matriculas.destroy', $matricula) }}" 
                          method="POST" class="inline" 
                          onsubmit="return confirm('Tem certeza que deseja remover esta matrícula? Esta ação não pode ser desfeita.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-trash mr-2"></i>Excluir Matrícula
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>