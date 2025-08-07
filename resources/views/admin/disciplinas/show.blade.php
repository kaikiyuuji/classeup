<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    {{ $disciplina->nome }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $disciplina->codigo }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.disciplinas.edit', $disciplina) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <x-icons.edit class="w-4 h-4 mr-2" />
                    Editar
                </a>
                <a href="{{ route('admin.disciplinas.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <x-icons.arrow-left class="w-4 h-4 mr-2" />
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-8">
                    <!-- Informações da Disciplina -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Informações da Disciplina
                        </h3>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nome da Disciplina</label>
                                <div class="px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900">
                                    {{ $disciplina->nome }}
                                </div>
                            </div>

                            <!-- Código -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Código</label>
                                <div class="px-4 py-3 bg-white border border-gray-300 rounded-lg">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $disciplina->codigo }}
                                    </span>
                                </div>
                            </div>

                            <!-- Carga Horária -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Carga Horária</label>
                                <div class="px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900">
                                    {{ $disciplina->carga_horaria }} horas
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                                <div class="px-4 py-3 bg-white border border-gray-300 rounded-lg">
                                    @if($disciplina->ativo)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <x-icons.circle class="w-1.5 h-1.5 mr-1.5" />
                                            Ativa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <x-icons.circle class="w-1.5 h-1.5 mr-1.5" />
                                            Inativa
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Descrição -->
                            @if($disciplina->descricao)
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Descrição</label>
                                    <div class="px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900">
                                        {{ $disciplina->descricao }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informações do Sistema -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informações do Sistema
                        </h3>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Data de Criação -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Data de Criação</label>
                                <div class="px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900">
                                    {{ $disciplina->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            <!-- Última Atualização -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Última Atualização</label>
                                <div class="px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900">
                                    {{ $disciplina->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="flex justify-between gap-4 pt-6 border-t border-gray-200 mt-8">
                        <form action="{{ route('admin.disciplinas.destroy', $disciplina) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex justify-center items-center px-6 py-3 border border-red-300 shadow-sm text-base font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                    onclick="return confirm('Tem certeza que deseja excluir esta disciplina? Esta ação não pode ser desfeita.')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Excluir Disciplina
                            </button>
                        </form>
                        
                        <div class="flex gap-4">
                            <a href="{{ route('admin.disciplinas.index') }}" 
                               class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                Voltar à Lista
                            </a>
                            <a href="{{ route('admin.disciplinas.edit', $disciplina) }}" 
                               class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar Disciplina
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>