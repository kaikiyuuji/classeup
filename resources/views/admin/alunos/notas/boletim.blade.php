<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900">
            Boletim de {{ $aluno->nome }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            Matrícula: {{ $aluno->numero_matricula }} | 
            Turma: {{ $aluno->turma->nome ?? 'Não vinculado' }}
        </p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Boletim de {{ $aluno->nome }}</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Matrícula: {{ $aluno->numero_matricula }} | 
                        Turma: {{ $aluno->turma->nome ?? 'Não vinculado' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('alunos.show', $aluno) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar ao Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Boletim Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Notas por Disciplina</h2>
            <p class="text-sm text-gray-600 mt-1">Clique em "Salvar" após alterar as notas para persistir as mudanças.</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disciplina</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">AV1</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">AV2</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">AV3</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">AV4</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Substitutiva</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rec. Final</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nota Final</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Situação</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($avaliacoes as $avaliacao)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $avaliacao->disciplina->nome }}</div>
                                <div class="text-sm text-gray-500">{{ $avaliacao->disciplina->codigo }}</div>
                            </td>
                            
                            <!-- Form para atualizar notas -->
                            <form id="form-{{ $avaliacao->id }}" action="{{ route('alunos.avaliacoes.update', [$aluno, $avaliacao]) }}" method="POST" class="contents">
                                @csrf
                                @method('PUT')
                                
                                <!-- AV1 -->
                                <td class="px-4 py-4 text-center">
                                    <input type="number" 
                                           name="av1" 
                                           value="{{ old('av1', $avaliacao->av1) }}" 
                                           step="0.1" 
                                           min="0" 
                                           max="10" 
                                           class="w-16 px-2 py-1 text-center border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           onchange="markFormChanged({{ $avaliacao->id }})">
                                </td>
                                
                                <!-- AV2 -->
                                <td class="px-4 py-4 text-center">
                                    <input type="number" 
                                           name="av2" 
                                           value="{{ old('av2', $avaliacao->av2) }}" 
                                           step="0.1" 
                                           min="0" 
                                           max="10" 
                                           class="w-16 px-2 py-1 text-center border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           onchange="markFormChanged({{ $avaliacao->id }})">
                                </td>
                                
                                <!-- AV3 -->
                                <td class="px-4 py-4 text-center">
                                    <input type="number" 
                                           name="av3" 
                                           value="{{ old('av3', $avaliacao->av3) }}" 
                                           step="0.1" 
                                           min="0" 
                                           max="10" 
                                           class="w-16 px-2 py-1 text-center border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           onchange="markFormChanged({{ $avaliacao->id }})">
                                </td>
                                
                                <!-- AV4 -->
                                <td class="px-4 py-4 text-center">
                                    <input type="number" 
                                           name="av4" 
                                           value="{{ old('av4', $avaliacao->av4) }}" 
                                           step="0.1" 
                                           min="0" 
                                           max="10" 
                                           class="w-16 px-2 py-1 text-center border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           onchange="markFormChanged({{ $avaliacao->id }})">
                                </td>
                                
                                <!-- Substitutiva -->
                                <td class="px-4 py-4 text-center">
                                    <input type="number" 
                                           name="substitutiva" 
                                           value="{{ old('substitutiva', $avaliacao->substitutiva) }}" 
                                           step="0.1" 
                                           min="0" 
                                           max="10" 
                                           class="w-16 px-2 py-1 text-center border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           onchange="markFormChanged({{ $avaliacao->id }})">
                                </td>
                                
                                <!-- Recuperação Final -->
                                <td class="px-4 py-4 text-center">
                                    <input type="number" 
                                           name="recuperacao_final" 
                                           value="{{ old('recuperacao_final', $avaliacao->recuperacao_final) }}" 
                                           step="0.1" 
                                           min="0" 
                                           max="10" 
                                           class="w-16 px-2 py-1 text-center border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           onchange="markFormChanged({{ $avaliacao->id }})">
                                </td>
                            </form>
                            
                            <!-- Nota Final (calculada) -->
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $avaliacao->nota_final >= 7 ? 'bg-green-100 text-green-800' : ($avaliacao->nota_final >= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ number_format($avaliacao->nota_final, 1) }}
                                </span>
                            </td>
                            
                            <!-- Situação -->
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $avaliacao->situacao === 'aprovado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($avaliacao->situacao) }}
                                </span>
                            </td>
                            
                            <!-- Ações -->
                            <td class="px-4 py-4 text-center">
                                <button type="submit" 
                                        form="form-{{ $avaliacao->id }}"
                                        id="btn-{{ $avaliacao->id }}"
                                        class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                        disabled>
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Salvar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma avaliação encontrada</h3>
                                    <p class="text-gray-600">Este aluno não possui avaliações cadastradas ou não está vinculado a uma turma com disciplinas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<script>
// Função para marcar que o formulário foi alterado
function markFormChanged(avaliacaoId) {
    const button = document.getElementById(`btn-${avaliacaoId}`);
    button.disabled = false;
    button.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed');
    button.classList.add('bg-green-600', 'hover:bg-green-700');
    button.innerHTML = `
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        Salvar Alterações
    `;
}

// Resetar botões após submit
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[id^="form-"]');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const button = form.querySelector('button[type="submit"]');
            button.disabled = true;
            button.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Salvando...
            `;
        });
    });
});
</script>
        </div>
    </div>
</x-app-layout>