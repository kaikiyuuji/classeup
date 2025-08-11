<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    {{ __('Editar Turma') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $turma->nome }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.turmas.show', $turma) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <x-heroicon-o-eye class="w-4 h-4 mr-2" />
                    Ver Detalhes
                </a>
                <a href="{{ route('admin.turmas.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-8">
                    <form action="{{ route('admin.turmas.update', $turma) }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Informações da Turma -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-heroicon-o-book-open class="w-5 h-5 mr-2 text-indigo-600" />
                                Informações da Turma
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Nome da Turma -->
                                <div class="col-span-2">
                                    <label for="nome" class="block text-sm font-semibold text-gray-700 mb-2">Nome da Turma *</label>
                                    <input type="text" name="nome" id="nome" value="{{ old('nome', $turma->nome) }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                                           placeholder="Digite o nome da turma"
                                           required>
                                    @error('nome')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Ano Letivo -->
                                <div>
                                    <label for="ano_letivo" class="block text-sm font-semibold text-gray-700 mb-2">Ano Letivo *</label>
                                    <input type="number" name="ano_letivo" id="ano_letivo" value="{{ old('ano_letivo', $turma->ano_letivo) }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                                           placeholder="2024"
                                           min="2020" max="2030"
                                           required>
                                    @error('ano_letivo')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Nível Educacional -->
                                <div>
                                    <label for="serie" class="block text-sm font-semibold text-gray-700 mb-2">Nível Educacional *</label>
                                    <select name="serie" id="serie" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                                            required>
                                        <option value="">Selecione o nível educacional</option>
                                        @foreach(App\Models\Turma::getNiveisEducacionais() as $valor => $label)
                                            <option value="{{ $valor }}" {{ old('serie', $turma->serie) == $valor ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('serie')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Turno -->
                                <div>
                                    <label for="turno" class="block text-sm font-semibold text-gray-700 mb-2">Turno *</label>
                                    <select name="turno" id="turno" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                                            required>
                                        <option value="">Selecione o turno</option>
                                        <option value="matutino" {{ old('turno', $turma->turno) == 'matutino' ? 'selected' : '' }}>Matutino</option>
                                        <option value="vespertino" {{ old('turno', $turma->turno) == 'vespertino' ? 'selected' : '' }}>Vespertino</option>
                                        <option value="noturno" {{ old('turno', $turma->turno) == 'noturno' ? 'selected' : '' }}>Noturno</option>
                                        <option value="integral" {{ old('turno', $turma->turno) == 'integral' ? 'selected' : '' }}>Integral</option>
                                    </select>
                                    @error('turno')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Capacidade Máxima -->
                                <div>
                                    <label for="capacidade_maxima" class="block text-sm font-semibold text-gray-700 mb-2">Capacidade Máxima *</label>
                                    <input type="number" name="capacidade_maxima" id="capacidade_maxima" value="{{ old('capacidade_maxima', $turma->capacidade_maxima) }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                                           placeholder="30"
                                           min="1" max="50"
                                           required>
                                    @error('capacidade_maxima')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status da Turma -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-heroicon-o-check-circle class="w-5 h-5 mr-2 text-green-600" />
                                Status da Turma
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- Status Ativo -->
                                <div class="flex items-center space-x-3">
                                    <input type="hidden" name="ativo" value="0">
                                    <input type="checkbox" name="ativo" id="ativo" value="1" 
                                           {{ old('ativo', $turma->ativo) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                                    <label for="ativo" class="text-sm font-medium text-gray-700">
                                        Turma ativa
                                    </label>
                                </div>
                                <p class="text-sm text-gray-500">
                                    Turmas inativas não aparecem nas listagens principais e não permitem novas matrículas.
                                </p>
                                @error('ativo')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.turmas.show', $turma) }}" 
                               class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                                <x-heroicon-o-x-mark class="w-4 h-4 mr-2" />
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="inline-flex justify-center items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                                <x-heroicon-o-check class="w-4 h-4 mr-2" />
                                Atualizar Turma
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>