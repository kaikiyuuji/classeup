<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Justificar Falta') }}
        </h2>
    </x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md max-w-2xl mx-auto">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Justificar Falta</h1>
                <a href="{{ route('faltas.relatorio-aluno', ['matricula' => $falta->matricula]) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <x-icons.arrow-left class="w-4 h-4 mr-2" />Voltar
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Informações da Falta -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-red-800 mb-3">
                    <x-icons.exclamation class="w-5 h-5 mr-2" />
                    Detalhes da Falta
                </h3>
                
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Aluno:</p>
                        <p class="font-medium text-gray-800">{{ $falta->aluno->nome }}</p>
                    </div>
                    
                    <div>
                        <p class="text-gray-600">Matrícula:</p>
                        <p class="font-medium text-gray-800">{{ $falta->matricula }}</p>
                    </div>
                    
                    <div>
                        <p class="text-gray-600">Data da Falta:</p>
                        <p class="font-medium text-gray-800">{{ $falta->data_falta->format('d/m/Y') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-gray-600">Disciplina:</p>
                        <p class="font-medium text-gray-800">{{ $falta->disciplina->nome }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <p class="text-gray-600">Professor:</p>
                        <p class="font-medium text-gray-800">{{ $falta->professor->nome }}</p>
                    </div>
                </div>
            </div>

            <!-- Formulário de Justificativa -->
            <form action="{{ route('faltas.processar-justificativa', $falta->id) }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-2">
                        Justificativa: <span class="text-red-500">*</span>
                    </label>
                    <textarea id="observacoes" 
                              name="observacoes" 
                              rows="5" 
                              placeholder="Digite a justificativa para a falta..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('observacoes') border-red-500 @enderror"
                              required>{{ old('observacoes', $falta->observacoes) }}</textarea>
                    
                    @error('observacoes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <p class="mt-1 text-sm text-gray-500">
                        Máximo de 1000 caracteres. Seja específico sobre o motivo da falta.
                    </p>
                </div>

                <!-- Exemplos de Justificativas -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Exemplos de justificativas válidas:</h4>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Atestado médico apresentado</li>
                            <li>• Consulta médica agendada</li>
                            <li>• Participação em evento escolar</li>
                            <li>• Motivo familiar justificado</li>
                            <li>• Problema de transporte comprovado</li>
                        </ul>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('faltas.relatorio-aluno', ['matricula' => $falta->matricula]) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition-colors">
                        <x-icons.check class="w-4 h-4 mr-2" />Justificar Falta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Contador de caracteres
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('observacoes');
    const maxLength = 1000;
    
    // Criar elemento contador
    const counter = document.createElement('div');
    counter.className = 'text-sm text-gray-500 text-right mt-1';
    textarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const remaining = maxLength - textarea.value.length;
        counter.textContent = `${textarea.value.length}/${maxLength} caracteres`;
        
        if (remaining < 100) {
            counter.className = 'text-sm text-orange-500 text-right mt-1';
        } else if (remaining < 50) {
            counter.className = 'text-sm text-red-500 text-right mt-1';
        } else {
            counter.className = 'text-sm text-gray-500 text-right mt-1';
        }
    }
    
    textarea.addEventListener('input', updateCounter);
    updateCounter(); // Inicializar contador
});
</script>
</x-app-layout>