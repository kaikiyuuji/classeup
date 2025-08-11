<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    Cadastrar Professor
                </h2>
                <p class="text-sm text-gray-600 mt-1">Preencha as informações do novo professor</p>
            </div>
            <a href="{{ route('admin.professores.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-8">
                    <form action="{{ route('admin.professores.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <!-- Informações Pessoais -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-heroicon-o-user class="w-5 h-5 mr-2 text-purple-600" />
                                Informações Pessoais
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Nome -->
                                <div class="col-span-2">
                                    <label for="nome" class="block text-sm font-semibold text-gray-700 mb-2">Nome Completo *</label>
                                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="Digite o nome completo do professor"
                                           required>
                                    @error('nome')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
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
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
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
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Especialidade -->
                                <div>
                                    <label for="especialidade" class="block text-sm font-semibold text-gray-700 mb-2">Especialidade *</label>
                                    <input type="text" name="especialidade" id="especialidade" value="{{ old('especialidade') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="Ex: Matemática, Português, História"
                                           required>
                                    @error('especialidade')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informações de Contato -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-heroicon-o-envelope class="w-5 h-5 mr-2 text-green-600" />
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
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
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
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Endereço -->
                                <div class="col-span-2">
                                    <label for="endereco" class="block text-sm font-semibold text-gray-700 mb-2">Endereço</label>
                                    <textarea name="endereco" id="endereco" rows="3" 
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                              placeholder="Digite o endereço completo do professor">{{ old('endereco') }}</textarea>
                                    @error('endereco')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Foto de Perfil -->
                                <div class="col-span-2">
                                    <label for="foto_perfil" class="block text-sm font-semibold text-gray-700 mb-2">Foto de Perfil</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors duration-200">
                                        <div class="space-y-1 text-center">
                                            <x-heroicon-o-photo class="mx-auto h-12 w-12 text-gray-400" />
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
                                                    <x-heroicon-o-x-mark class="w-3 h-3 mr-1" />
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
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informações Acadêmicas -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-heroicon-o-book-open class="w-5 h-5 mr-2 text-blue-600" />
                                Informações Acadêmicas
                            </h3>
                            
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Formação -->
                                <div>
                                    <label for="formacao" class="block text-sm font-semibold text-gray-700 mb-2">Formação Acadêmica *</label>
                                    <textarea name="formacao" id="formacao" rows="4" 
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                              placeholder="Descreva a formação acadêmica do professor (graduação, pós-graduação, especializações, etc.)"
                                              required>{{ old('formacao') }}</textarea>
                                    @error('formacao')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <x-heroicon-o-check-circle class="w-5 h-5 mr-2 text-purple-600" />
                                Status do Professor
                            </h3>
                            
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="ativo" 
                                       id="ativo" 
                                       value="1"
                                       {{ old('ativo', true) ? 'checked' : '' }}
                                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="ativo" class="ml-3 block text-sm font-medium text-gray-900">
                                    Professor ativo
                                </label>
                            </div>
                            @error('ativo')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.professores.index') }}" 
                               class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                <x-heroicon-o-x-mark class="w-5 h-5 mr-2" />
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <x-heroicon-o-check class="w-4 h-4 mr-2" />
                                Cadastrar Professor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('fileName').textContent = file.name;
                    document.getElementById('fileSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    document.getElementById('fileType').textContent = file.type;
                    document.getElementById('imagePreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            document.getElementById('foto_perfil').value = '';
            document.getElementById('imagePreview').classList.add('hidden');
        }
    </script>
</x-app-layout>