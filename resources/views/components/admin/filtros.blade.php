@props([
    'action' => '',
    'filtros' => [],
    'valorAtual' => [],
    'placeholder' => 'Buscar...',
    'showSearch' => true,
    'showStatus' => false,
    'showTurma' => false,
    'showDisciplina' => false,
    'showEspecialidade' => false,
    'showNivelEducacional' => false,
    'showTurno' => false,
    'turmas' => [],
    'disciplinas' => [],
    'especialidades' => []
])

<div class="bg-white border-b border-gray-200 px-6 py-4">
    <form method="GET" action="{{ $action }}" class="space-y-4">
        <div class="flex flex-wrap gap-4 items-end">
            <!-- Campo de Busca -->
            @if($showSearch)
                <div class="flex-1 min-w-64">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                        Buscar
                    </label>
                    <input 
                        type="text" 
                        name="search" 
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="{{ $placeholder }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                </div>
            @endif

            <!-- Filtro de Status -->
            @if($showStatus)
                <div class="min-w-40">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status
                    </label>
                    <select 
                        name="status" 
                        id="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                        <option value="">Todos</option>
                        <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
            @endif

            <!-- Filtro de Turma -->
            @if($showTurma && count($turmas) > 0)
                <div class="min-w-48">
                    <label for="turma_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Turma
                    </label>
                    <select 
                        name="turma_id" 
                        id="turma_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                        <option value="">Todas as turmas</option>
                        @foreach($turmas as $turma)
                            <option value="{{ $turma->id }}" {{ request('turma_id') == $turma->id ? 'selected' : '' }}>
                                {{ $turma->nome . " - " . ucfirst($turma->serie) . " - " . ucfirst($turma->turno) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Filtro de Disciplina -->
            @if($showDisciplina && count($disciplinas) > 0)
                <div class="min-w-48">
                    <label for="disciplina_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Disciplina
                    </label>
                    <select 
                        name="disciplina_id" 
                        id="disciplina_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                        <option value="">Todas as disciplinas</option>
                        @foreach($disciplinas as $disciplina)
                            <option value="{{ $disciplina->id }}" {{ request('disciplina_id') == $disciplina->id ? 'selected' : '' }}>
                                {{ $disciplina->nome . " - " . $disciplina->codigo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Filtro de Especialidade -->
            @if($showEspecialidade && count($especialidades) > 0)
                <div class="min-w-48">
                    <label for="especialidade" class="block text-sm font-medium text-gray-700 mb-1">
                        Especialidade
                    </label>
                    <select 
                        name="especialidade" 
                        id="especialidade"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                        <option value="">Todas as especialidades</option>
                        @foreach($especialidades as $especialidade)
                            <option value="{{ $especialidade }}" {{ request('especialidade') === $especialidade ? 'selected' : '' }}>
                                {{ $especialidade }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Filtro de Nível Educacional -->
            @if($showNivelEducacional)
                <div class="min-w-48">
                    <label for="nivel_educacional" class="block text-sm font-medium text-gray-700 mb-1">
                        Nível Educacional
                    </label>
                    <select 
                        name="nivel_educacional" 
                        id="nivel_educacional"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                        <option value="">Todos os níveis</option>
                        <option value="pré-escola" {{ request('nivel_educacional') === 'pré-escola' ? 'selected' : '' }}>Pré-escola</option>
                        <option value="fundamental" {{ request('nivel_educacional') === 'fundamental' ? 'selected' : '' }}>Fundamental</option>
                        <option value="médio" {{ request('nivel_educacional') === 'médio' ? 'selected' : '' }}>Médio</option>
                    </select>
                </div>
            @endif

            <!-- Filtro de Turno -->
            @if($showTurno)
                <div class="min-w-40">
                    <label for="turno" class="block text-sm font-medium text-gray-700 mb-1">
                        Turno
                    </label>
                    <select 
                        name="turno" 
                        id="turno"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                        <option value="">Todos os turnos</option>
                        <option value="matutino" {{ request('turno') === 'matutino' ? 'selected' : '' }}>Matutino</option>
                        <option value="vespertino" {{ request('turno') === 'vespertino' ? 'selected' : '' }}>Vespertino</option>
                        <option value="noturno" {{ request('turno') === 'noturno' ? 'selected' : '' }}>Noturno</option>
                        <option value="integral" {{ request('turno') === 'integral' ? 'selected' : '' }}>Integral</option>
                    </select>
                </div>
            @endif

            <!-- Botões de Ação -->
            <div class="flex gap-2">
                <button 
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filtrar
                </button>
                
                @if(request()->hasAny(['search', 'status', 'turma_id', 'disciplina_id', 'especialidade', 'nivel_educacional', 'turno']))
                    <a 
                        href="{{ $action }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Limpar
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>