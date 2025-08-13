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
    <body class="font-sans antialiased">
        @php
            $userType = 'admin'; // Default
            if (Auth::check()) {
                $userType = Auth::user()->tipo_usuario;
            }
        @endphp

        <x-sidebar-navigation :user-type="$userType">
            @isset($header)
                <x-slot name="header">
                    {{ $header }}
                </x-slot>
            @endisset

            <!-- Session Messages -->
            <div class="px-4 sm:px-6 lg:px-8 pt-6">
                <x-session-messages />
            </div>
            
            @hasSection('content')
                <div class="px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            @else
                {{ $slot }}
            @endif
        </x-sidebar-navigation>
        
        <script>
            // Image preview functions
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const preview = document.getElementById('imagePreview');
                        if (preview) {
                            preview.src = e.target.result;
                            preview.style.display = 'block';
                        }
                    }
                    
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function removeImage() {
                const input = document.getElementById('foto_perfil');
                const preview = document.getElementById('imagePreview');
                const removeInput = document.getElementById('remove_foto');
                
                if (input) input.value = '';
                if (preview) preview.style.display = 'none';
                if (removeInput) removeInput.value = '1';
            }

            // Auto-hide alerts
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('[data-auto-hide]');
                alerts.forEach(function(alert) {
                    setTimeout(function() {
                        alert.style.transition = 'opacity 0.5s ease-out';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.remove();
                        }, 500);
                    }, 5000);
                });
            });
        </script>
    </body>
</html>