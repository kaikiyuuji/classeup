<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Detalhes do Professor
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Visualize e gerencie as informações do professor {{ $professor->nome }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.professores.edit', $professor) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                    <x-heroicon-o-pencil class="w-4 h-4 mr-2" />
                    Editar
                </a>
                <a href="{{ route('admin.professores.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                    <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
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
                                @if($professor->foto_perfil_url)
                    <img src="{{ $professor->foto_perfil_url }}"
                         alt="Foto de {{ $professor->nome }}"
                                         class="w-40 h-40 rounded-full object-cover border-4 border-white shadow-lg">
                                @else
                                    <div class="w-40 h-40 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center border-4 border-white shadow-lg">
                                        <span class="text-4xl font-bold text-white">
                                            {{ strtoupper(substr($professor->nome, 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                                
                                <!-- Badge de Status -->
                                <div class="absolute -bottom-2 -right-2">
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full shadow-sm
                                        {{ $professor->ativo ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                        {{ $professor->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Informações Principais -->
                        <div class="flex-1 text-left">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $professor->nome }}</h1>
                            <div class="flex items-start space-x-4 text-gray-600">
                                <div class="flex items-center">
                                    <x-heroicon-o-envelope class="w-4 h-4 mr-2" />
                                    <a href="mailto:{{ $professor->email }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                    {{ $professor->email }}
                                    </a>
                                </div>
                                @if($professor->telefone)
                                    <div class="flex items-center">
                                        <x-heroicon-o-phone class="w-4 h-4 mr-2" />
                                        <a href="tel:{{ $professor->telefone }}" class="text-blue-600 hover:text-blue-800 transition-colors" data-format="phone">
                        {{ $professor->telefone }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3 text-sm text-gray-500">
                                Cadastrado em {{ $professor->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Informações Detalhadas -->
                    <div class="grid grid-cols-2 gap-8">
                        <!-- Informações Pessoais -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-heroicon-o-user class="w-5 h-5 mr-2 text-purple-600" />
                                Informações Pessoais
                            </h3>
                            
                            <dl class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Nome Completo</dt>
                                    <dd class="text-sm text-gray-900 font-medium">{{ $professor->nome }}</dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">CPF</dt>
                                    <dd class="text-sm text-gray-900 font-mono" data-format="cpf">{{ $professor->cpf }}</dd>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <dt class="text-sm font-medium text-gray-600">Data de Nascimento</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $professor->data_nascimento?->format('d/m/Y') }}
                                        @if($professor->data_nascimento)
                                            <span class="text-gray-500 ml-1">
                                                ({{ $professor->data_nascimento->age }} anos)
                                            </span>
                                        @endif
                                    </dd>
                                </div>

                                <div class="flex justify-between items-center py-2">
                                    <dt class="text-sm font-medium text-gray-600">Status</dt>
                                    <dd>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $professor->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $professor->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Informações Profissionais -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Informações Profissionais
                            </h3>
                            
                            <dl class="space-y-4">
                                @if($professor->especialidade)
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <dt class="text-sm font-medium text-gray-600">Especialidade</dt>
                                        <dd class="text-sm text-gray-900 font-medium">{{ $professor->especialidade }}</dd>
                                    </div>
                                @endif

                                @if($professor->formacao)
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <dt class="text-sm font-medium text-gray-600">Formação</dt>
                                        <dd class="text-sm text-gray-900">{{ $professor->formacao }}</dd>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center py-2">
                                    <dt class="text-sm font-medium text-gray-600">Cadastrado em</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $professor->created_at->format('d/m/Y H:i') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Informações de Contato -->
                    <div class="mt-8">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-heroicon-o-envelope class="w-5 h-5 mr-2 text-green-600" />
                                Informações de Contato
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <dl class="space-y-4">
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <dt class="text-sm font-medium text-gray-600">Email</dt>
                                        <dd class="text-sm">
                                            <a href="mailto:{{ $professor->email }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                                {{ $professor->email }}
                                            </a>
                                        </dd>
                                    </div>

                                    @if($professor->telefone)
                                        <div class="flex justify-between items-center py-2">
                                            <dt class="text-sm font-medium text-gray-600">Telefone</dt>
                                            <dd class="text-sm">
                                                <a href="tel:{{ $professor->telefone }}" class="text-blue-600 hover:text-blue-800 transition-colors" data-format="phone">
                                                    {{ $professor->telefone }}
                                                </a>
                                            </dd>
                                        </div>
                                    @endif
                                </dl>

                                @if($professor->endereco)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600 mb-2">Endereço</dt>
                                        <dd class="text-sm text-gray-900 leading-relaxed bg-white p-3 rounded border">
                                            {{ $professor->endereco }}
                                        </dd>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Disciplinas -->
                    <div class="mt-8">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-heroicon-o-book-open class="w-5 h-5 mr-2 text-blue-600" />
                                Disciplinas Vinculadas
                            </h3>
                            
                            <!-- Disciplinas Vinculadas -->
                            @if($disciplinasVinculadas->count() > 0)
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Disciplinas que o professor leciona:</h4>
                                    <div class="grid grid-cols-3 gap-3">
                                        @foreach($disciplinasVinculadas as $disciplina)
                                            <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $disciplina->nome }}</p>
                                                        <p class="text-xs text-gray-500">{{ $disciplina->codigo }}</p>
                                                    </div>
                                                </div>
                                                <form action="{{ route('admin.professores.desvincular-disciplina', $professor) }}" method="POST" class="inline"
                                                      onsubmit="return handleUnlinkConfirm(event, '{{ $disciplina->nome }}', 'deste professor')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="disciplina_id" value="{{ $disciplina->id }}">
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 transition-colors">
                                                        <x-heroicon-o-x-mark class="w-4 h-4" />
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="mb-6 text-center py-8">
                                    <x-heroicon-o-book-open class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                                    <p class="text-gray-500 text-sm">Nenhuma disciplina vinculada</p>
                                </div>
                            @endif

                            <!-- Formulário para Vincular Nova Disciplina -->
                            @if($disciplinasDisponiveis->count() > 0)
                                <div class="border-t border-gray-200 pt-6">
                                    <h4 class="text-sm font-medium text-gray-700 mb-4">Vincular Nova Disciplina:</h4>
                                    <form action="{{ route('admin.professores.vincular-disciplina', $professor) }}" method="POST" class="flex gap-3">
                                        @csrf
                                        <div class="flex-1">
                                            <select name="disciplina_id" 
                                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                                    required>
                                                <option value="">Selecione uma disciplina...</option>
                                                @foreach($disciplinasDisponiveis as $disciplina)
                                                    <option value="{{ $disciplina->id }}">{{ $disciplina->nome }} ({{ $disciplina->codigo }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" 
                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                                            <x-heroicon-o-plus class="w-4 h-4 inline mr-1" />
                                            Vincular
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="border-t border-gray-200 pt-6">
                                    <p class="text-sm text-gray-500 text-center">Todas as disciplinas disponíveis já estão vinculadas a este professor.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <!-- Botão de Exclusão -->
                            <button type="button" 
                                    onclick="openDeleteModal()"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                                <x-heroicon-o-trash class="w-4 h-4 mr-2" />
                                Excluir Professor
                            </button>

                            <!-- Botões de Ação -->
                            <div class="flex space-x-3">
                                <a href="{{ route('admin.professores.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                                    <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                                    Voltar à Lista
                                </a>
                                <a href="{{ route('admin.professores.edit', $professor) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200">
                                    <x-heroicon-o-pencil class="w-4 h-4 mr-2" />
                                    Editar Professor
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
                                Você está prestes a excluir o professor <strong>{{ $professor->nome }}</strong>.
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
                <form id="deleteForm" action="{{ route('admin.professores.destroy', $professor) }}" method="POST">
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
                            Para confirmar, digite o nome completo do professor: <strong>{{ $professor->nome }}</strong>
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
const professorNome = '{{ $professor->nome }}';
const professorId = {{ $professor->id }};

function openDeleteModal() {
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
}

async function checkRelationships() {
    try {
        const response = await fetch(`/admin/professores/${professorId}/relacionamentos`, {
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
        const nameMatches = nameInput.value.trim() === professorNome;
        
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