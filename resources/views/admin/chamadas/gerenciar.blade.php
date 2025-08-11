<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gerenciar Chamadas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-8 py-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Gerenciar Chamadas</h1>
                            <p class="text-gray-600 mt-2">
                                {{ $turma->nome }} - {{ $disciplina->nome }} ({{ $professor->nome }})
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.chamadas.index') }}" 
                               class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors shadow-sm font-medium">
                                <x-heroicon-o-arrow-left class="w-5 h-5 mr-2" />Voltar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <!-- Filtros -->
                    <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h3>
                        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <input type="hidden" name="professor_id" value="{{ $professor->id }}">
                            
                            <div>
                                <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-2">Data Início</label>
                                <input type="date" id="data_inicio" name="data_inicio" value="{{ $dataInicio }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-2">Data Fim</label>
                                <input type="date" id="data_fim" name="data_fim" value="{{ $dataFim }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="flex items-end">
                                <button type="submit" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors font-medium">
                                    Filtrar
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de Chamadas por Dia -->
                    @if($chamadasPorDia->count() > 0)
                        <div class="space-y-6">
                            @foreach($chamadasPorDia as $data => $chamadas)
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">
                                                {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                {{ $chamadas->count() }} alunos registrados
                                                ({{ $chamadas->where('status', 'presente')->count() }} presenças, 
                                                {{ $chamadas->where('status', 'falta')->count() }} faltas)
                                            </p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button onclick="editarDia('{{ $data }}')"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                                Editar Dia
                                            </button>
                                            <button onclick="excluirDia('{{ $data }}')"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                                Excluir Dia
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div id="chamadas-{{ $data }}" class="hidden p-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($chamadas as $chamada)
                                                <div class="border border-gray-200 rounded-lg p-4 chamada-item" data-chamada-id="{{ $chamada->id }}">
                                                    <div class="flex justify-between items-start mb-3">
                                                        <div>
                                                            <h5 class="font-semibold text-gray-900">{{ $chamada->aluno->nome }}</h5>
                                                            <p class="text-sm text-gray-600">{{ $chamada->matricula }}</p>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <select class="status-select text-sm border border-gray-300 rounded px-2 py-1" 
                                                                    data-chamada-id="{{ $chamada->id }}">
                                                                <option value="presente" {{ $chamada->status === 'presente' ? 'selected' : '' }}>Presente</option>
                                                                <option value="falta" {{ $chamada->status === 'falta' ? 'selected' : '' }}>Falta</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($chamada->status === 'falta')
                                                        <div class="mt-3">
                                                            <label class="flex items-center gap-2 text-sm">
                                                                <input type="checkbox" class="justificada-checkbox" 
                                                                       data-chamada-id="{{ $chamada->id }}"
                                                                       {{ $chamada->justificada ? 'checked' : '' }}>
                                                                Justificada
                                                            </label>
                                                            
                                                            <textarea class="observacoes-textarea w-full mt-2 text-sm border border-gray-300 rounded px-2 py-1" 
                                                                      data-chamada-id="{{ $chamada->id }}"
                                                                      placeholder="Observações..."
                                                                      rows="2">{{ $chamada->observacoes }}</textarea>
                                                        </div>
                                                    @endif
                                                    
                                                    <div class="mt-3 flex justify-end">
                                                        <button onclick="salvarChamada({{ $chamada->id }})"
                                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                                            Salvar
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <x-heroicon-o-calendar class="w-16 h-16 mx-auto" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhuma chamada encontrada</h3>
                            <p class="text-gray-600">Não há chamadas registradas para o período selecionado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirmar Exclusão</h3>
            <p class="text-gray-600 mb-6">Tem certeza que deseja excluir todas as chamadas deste dia? Esta ação não pode ser desfeita.</p>
            <div class="flex justify-end gap-3">
                <button onclick="fecharModal()" 
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button id="confirmarExclusao" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Excluir
                </button>
            </div>
        </div>
    </div>

    <script>
        let dataParaExcluir = null;
        
        function editarDia(data) {
            const container = document.getElementById(`chamadas-${data}`);
            container.classList.toggle('hidden');
        }
        
        function excluirDia(data) {
            dataParaExcluir = data;
            document.getElementById('confirmModal').classList.remove('hidden');
            document.getElementById('confirmModal').classList.add('flex');
        }
        
        function fecharModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.remove('flex');
            dataParaExcluir = null;
        }
        
        document.getElementById('confirmarExclusao').addEventListener('click', function() {
            if (dataParaExcluir) {
                fetch(`{{ route('admin.chamadas.excluir-dia', ['data' => '__DATA__', 'turma' => $turma->id, 'disciplina' => $disciplina->id]) }}`.replace('__DATA__', dataParaExcluir), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao excluir chamada.');
                });
                
                fecharModal();
            }
        });
        
        function salvarChamada(chamadaId) {
            const container = document.querySelector(`[data-chamada-id="${chamadaId}"]`);
            const status = container.querySelector('.status-select').value;
            const justificada = container.querySelector('.justificada-checkbox')?.checked || false;
            const observacoes = container.querySelector('.observacoes-textarea')?.value || '';
            
            fetch(`{{ route('admin.chamadas.editar', '__ID__') }}`.replace('__ID__', chamadaId), {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status,
                    justificada: justificada,
                    observacoes: observacoes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar feedback visual
                    const button = container.querySelector('button');
                    const originalText = button.textContent;
                    button.textContent = 'Salvo!';
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-green-800');
                    
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-800');
                        button.classList.add('bg-green-600', 'hover:bg-green-700');
                    }, 2000);
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao salvar chamada.');
            });
        }
        
        // Mostrar/ocultar campos de justificativa baseado no status
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('status-select')) {
                const container = e.target.closest('.chamada-item');
                const justificativaDiv = container.querySelector('.mt-3');
                
                if (e.target.value === 'falta' && !justificativaDiv) {
                    // Adicionar campos de justificativa
                    const chamadaId = e.target.dataset.chamadaId;
                    const justificativaHtml = `
                        <div class="mt-3">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" class="justificada-checkbox" data-chamada-id="${chamadaId}">
                                Justificada
                            </label>
                            <textarea class="observacoes-textarea w-full mt-2 text-sm border border-gray-300 rounded px-2 py-1" 
                                      data-chamada-id="${chamadaId}"
                                      placeholder="Observações..."
                                      rows="2"></textarea>
                        </div>
                    `;
                    e.target.closest('.flex').parentNode.insertAdjacentHTML('afterend', justificativaHtml);
                } else if (e.target.value === 'presente' && justificativaDiv) {
                    // Remover campos de justificativa
                    justificativaDiv.remove();
                }
            }
        });
    </script>
</x-app-layout>