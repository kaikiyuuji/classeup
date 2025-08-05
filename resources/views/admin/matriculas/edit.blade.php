<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    Editar Matrícula
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $matricula->aluno->nome }} - {{ $matricula->turma->nome }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('matriculas.show', $matricula) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Visualizar
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
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md">
            <!-- Header -->
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Editar Matrícula</h1>
                        <p class="text-gray-600 mt-1">Atualize as informações da matrícula</p>
                    </div>
                    <a href="{{ route('matriculas.show', $matricula) }}" 
                       class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-times text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Informações Atuais -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 mr-3">
                            @if($matricula->aluno->foto_perfil)
                                <img class="h-10 w-10 rounded-full object-cover" 
                                     src="{{ Storage::url($matricula->aluno->foto_perfil) }}" 
                                     alt="{{ $matricula->aluno->nome }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $matricula->aluno->nome }}</div>
                            <div class="text-xs text-gray-500">{{ $matricula->aluno->email }}</div>
                        </div>
                    </div>
                    <div class="text-gray-400">→</div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $matricula->turma->nome }}</div>
                        <div class="text-xs text-gray-500">{{ $matricula->turma->ano_letivo }}</div>
                    </div>
                </div>
            </div>

            <!-- Formulário -->
            <form action="{{ route('matriculas.update', $matricula) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <!-- Aluno -->
                <div class="mb-6">
                    <label for="aluno_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Aluno *
                    </label>
                    <select name="aluno_id" id="aluno_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('aluno_id') border-red-500 @enderror"
                            required>
                        <option value="">Selecione um aluno</option>
                        @foreach($alunos as $aluno)
                            <option value="{{ $aluno->id }}" 
                                    {{ (old('aluno_id', $matricula->aluno_id) == $aluno->id) ? 'selected' : '' }}>
                                {{ $aluno->nome }} - {{ $aluno->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('aluno_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Alterar o aluno criará uma nova associação
                    </p>
                </div>

                <!-- Turma -->
                <div class="mb-6">
                    <label for="turma_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Turma *
                    </label>
                    <select name="turma_id" id="turma_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('turma_id') border-red-500 @enderror"
                            required>
                        <option value="">Selecione uma turma</option>
                        @foreach($turmas as $turma)
                            <option value="{{ $turma->id }}" 
                                    data-capacidade="{{ $turma->capacidade_maxima }}"
                                    {{ (old('turma_id', $matricula->turma_id) == $turma->id) ? 'selected' : '' }}>
                                {{ $turma->nome }} - {{ $turma->ano_letivo }} ({{ $turma->periodo }})
                            </option>
                        @endforeach
                    </select>
                    @error('turma_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <div id="capacidade-info" class="text-sm text-gray-600 mt-1 hidden">
                        <i class="fas fa-info-circle"></i>
                        <span id="capacidade-text"></span>
                    </div>
                </div>

                <!-- Data da Matrícula -->
                <div class="mb-6">
                    <label for="data_matricula" class="block text-sm font-medium text-gray-700 mb-2">
                        Data da Matrícula *
                    </label>
                    <input type="date" name="data_matricula" id="data_matricula" 
                           value="{{ old('data_matricula', $matricula->data_matricula->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('data_matricula') border-red-500 @enderror"
                           required>
                    @error('data_matricula')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status *
                    </label>
                    <select name="status" id="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror"
                            required>
                        <option value="ativa" {{ old('status', $matricula->status) == 'ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="inativa" {{ old('status', $matricula->status) == 'inativa' ? 'selected' : '' }}>Inativa</option>
                        <option value="transferida" {{ old('status', $matricula->status) == 'transferida' ? 'selected' : '' }}>Transferida</option>
                        <option value="cancelada" {{ old('status', $matricula->status) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2"></i>
                            <div class="text-sm text-yellow-800">
                                <strong>Atenção:</strong> Alterar o status pode afetar a disponibilidade de vagas na turma.
                                <ul class="mt-1 list-disc list-inside text-xs">
                                    <li><strong>Ativa:</strong> Aluno está frequentando a turma</li>
                                    <li><strong>Inativa:</strong> Matrícula temporariamente suspensa</li>
                                    <li><strong>Transferida:</strong> Aluno foi transferido para outra turma</li>
                                    <li><strong>Cancelada:</strong> Matrícula foi cancelada definitivamente</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações de Auditoria -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Informações de Auditoria</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Criada em:</span>
                            {{ $matricula->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <span class="font-medium">Última atualização:</span>
                            {{ $matricula->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('matriculas.show', $matricula) }}" 
                       class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>Atualizar Matrícula
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const turmaSelect = document.getElementById('turma_id');
    const capacidadeInfo = document.getElementById('capacidade-info');
    const capacidadeText = document.getElementById('capacidade-text');

    turmaSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value && selectedOption.dataset.capacidade) {
            const capacidade = selectedOption.dataset.capacidade;
            capacidadeText.textContent = `Capacidade máxima: ${capacidade} alunos`;
            capacidadeInfo.classList.remove('hidden');
        } else {
            capacidadeInfo.classList.add('hidden');
        }
    });

    // Trigger change event if there's a pre-selected value
    if (turmaSelect.value) {
        turmaSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
</x-app-layout>