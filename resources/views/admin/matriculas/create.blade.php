<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    {{ __('Nova Matrícula') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Matricular aluno em uma turma</p>
            </div>
            <a href="{{ route('matriculas.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md">
            <!-- Header -->
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Nova Matrícula</h1>
                        <p class="text-gray-600 mt-1">Matricule um aluno em uma turma</p>
                    </div>
                    <a href="{{ route('matriculas.index') }}" 
                       class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-times text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Formulário -->
            <form action="{{ route('matriculas.store') }}" method="POST" class="p-6">
                @csrf

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
                            <option value="{{ $aluno->id }}" {{ old('aluno_id') == $aluno->id ? 'selected' : '' }}>
                                {{ $aluno->nome }} - {{ $aluno->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('aluno_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
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
                                    {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
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
                           value="{{ old('data_matricula', date('Y-m-d')) }}"
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
                        <option value="ativa" {{ old('status', 'ativa') == 'ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="inativa" {{ old('status') == 'inativa' ? 'selected' : '' }}>Inativa</option>
                        <option value="transferida" {{ old('status') == 'transferida' ? 'selected' : '' }}>Transferida</option>
                        <option value="cancelada" {{ old('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botões -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('matriculas.index') }}" 
                       class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>Matricular
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