<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    Cadastrar Aluno
                </h2>
                <p class="text-sm text-gray-600 mt-1">Preencha as informações do novo aluno</p>
            </div>
            <a href="{{ route('admin.alunos.index') }}" 
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
                    <form action="{{ route('admin.alunos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <!-- Informações Pessoais -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informações Pessoais
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Nome -->
                                <div class="col-span-2">
                                    <label for="nome" class="block text-sm font-semibold text-gray-700 mb-2">Nome Completo *</label>
                                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="Digite o nome completo do aluno"
                                           required>
                                    @error('nome')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- CPF -->
                                <div>
                                    <label for="cpf" class="block text-sm font-semibold text-gray-700 mb-2">CPF *</label>
                                    <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="000.000.000-00"
                                           maxlength="14"
                                           required>
                                    @error('cpf')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Data de Nascimento -->
                                <div>
                                    <label for="data_nascimento" class="block text-sm font-semibold text-gray-700 mb-2">Data de Nascimento *</label>
                                    <input type="date" name="data_nascimento" id="data_nascimento" value="{{ old('data_nascimento') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           required>
                                    @error('data_nascimento')
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

                        <!-- Informações de Contato -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Informações de Contato
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="exemplo@email.com"
                                           required>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Telefone -->
                                <div>
                                    <label for="telefone" class="block text-sm font-semibold text-gray-700 mb-2">Telefone</label>
                                    <input type="text" name="telefone" id="telefone" value="{{ old('telefone') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="(11) 99999-9999"
                                           maxlength="15">
                                    @error('telefone')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Foto de Perfil -->
                                 <div class="col-span-2">
                                    <label for="foto_perfil" class="block text-sm font-semibold text-gray-700 mb-2">Foto de Perfil</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors duration-200">
                                        <div class="space-y-1 text-center">
                                            <x-icons.photo class="mx-auto h-12 w-12 text-gray-400" />
                                            <div class="flex text-sm text-gray-600">
                                                <label for="foto_perfil" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                    <span>Enviar uma foto</span>
                                                    <input id="foto_perfil" name="foto_perfil" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                                                </label>
                                                <p class="pl-1">ou arraste e solte</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF até 2MB</p>
                                        </div>
                                    </div>
                                    <!-- Preview da imagem -->
                                    <div id="imagePreview" class="mt-4 hidden">
                                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                            <div class="flex items-center justify-between mb-3">
                                                <p class="text-sm font-medium text-blue-700">📷 Preview da imagem selecionada</p>
                                                <button type="button" onclick="removeImage()" class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-md transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Remover
                                                </button>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <img id="preview" class="h-20 w-20 object-cover rounded-lg border-2 border-blue-300 shadow-md" src="" alt="Preview">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div id="fileInfo" class="text-sm text-gray-700 space-y-1">
                                                        <div class="flex items-center">
                                                            <span class="font-medium text-gray-600 w-16">Nome:</span> 
                                                            <span id="fileName" class="text-gray-800 truncate"></span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <span class="font-medium text-gray-600 w-16">Tamanho:</span> 
                                                            <span id="fileSize" class="text-gray-800"></span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <span class="font-medium text-gray-600 w-16">Tipo:</span> 
                                                            <span id="fileType" class="text-gray-800"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @error('foto_perfil')
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

                                <!-- Endereço -->
                                <div class="col-span-2">
                                    <label for="endereco" class="block text-sm font-semibold text-gray-700 mb-2">Endereço</label>
                                    <textarea name="endereco" id="endereco" rows="3" 
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                              placeholder="Digite o endereço completo">{{ old('endereco') }}</textarea>
                                    @error('endereco')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Turma -->
                                <div>
                                    <label for="turma_id" class="block text-sm font-semibold text-gray-700 mb-2">Turma</label>
                                    <select name="turma_id" id="turma_id" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">Selecione uma turma (opcional)</option>
                                        @foreach($turmas as $turma)
                                            <option value="{{ $turma->id }}" 
                                                {{ (old('turma_id', $turmaSelecionada) == $turma->id) ? 'selected' : '' }}>
                                                {{ $turma->nome }} - {{ $turma->serie }}ª Série ({{ ucfirst($turma->turno) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('turma_id')
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

                        <!-- Botões de Ação -->
                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.alunos.index') }}" 
                               class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                <x-icons.x class="w-5 h-5 mr-2" />
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <x-icons.plus class="w-4 h-4 mr-2" />
                                Cadastrar Aluno
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>