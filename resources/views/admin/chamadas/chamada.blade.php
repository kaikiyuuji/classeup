<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    <x-heroicon-o-clipboard class="w-6 h-6 mr-2 text-blue-600 inline" />
                    Fazer Chamada
                </h2>
                <p class="text-gray-600 mt-1">{{ $turma->nome }} - {{ $disciplina->nome }}</p>
            </div>
            <a href="{{ route('admin.chamadas.index') }}" 
               class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors font-medium">
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                Voltar
            </a>
        </div>
    </x-slot>

<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
            <!-- Cabeçalho da Turma -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                            <x-heroicon-o-users class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">{{ $turma->nome }}</h1>
                            <p class="text-blue-100">{{ $disciplina->nome }} • Prof. {{ $professor->nome }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-blue-100 text-sm">Total de Alunos</p>
                        <p class="text-white text-2xl font-bold">{{ $alunos->count() }}</p>
                    </div>
                </div>
            </div>

        <div class="p-6">
            <!-- Mensagens de Alerta -->
            @if(session('warning'))
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 px-6 py-4 rounded-r-lg shadow-sm">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <p class="font-medium">{{ session('warning') }}</p>
                    </div>
                    @if(session('mostrar_confirmacao'))
                        <div class="flex gap-3 mt-4">
                            <button type="button" 
                                    onclick="confirmarReenvio()" 
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                                Sim, confirmar reenvio
                            </button>
                            <button type="button" 
                                    onclick="cancelarReenvio()" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                                Cancelar
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <form action="{{ route('admin.chamadas.store') }}" method="POST" id="chamadaForm">
                @csrf
                <input type="hidden" name="turma_id" value="{{ $turma->id }}">
                <input type="hidden" name="disciplina_id" value="{{ $disciplina->id }}">
                <input type="hidden" name="professor_id" value="{{ $professor->id }}">
                
                <!-- Controles da Chamada -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Seleção de Data -->
                        <div>
                            <label for="data_chamada" class="block text-sm font-semibold text-gray-700 mb-3">
                                <x-heroicon-o-calendar class="w-4 h-4 mr-1 inline" />
                                Data da Aula
                            </label>
                            <input type="date" 
                                   id="data_chamada" 
                                   name="data_chamada" 
                                   value="{{ $data }}" 
                                   max="{{ now()->format('Y-m-d') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium"
                                   onchange="atualizarChamada()">
                        </div>

                        <!-- Ações Rápidas -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <x-heroicon-o-bolt class="w-4 h-4 mr-1 inline" />
                                Ações Rápidas
                            </label>
                            <div class="flex gap-3">
                                <button type="button" 
                                        onclick="marcarTodos(true)" 
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition-colors font-medium shadow-sm flex items-center justify-center">
                                    <x-heroicon-o-check class="w-4 h-4 mr-2" />
                                    Todos Presentes
                                </button>
                                <button type="button" 
                                        onclick="marcarTodos(false)" 
                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg transition-colors font-medium shadow-sm flex items-center justify-center">
                                    <x-heroicon-o-x-mark class="w-4 h-4 mr-2" />
                                    Todos Faltosos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Alunos -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <x-heroicon-o-users class="w-5 h-5 mr-2 text-blue-600" />
                            Lista de Alunos
                            <span class="ml-2 bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                                {{ $alunos->count() }} alunos
                            </span>
                        </h3>
                    </div>
                    
                    @if($alunos->isEmpty())
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg p-6 text-center">
                            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-yellow-600" />
                            </div>
                            <p class="text-yellow-800 font-medium text-lg">
                                Nenhum aluno encontrado nesta turma.
                            </p>
                            <p class="text-yellow-600 text-sm mt-2">
                                Verifique se há alunos matriculados nesta turma.
                            </p>
                        </div>
                    @else
                        <div class="grid gap-4">
                            @foreach($alunos as $aluno)
                                <div class="bg-white border border-gray-200 rounded-xl p-4 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 mr-4">
                                                @if($aluno->foto_perfil)
                                                    <img src="{{ Storage::url($aluno->foto_perfil) }}" 
                                                         alt="{{ $aluno->nome }}" 
                                                         class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                                                @else
                                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                                        <x-heroicon-o-user class="w-6 h-6 text-white" />
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900 text-lg">{{ $aluno->nome }}</p>
                                                <p class="text-sm text-gray-600 flex items-center">
                                                    <x-heroicon-o-user class="w-4 h-4 mr-1" />
                                                    {{ $aluno->numero_matricula }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <label class="flex items-center cursor-pointer group">
                                                <input type="checkbox" 
                                                       name="presencas[]" 
                                                       value="{{ $aluno->numero_matricula }}"
                                                       class="sr-only presenca-checkbox"
                                                       {{ in_array($aluno->numero_matricula, $presencasExistentes) ? 'checked' : '' }}>
                                                <div class="relative">
                                                    <div class="w-14 h-7 bg-red-400 rounded-full shadow-inner transition-colors duration-200 ease-in-out checkbox-bg group-hover:shadow-lg"></div>
                                                    <div class="absolute left-1 top-1 bg-white w-5 h-5 rounded-full shadow transition-transform duration-200 ease-in-out checkbox-dot"></div>
                                                </div>
                                                <div class="ml-4 text-right">
                                                    <span class="block text-sm font-bold status-text text-red-600">Falta</span>
                                                    <span class="block text-xs text-gray-500">Clique para alterar</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Botões de Ação -->
                <div class="bg-gray-50 rounded-xl p-6 mt-8">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <p class="font-medium">Lembre-se:</p>
                            <p>Verifique a presença de todos os alunos antes de salvar.</p>
                        </div>
                        <div class="flex gap-4">
                            <button type="button" 
                                    onclick="window.history.back()" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors font-medium shadow-sm flex items-center">
                                <x-heroicon-o-x-mark class="w-4 h-4 mr-2" />
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition-colors font-medium shadow-sm flex items-center">
                                <x-heroicon-o-check class="w-5 h-5 mr-2" />
                                Salvar Chamada
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function marcarTodos(presente) {
    const checkboxes = document.querySelectorAll('.presenca-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = presente;
        atualizarEstiloCheckbox(checkbox);
    });
}

function atualizarEstiloCheckbox(checkbox) {
    const bg = checkbox.parentElement.querySelector('.checkbox-bg');
    const dot = checkbox.parentElement.querySelector('.checkbox-dot');
    const statusText = checkbox.parentElement.parentElement.querySelector('.status-text');
    
    if (checkbox.checked) {
        bg.classList.remove('bg-red-400');
        bg.classList.add('bg-green-400');
        dot.classList.add('translate-x-7');
        statusText.textContent = 'Presente';
        statusText.classList.remove('text-red-600');
        statusText.classList.add('text-green-600');
    } else {
        bg.classList.remove('bg-green-400');
        bg.classList.add('bg-red-400');
        dot.classList.remove('translate-x-7');
        statusText.textContent = 'Falta';
        statusText.classList.remove('text-green-600');
        statusText.classList.add('text-red-600');
    }
}

function atualizarChamada() {
    const data = document.getElementById('data_chamada').value;
    const url = new URL(window.location);
    url.searchParams.set('data', data);
    window.location.href = url.toString();
}

// Inicializar estilos dos checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.presenca-checkbox');
    checkboxes.forEach(checkbox => {
        atualizarEstiloCheckbox(checkbox);
        checkbox.addEventListener('change', function() {
            atualizarEstiloCheckbox(this);
        });
    });
});

// Funções para confirmação de reenvio
function confirmarReenvio() {
    // Adiciona campo hidden para confirmar reenvio
    const form = document.getElementById('chamadaForm');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'confirmar_reenvio';
    input.value = '1';
    form.appendChild(input);
    
    // Submete o formulário
    form.submit();
}

function cancelarReenvio() {
    // Remove a mensagem de warning recarregando a página sem os dados
    window.location.href = window.location.pathname + window.location.search.split('&')[0];
}
</script>
</x-app-layout>