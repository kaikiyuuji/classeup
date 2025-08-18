<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    {{ __('Gerenciar Professores') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Visualize e gerencie todos os professores cadastrados</p>
            </div>
            <a href="{{ route('admin.professores.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                Novo Professor
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-8">
            

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <!-- Filtros -->
                <x-admin.filtros 
                    :action="route('admin.professores.index')"
                    placeholder="Buscar por nome, email, especialidade ou formação..."
                    :show-search="true"
                    :show-status="true"
                    :show-disciplina="true"
                    :show-especialidade="true"
                    :disciplinas="$disciplinas"
                    :especialidades="$especialidades"
                />

                @if($professores->count() > 0)
                    <!-- Table View -->
                    <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <x-admin.sortable-header 
                                            field="nome" 
                                            :current-sort="$sortField ?? ''" 
                                            :current-direction="$sortDirection ?? 'asc'" 
                                            label="Professor" 
                                        />
                                        <x-admin.sortable-header 
                                            field="email" 
                                            :current-sort="$sortField ?? ''" 
                                            :current-direction="$sortDirection ?? 'asc'" 
                                            label="Contato" 
                                        />
                                        <x-admin.sortable-header 
                                            field="especialidade" 
                                            :current-sort="$sortField ?? ''" 
                                            :current-direction="$sortDirection ?? 'asc'" 
                                            label="Especialidade" 
                                        />
                                        <x-admin.sortable-header 
                                            field="ativo" 
                                            :current-sort="$sortField ?? ''" 
                                            :current-direction="$sortDirection ?? 'asc'" 
                                            label="Status" 
                                        />
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($professores as $professor)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($professor->foto_perfil_url)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $professor->foto_perfil_url }}" alt="{{ $professor->nome }}">
                                                        @else
                                                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                                <span class="text-purple-600 font-medium text-sm">{{ substr($professor->nome, 0, 2) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $professor->nome }}</div>
                                                        <div class="text-sm text-gray-500">ID: #{{ $professor->id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $professor->email }}</div>
                                                @if($professor->telefone)
                                                    <div class="text-sm text-gray-500" data-format="phone">{{ $professor->telefone }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $professor->especialidade }}</div>
                                                <div class="text-sm text-gray-500">{{ Str::limit($professor->formacao, 50) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $professor->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    <x-heroicon-o-pause class="w-1.5 h-1.5 mr-1.5" />
                                                    {{ $professor->ativo ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <a href="{{ route('admin.professores.show', $professor) }}" 
                                                   class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <x-heroicon-o-eye class="w-3 h-3 mr-1" />
                                                        Ver
                                                    </a>
                                                    <a href="{{ route('admin.professores.edit', $professor) }}" 
                                                       class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <x-heroicon-o-pencil class="w-3 h-3 mr-1" />
                                                        Editar
                                                    </a>
                                                    <button type="button" 
                                                            onclick="openDeleteModal({{ $professor->id }}, '{{ $professor->nome }}')"
                                                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        <x-heroicon-o-trash class="w-3 h-3 mr-1" />
                                                        Excluir
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="px-8 py-3 border-t border-gray-200">
                        {{ $professores->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <x-heroicon-o-academic-cap class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum professor cadastrado</h3>
                        <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro professor.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.professores.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                                Cadastrar Primeiro Professor
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Modal de Exclusão Avançada -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Cabeçalho do Modal -->
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Confirmar Exclusão do Professor</h3>
                    <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                <!-- Conteúdo do Modal -->
                <div class="mt-4">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-400 mr-2 mt-0.5" />
                            <div>
                                <h4 class="text-sm font-medium text-red-800">Atenção: Esta ação é irreversível</h4>
                                <p class="text-sm text-red-700 mt-1">
                                    Você está prestes a excluir o professor <strong id="professorNomeDisplay"></strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Área de Relacionamentos -->
                    <div id="relationshipsInfo" class="mb-4">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <x-heroicon-o-information-circle class="w-5 h-5 text-yellow-400 mr-2" />
                                <h5 class="text-sm font-medium text-yellow-800">Verificando relacionamentos...</h5>
                            </div>
                            <div id="relationshipsContent" class="text-sm text-yellow-700">
                                <div class="animate-pulse">Carregando dados relacionados...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulário de Confirmação -->
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')

                        <!-- Checkbox de Confirmação -->
                        <div class="mb-4">
                            <label class="flex items-start">
                                <input type="checkbox" 
                                       name="ciente_exclusao_dados" 
                                       id="confirmCheckbox" 
                                       class="mt-1 mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded" 
                                       required>
                                <span class="text-sm text-gray-700">
                                    Estou ciente de que esta ação excluirá permanentemente o professor e todos os dados relacionados (chamadas, vínculos com turmas e disciplinas). Esta ação não pode ser desfeita.
                                </span>
                            </label>
                        </div>

                        <!-- Campo de Confirmação do Nome -->
                        <div class="mb-4">
                            <label for="nomeConfirmacao" class="block text-sm font-medium text-gray-700 mb-2">
                                Para confirmar, digite o nome completo do professor: <strong id="professorNomeConfirm"></strong>
                            </label>
                            <input type="text" 
                                   name="nome_confirmacao" 
                                   id="nomeConfirmacao" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500" 
                                   placeholder="Digite o nome completo do professor" 
                                   required>
                            <div id="nameError" class="text-red-600 text-sm mt-1 hidden">O nome digitado não confere com o nome do professor.</div>
                        </div>

                        <input type="hidden" name="confirmacao_exclusao" value="1">

                        <!-- Botões de Ação -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button type="button" 
                                    onclick="closeDeleteModal()" 
                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    id="deleteButton" 
                                    disabled
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                Excluir Professor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    let currentProfessorId = null;
    let currentProfessorNome = null;

    function openDeleteModal(professorId, professorNome) {
        currentProfessorId = professorId;
        currentProfessorNome = professorNome;
        
        document.getElementById('professorNomeDisplay').textContent = professorNome;
        document.getElementById('professorNomeConfirm').textContent = professorNome;
        document.getElementById('deleteForm').action = `/admin/professores/${professorId}`;
        
        document.getElementById('deleteModal').classList.remove('hidden');
        checkRelationships();
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        resetForm();
    }

    function resetForm() {
        document.getElementById('deleteForm').reset();
        document.getElementById('deleteButton').disabled = true;
        document.getElementById('nameError').classList.add('hidden');
        
        // Reset relationship info
        const relationshipsInfo = document.getElementById('relationshipsInfo');
        relationshipsInfo.querySelector('div').className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-4';
        relationshipsInfo.querySelector('h5').className = 'text-sm font-medium text-yellow-800';
        relationshipsInfo.querySelector('h5').textContent = 'Verificando relacionamentos...';
        relationshipsInfo.querySelector('svg').className = 'w-5 h-5 text-yellow-400 mr-2';
        document.getElementById('relationshipsContent').innerHTML = '<div class="animate-pulse">Carregando dados relacionados...</div>';
    }

    async function checkRelationships() {
        try {
            const response = await fetch(`/admin/professores/${currentProfessorId}/relacionamentos`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            displayRelationships(data);
        } catch (error) {
            console.error('Erro ao verificar relacionamentos:', error);
            document.getElementById('relationshipsContent').innerHTML = 
                '<span class="text-red-600">Erro ao carregar relacionamentos. Prossiga com cautela.</span>';
        }
    }

    function displayRelationships(data) {
        const content = document.getElementById('relationshipsContent');
        
        if (data.temRelacionamentos) {
            let html = '<div class="text-red-700"><strong>Este professor possui os seguintes dados que serão excluídos:</strong><ul class="list-disc list-inside mt-2 space-y-1">';
            
            if (data.relacionamentos.chamadas > 0) {
                html += `<li>${data.relacionamentos.chamadas} chamada(s) registrada(s)</li>`;
            }
            
            if (data.relacionamentos.turmasComDisciplinas > 0) {
                html += `<li>${data.relacionamentos.turmasComDisciplinas} vínculo(s) com turmas e disciplinas</li>`;
            }
            
            if (data.relacionamentos.disciplinas > 0) {
                html += `<li>${data.relacionamentos.disciplinas} vínculo(s) direto(s) com disciplinas</li>`;
            }
            
            html += '</ul></div>';
            content.innerHTML = html;
            
            // Muda a cor do container para vermelho
            content.parentElement.className = 'bg-red-50 border border-red-200 rounded-lg p-4';
            content.parentElement.querySelector('h5').className = 'text-sm font-medium text-red-800';
            content.parentElement.querySelector('svg').className = 'w-5 h-5 text-red-400 mr-2';
        } else {
            content.innerHTML = '<span class="text-green-700">Este professor não possui relacionamentos. A exclusão será segura.</span>';
            
            // Muda a cor do container para verde
            content.parentElement.className = 'bg-green-50 border border-green-200 rounded-lg p-4';
            content.parentElement.querySelector('h5').className = 'text-sm font-medium text-green-800';
            content.parentElement.querySelector('h5').textContent = 'Verificação concluída';
            content.parentElement.querySelector('svg').className = 'w-5 h-5 text-green-400 mr-2';
        }
    }

    // Validação em tempo real
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('confirmCheckbox');
        const nameInput = document.getElementById('nomeConfirmacao');
        const deleteButton = document.getElementById('deleteButton');
        const nameError = document.getElementById('nameError');
        
        function validateForm() {
            const isChecked = checkbox.checked;
            const nameMatches = nameInput.value.trim() === currentProfessorNome;
            
            if (nameInput.value.trim() && !nameMatches) {
                nameError.classList.remove('hidden');
            } else {
                nameError.classList.add('hidden');
            }
            
            deleteButton.disabled = !(isChecked && nameMatches);
        }
        
        checkbox.addEventListener('change', validateForm);
        nameInput.addEventListener('input', validateForm);
    });

    // Fechar modal ao clicar fora
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    </script>
</x-app-layout>