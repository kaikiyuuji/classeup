<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white">
        <div class="min-h-screen bg-white flex">
            <!-- Sidebar -->
            <x-sidebar-navigation :userType="auth()->user()->tipo_usuario" />
            
            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow-sm border-b border-gray-200">
                        <div class="mx-auto py-4 px-6">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Session Messages são exibidas via componente x-session-messages nas páginas individuais -->

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-white px-6 py-8 min-h-screen">
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot }}
                    @endif
                </main>
            </div>
        </div>
        
        <script>
            // Image preview functions
            function previewImage(input) {
                const file = input.files[0];
                const preview = document.getElementById('preview');
                const previewContainer = document.getElementById('imagePreview');
                const errorContainer = document.getElementById('imageError');
                
                // Remove previous error messages
                if (errorContainer) {
                    errorContainer.remove();
                }
                
                if (!file) {
                    previewContainer.classList.add('hidden');
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    showImageError(input, 'Tipo de arquivo não permitido. Use apenas JPEG, PNG ou GIF.');
                    input.value = '';
                    return;
                }
                
                // Validate file size (2MB = 2048KB)
                const maxSize = 2048 * 1024; // 2MB in bytes
                if (file.size > maxSize) {
                    showImageError(input, 'Arquivo muito grande. O tamanho máximo é 2MB.');
                    input.value = '';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    
                    // Update file information
                    const fileName = document.getElementById('fileName');
                    const fileSize = document.getElementById('fileSize');
                    const fileType = document.getElementById('fileType');
                    
                    if (fileName) fileName.textContent = file.name;
                    if (fileSize) fileSize.textContent = formatFileSize(file.size);
                    if (fileType) fileType.textContent = file.type;
                    
                    previewContainer.classList.remove('hidden');
                    
                    // Add fade-in animation
                    previewContainer.style.opacity = '0';
                    setTimeout(() => {
                        previewContainer.style.transition = 'opacity 0.3s ease-in-out';
                        previewContainer.style.opacity = '1';
                    }, 10);
                };
                reader.readAsDataURL(file);
            }

            function removeImage() {
                const input = document.getElementById('foto_perfil');
                const preview = document.getElementById('preview');
                const previewContainer = document.getElementById('imagePreview');
                const errorContainer = document.getElementById('imageError');
                
                input.value = '';
                preview.src = '';
                previewContainer.classList.add('hidden');
                
                // Remove error messages
                if (errorContainer) {
                    errorContainer.remove();
                }
            }
            
            function showImageError(input, message) {
                // Remove existing error
                const existingError = document.getElementById('imageError');
                if (existingError) {
                    existingError.remove();
                }
                
                // Create error element
                const errorDiv = document.createElement('div');
                errorDiv.id = 'imageError';
                errorDiv.className = 'mt-2 text-sm text-red-600 flex items-center';
                errorDiv.innerHTML = `
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    ${message}
                `;
                
                // Insert after the file input container
                const fileContainer = input.closest('.border-dashed');
                if (fileContainer && fileContainer.parentNode) {
                    fileContainer.parentNode.insertBefore(errorDiv, fileContainer.nextSibling);
                }
            }
            
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        </script>
        
        <!-- Máscaras de formatação -->
        <script src="{{ asset('js/masks.js') }}"></script>
    </body>
</html>
