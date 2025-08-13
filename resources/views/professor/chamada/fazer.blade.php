<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lançar Chamada') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-session-messages />
            
            <!-- Cabeçalho -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Lançar Chamada</h1>
                            <p class="text-gray-600 mt-2">{{ $turma->nome }} - {{ $turma->serie }} ({{ ucfirst($turma->turno) }}) | {{ $disciplina->nome }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Professor: {{ $professor->nome }} • Total de Alunos: {{ $alunos->count() }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('professor.chamadas.index') }}" 
                               class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors font-medium shadow-sm">
                                <x-heroicon-o-arrow-left class="w-5 h-5 mr-2" />
                                Voltar às Chamadas
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seletor de Data -->
            <div class="mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Selecionar Data da Chamada</h3>
                            <p class="text-sm text-gray-600">Escolha a data para a qual deseja lançar a chamada</p>
                            @if(isset($chamadaJaLancada) && $chamadaJaLancada)
                                <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <x-heroicon-s-check-circle class="w-4 h-4 mr-2" />
                                    Chamada já lançada para esta data
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center space-x-4">
                            <form method="GET" action="{{ route('professor.chamada.fazer', ['turma' => $turma->id, 'disciplina' => $disciplina->id]) }}" class="flex items-center space-x-3">
                                <label for="data" class="text-sm font-medium text-gray-700">Data:</label>
                                <input type="date" 
                                       id="data" 
                                       name="data" 
                                       value="{{ $data }}" 
                                       max="{{ date('Y-m-d') }}"
                                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       onchange="this.form.submit()">
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($data)->locale('pt_BR')->dayName }}
                                    @if(isset($chamadaJaLancada) && $chamadaJaLancada)
                                        <span class="inline-flex items-center ml-2 text-green-600">
                                            <x-heroicon-s-check-circle class="w-4 h-4" />
                                        </span>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas de Confirmação -->
            @if(session('warning') && session('mostrar_confirmacao'))
                <div class="mb-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                        <div class="flex items-start">
                            <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-yellow-600 mr-3 mt-0.5" />
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-yellow-800 mb-2">Atenção!</h3>
                                <p class="text-yellow-700 mb-4">{{ session('warning') }}</p>
                                <div class="flex space-x-3">
                                    <form method="POST" action="{{ route('professor.chamada.salvar') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="turma_id" value="{{ $turma->id }}">
                                        <input type="hidden" name="disciplina_id" value="{{ $disciplina->id }}">
                                        <input type="hidden" name="data_chamada" value="{{ $data }}">
                                        <input type="hidden" name="confirmar_reenvio" value="1">
                                        @foreach(old('presencas', []) as $matricula)
                                            <input type="hidden" name="presencas[]" value="{{ $matricula }}">
                                        @endforeach
                                        <button type="submit" class="inline-flex items-center bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm">
                                            <x-heroicon-o-check class="w-4 h-4 mr-2" />
                                            Sim, Substituir
                                        </button>
                                    </form>
                                    <a href="{{ route('professor.chamadas.index') }}" 
                                       class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm">
                                        <x-heroicon-o-x-mark class="w-4 h-4 mr-2" />
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulário de Chamada -->
            <form method="POST" action="{{ route('professor.chamada.salvar') }}" id="formChamada">
                @csrf
                <input type="hidden" name="turma_id" value="{{ $turma->id }}">
                <input type="hidden" name="disciplina_id" value="{{ $disciplina->id }}">
                <input type="hidden" name="data_chamada" value="{{ $data }}">

                <!-- Controles de Chamada -->
                <div class="mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <x-heroicon-o-clipboard-document-check class="w-5 h-5 mr-2" />
                                Controles de Chamada
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                                <div class="flex flex-wrap gap-3">
                                    <button type="button" 
                                            class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm"
                                            onclick="marcarTodosPresentes()">
                                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2" />
                                        Marcar Todos Presentes
                                    </button>
                                    <button type="button" 
                                            class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm"
                                            onclick="marcarTodosFaltas()">
                                        <x-heroicon-o-x-circle class="w-4 h-4 mr-2" />
                                        Marcar Todos Faltas
                                    </button>
                                </div>
                                <div class="flex space-x-4">
                                    <div class="bg-green-100 text-green-800 px-3 py-2 rounded-lg font-medium">
                                        Presentes: <span id="numeroPresentes">{{ count($presencasExistentes ?? []) }}</span>
                                    </div>
                                    <div class="bg-red-100 text-red-800 px-3 py-2 rounded-lg font-medium">
                                        Faltas: <span id="numeroFaltas">{{ $alunos->count() - count($presencasExistentes ?? []) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Alunos -->
                <div class="mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <x-heroicon-o-user-group class="w-5 h-5 mr-2" />
                                Lista de Alunos ({{ $alunos->count() }})
                            </h3>
                        </div>
                        <div class="overflow-hidden">
                            @if($alunos->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matrícula</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome do Aluno</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Presença</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($alunos as $index => $aluno)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $index + 1 }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $aluno->numero_matricula }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            @if($aluno->foto)
                                                                <img src="{{ asset('storage/' . $aluno->foto) }}" 
                                                                     alt="Foto de {{ $aluno->nome }}" 
                                                                     class="w-8 h-8 rounded-full mr-3 object-cover">
                                                            @else
                                                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                                                    <x-heroicon-o-user class="w-4 h-4 text-gray-600" />
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900">{{ $aluno->nome }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <div class="flex items-center justify-center">
                                                            <label class="relative inline-flex items-center cursor-pointer">
                                                                <input type="checkbox" 
                                                                       name="presencas[]" 
                                                                       value="{{ $aluno->numero_matricula }}"
                                                                       class="presenca-checkbox"
                                                                       {{ in_array($aluno->numero_matricula, $presencasExistentes ?? []) ? 'checked' : '' }}
                                                                       onchange="atualizarContadores()"
                                                                       style="display: none;">
                                                                <div class="toggle-switch w-14 h-7 rounded-full relative transition-colors duration-300 {{ in_array($aluno->numero_matricula, $presencasExistentes ?? []) ? 'bg-green-500' : 'bg-red-500' }}">
                                                                    <div class="toggle-circle absolute top-0.5 left-0.5 w-6 h-6 bg-white rounded-full shadow-md transform transition-transform duration-300 {{ in_array($aluno->numero_matricula, $presencasExistentes ?? []) ? 'translate-x-7' : 'translate-x-0' }}"></div>
                                                                </div>
                                                                <span class="ml-3 text-sm font-medium w-16 text-left">
                                                                    <span class="presente-label text-green-600 font-semibold" style="display: {{ in_array($aluno->numero_matricula, $presencasExistentes ?? []) ? 'inline' : 'none' }};">Presente</span>
                                                                    <span class="falta-label text-red-600 font-semibold" style="display: {{ in_array($aluno->numero_matricula, $presencasExistentes ?? []) ? 'none' : 'inline' }};">Falta</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-12 text-center">
                                    <x-heroicon-o-user-group class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum aluno encontrado</h3>
                                    <p class="text-gray-500">Não há alunos matriculados nesta turma.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                @if($alunos->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex flex-col sm:flex-row sm:justify-center sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                            <button type="submit" 
                                    class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-lg transition-colors font-semibold shadow-lg text-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <x-heroicon-o-check class="w-6 h-6 mr-3" />
                                Salvar Chamada
                            </button>
                            <a href="{{ route('professor.chamadas.index') }}" 
                               class="inline-flex items-center justify-center bg-gray-600 hover:bg-gray-700 text-white px-10 py-4 rounded-lg transition-colors font-semibold shadow-lg text-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <x-heroicon-o-arrow-left class="w-6 h-6 mr-3" />
                                Voltar às Chamadas
                            </a>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <script>
        function marcarTodosPresentes() {
            document.querySelectorAll('.presenca-checkbox').forEach(checkbox => {
                checkbox.checked = true;
                const row = checkbox.closest('tr');
                row.querySelector('.presente-label').style.display = 'inline';
                row.querySelector('.falta-label').style.display = 'none';
            });
            atualizarContadores();
        }

        function marcarTodosFaltas() {
            document.querySelectorAll('.presenca-checkbox').forEach(checkbox => {
                checkbox.checked = false;
                const row = checkbox.closest('tr');
                row.querySelector('.presente-label').style.display = 'none';
                row.querySelector('.falta-label').style.display = 'inline';
            });
            atualizarContadores();
        }

        function atualizarContadores() {
            const checkboxes = document.querySelectorAll('.presenca-checkbox');
            const totalAlunos = checkboxes.length;
            let presentes = 0;
            
            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const presenteLabel = row.querySelector('.presente-label');
                const faltaLabel = row.querySelector('.falta-label');
                const toggleSwitch = row.querySelector('.toggle-switch');
                const toggleCircle = row.querySelector('.toggle-circle');
                
                if (checkbox.checked) {
                    presentes++;
                    presenteLabel.style.display = 'inline';
                    faltaLabel.style.display = 'none';
                    toggleSwitch.classList.remove('bg-red-500');
                    toggleSwitch.classList.add('bg-green-500');
                    toggleCircle.classList.remove('translate-x-0');
                    toggleCircle.classList.add('translate-x-7');
                } else {
                    presenteLabel.style.display = 'none';
                    faltaLabel.style.display = 'inline';
                    toggleSwitch.classList.remove('bg-green-500');
                    toggleSwitch.classList.add('bg-red-500');
                    toggleCircle.classList.remove('translate-x-7');
                    toggleCircle.classList.add('translate-x-0');
                }
            });
            
            const faltas = totalAlunos - presentes;
            
            document.getElementById('numeroPresentes').textContent = presentes;
            document.getElementById('numeroFaltas').textContent = faltas;
        }

        // Inicializar contadores ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            atualizarContadores();
            
            // Adicionar event listeners para os switches
            document.querySelectorAll('.toggle-switch').forEach(toggleSwitch => {
                toggleSwitch.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const checkbox = this.closest('label').querySelector('.presenca-checkbox');
                    checkbox.checked = !checkbox.checked;
                    atualizarContadores();
                });
            });
            
            // Adicionar event listeners para os labels também
            document.querySelectorAll('label').forEach(label => {
                if (label.querySelector('.toggle-switch')) {
                    label.addEventListener('click', function(e) {
                        e.preventDefault();
                        const checkbox = this.querySelector('.presenca-checkbox');
                        checkbox.checked = !checkbox.checked;
                        atualizarContadores();
                    });
                }
            });
        });
    </script>
</x-app-layout>