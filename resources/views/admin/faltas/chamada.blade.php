<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chamada - ') }}{{ $turma->nome }} - {{ $disciplina->nome }}
        </h2>
    </x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Chamada - {{ $turma->nome }}</h1>
                    <p class="text-gray-600">{{ $disciplina->nome }} - Prof. {{ $professor->nome }}</p>
                </div>
                <a href="{{ route('faltas.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <x-icons.arrow-left class="w-4 h-4 mr-2" />Voltar
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Mensagens de Alerta -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('warning') }}
                    </div>
                    @if(session('mostrar_confirmacao'))
                        <div class="flex gap-3">
                            <button type="button" 
                                    onclick="confirmarReenvio()" 
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Sim, confirmar reenvio
                            </button>
                            <button type="button" 
                                    onclick="cancelarReenvio()" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Cancelar
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <form action="{{ route('faltas.store') }}" method="POST" id="chamadaForm">
                @csrf
                <input type="hidden" name="turma_id" value="{{ $turma->id }}">
                <input type="hidden" name="disciplina_id" value="{{ $disciplina->id }}">
                <input type="hidden" name="professor_id" value="{{ $professor->id }}">
                
                <!-- Seleção de Data -->
                <div class="mb-6">
                    <label for="data_falta" class="block text-sm font-medium text-gray-700 mb-2">
                        Data da Aula:
                    </label>
                    <input type="date" 
                           id="data_falta" 
                           name="data_falta" 
                           value="{{ $data }}" 
                           max="{{ now()->format('Y-m-d') }}"
                           class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           onchange="atualizarChamada()">
                </div>

                <!-- Lista de Alunos -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Lista de Alunos ({{ $alunos->count() }} alunos)
                        </h3>
                        <div class="flex gap-2">
                            <button type="button" 
                                    onclick="marcarTodos(true)" 
                                    class="text-sm bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition-colors">
                                Marcar Todos como Faltosos
                            </button>
                            <button type="button" 
                                    onclick="marcarTodos(false)" 
                                    class="text-sm bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded transition-colors">
                                Marcar Todos como Presentes
                            </button>
                        </div>
                    </div>
                    
                    @if($alunos->isEmpty())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-700">
                                <x-icons.exclamation-triangle class="w-5 h-5 mr-2" />
                                Nenhum aluno encontrado nesta turma.
                            </p>
                        </div>
                    @else
                        <div class="grid gap-2">
                            @foreach($alunos as $aluno)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 mr-4">
                                            @if($aluno->foto_perfil)
                                                <img src="{{ Storage::url($aluno->foto_perfil) }}" 
                                                     alt="{{ $aluno->nome }}" 
                                                     class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                                    <x-icons.user class="w-5 h-5 text-gray-600" />
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $aluno->nome }}</p>
                                            <p class="text-sm text-gray-600">Matrícula: {{ $aluno->numero_matricula }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   name="faltas[]" 
                                                   value="{{ $aluno->numero_matricula }}"
                                                   class="sr-only falta-checkbox"
                                                   {{ in_array($aluno->numero_matricula, $faltasExistentes) ? 'checked' : '' }}>
                                            <div class="relative">
                                                <div class="w-12 h-6 bg-green-400 rounded-full shadow-inner transition-colors duration-200 ease-in-out checkbox-bg"></div>
                                                <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow transition-transform duration-200 ease-in-out checkbox-dot"></div>
                                            </div>
                                            <span class="ml-3 text-sm font-medium status-text text-green-600">Presente</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-end gap-3">
                    <button type="button" 
                            onclick="window.history.back()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        <x-icons.save class="w-4 h-4 mr-2" />Salvar Chamada
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function marcarTodos(faltoso) {
    const checkboxes = document.querySelectorAll('.falta-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = faltoso;
        atualizarEstiloCheckbox(checkbox);
    });
}

function atualizarEstiloCheckbox(checkbox) {
    const bg = checkbox.parentElement.querySelector('.checkbox-bg');
    const dot = checkbox.parentElement.querySelector('.checkbox-dot');
    const statusText = checkbox.parentElement.parentElement.querySelector('.status-text');
    
    if (checkbox.checked) {
        bg.classList.remove('bg-green-400');
        bg.classList.add('bg-red-400');
        dot.classList.add('translate-x-6');
        statusText.textContent = 'Faltoso';
        statusText.classList.remove('text-green-600');
        statusText.classList.add('text-red-600');
    } else {
        bg.classList.remove('bg-red-400');
        bg.classList.add('bg-green-400');
        dot.classList.remove('translate-x-6');
        statusText.textContent = 'Presente';
        statusText.classList.remove('text-red-600');
        statusText.classList.add('text-green-600');
    }
}

function atualizarChamada() {
    const data = document.getElementById('data_falta').value;
    const url = new URL(window.location);
    url.searchParams.set('data', data);
    window.location.href = url.toString();
}

// Inicializar estilos dos checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.falta-checkbox');
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