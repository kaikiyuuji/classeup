<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    {{ __('Cadastrar Nova Turma') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Preencha as informações para criar uma nova turma</p>
            </div>
            <a href="{{ route('admin.turmas.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <x-icons.arrow-left class="w-4 h-4 mr-2" />
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-8">
                    <form action="{{ route('admin.turmas.store') }}" method="POST" class="space-y-8">
                        @csrf

                        <!-- Informações da Turma -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-icons.document class="w-5 h-5 mr-2 text-blue-600" />
                                Informações da Turma
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Nome -->
                                <div class="col-span-2">
                                    <label for="nome" class="block text-sm font-semibold text-gray-700 mb-2">Nome da Turma *</label>
                                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="Digite o nome da turma (ex: 3º Ano A)"
                                           required>
                                    @error('nome')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <x-icons.exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Ano Letivo -->
                                <div>
                                    <label for="ano_letivo" class="block text-sm font-semibold text-gray-700 mb-2">Ano Letivo *</label>
                                    <select name="ano_letivo" id="ano_letivo" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                            required>
                                        <option value="">Selecione o ano letivo</option>
                                        @for($year = 2020; $year <= 2030; $year++)
                                            <option value="{{ $year }}" {{ old('ano_letivo') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('ano_letivo')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <x-icons.exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Nível Educacional -->
                                <div>
                                    <label for="serie" class="block text-sm font-semibold text-gray-700 mb-2">Nível Educacional *</label>
                                    <select name="serie" id="serie" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                            required>
                                        <option value="">Selecione o nível educacional</option>
                                        @foreach(App\Models\Turma::getNiveisEducacionais() as $valor => $label)
                                            <option value="{{ $valor }}" {{ old('serie') == $valor ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('serie')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <x-icons.exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Turno -->
                                <div>
                                    <label for="turno" class="block text-sm font-semibold text-gray-700 mb-2">Turno *</label>
                                    <select name="turno" id="turno" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                            required>
                                        <option value="">Selecione o turno</option>
                                        <option value="matutino" {{ old('turno') === 'matutino' ? 'selected' : '' }}>Matutino</option>
                                        <option value="vespertino" {{ old('turno') === 'vespertino' ? 'selected' : '' }}>Vespertino</option>
                                        <option value="noturno" {{ old('turno') === 'noturno' ? 'selected' : '' }}>Noturno</option>
                                    </select>
                                    @error('turno')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <x-icons.exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Capacidade Máxima -->
                                <div>
                                    <label for="capacidade_maxima" class="block text-sm font-semibold text-gray-700 mb-2">Capacidade Máxima *</label>
                                    <input type="number" name="capacidade_maxima" id="capacidade_maxima" value="{{ old('capacidade_maxima') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="Número máximo de alunos"
                                           min="1" max="50"
                                           required>
                                    @error('capacidade_maxima')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <x-icons.exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-icons.check-circle class="w-5 h-5 mr-2 text-green-600" />
                                Status
                            </h3>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="ativo" id="ativo" value="1" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       {{ old('ativo', true) ? 'checked' : '' }}>
                                <label for="ativo" class="ml-2 block text-sm text-gray-700">
                                    Turma ativa
                                </label>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Turmas ativas podem receber matrículas de alunos.
                            </p>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.turmas.index') }}" 
                               class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="inline-flex justify-center items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                                <x-icons.check class="w-4 h-4 mr-2" />
                                Cadastrar Turma
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>