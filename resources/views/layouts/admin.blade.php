<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Administração</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Admin Scripts -->
        <script>
            // Confirmação de exclusão
            document.addEventListener('DOMContentLoaded', function() {
                const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
                
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        const message = this.getAttribute('data-confirm-delete') || 'Tem certeza que deseja excluir este item?';
                        
                        if (confirm(message)) {
                            this.closest('form').submit();
                        }
                    });
                });
            });

            // Auto-hide de mensagens de sucesso
            document.addEventListener('DOMContentLoaded', function() {
                const successMessages = document.querySelectorAll('.alert-success');
                
                successMessages.forEach(message => {
                    setTimeout(() => {
                        message.style.transition = 'opacity 0.5s ease-out';
                        message.style.opacity = '0';
                        
                        setTimeout(() => {
                            message.remove();
                        }, 500);
                    }, 5000);
                });
            });

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

            // Validação de formulários
            function validateForm(formId) {
                const form = document.getElementById(formId);
                if (!form) return true;

                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('border-red-500');
                        isValid = false;
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });

                return isValid;
            }

            // Validação de email
            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            // Validação de CPF
            function validateCPF(cpf) {
                cpf = cpf.replace(/[^\d]/g, '');
                
                if (cpf.length !== 11) return false;
                
                // Verifica se todos os dígitos são iguais
                if (/^(\d)\1{10}$/.test(cpf)) return false;
                
                // Validação dos dígitos verificadores
                let sum = 0;
                for (let i = 0; i < 9; i++) {
                    sum += parseInt(cpf.charAt(i)) * (10 - i);
                }
                let digit1 = 11 - (sum % 11);
                if (digit1 > 9) digit1 = 0;
                
                sum = 0;
                for (let i = 0; i < 10; i++) {
                    sum += parseInt(cpf.charAt(i)) * (11 - i);
                }
                let digit2 = 11 - (sum % 11);
                if (digit2 > 9) digit2 = 0;
                
                return parseInt(cpf.charAt(9)) === digit1 && parseInt(cpf.charAt(10)) === digit2;
            }

            // Máscara para CPF
            function maskCPF(input) {
                let value = input.value.replace(/\D/g, '');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                input.value = value;
            }

            // Máscara para telefone
            function maskPhone(input) {
                let value = input.value.replace(/\D/g, '');
                if (value.length <= 10) {
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{4})(\d)/, '$1-$2');
                } else {
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                }
                input.value = value;
            }

            // Máscara para CEP
            function maskCEP(input) {
                let value = input.value.replace(/\D/g, '');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                input.value = value;
            }
        </script>
    </body>
</html>