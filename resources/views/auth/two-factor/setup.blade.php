<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configurar autenticação em duas etapas</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-8 space-y-6">
                @if (session('error'))
                    <div class="px-4 py-2 bg-red-50 text-red-700 rounded">{{ session('error') }}</div>
                @endif

                <p class="text-sm text-gray-600">
                    1. Instale um app autenticador (Google Authenticator, 1Password, Authy).
                </p>
                <p class="text-sm text-gray-600">
                    2. Escaneie o QR code abaixo ou use o link:
                </p>

                <div class="bg-gray-100 p-4 rounded font-mono text-xs break-all">{{ $qrUrl }}</div>

                <p class="text-sm text-gray-600">
                    3. Digite o código de 6 dígitos exibido no app:
                </p>

                <form method="POST" action="{{ route('two-factor.confirm') }}" class="space-y-4">
                    @csrf
                    <input type="text" name="codigo" maxlength="6" inputmode="numeric" required autofocus
                           class="w-32 text-center text-2xl font-mono rounded border-gray-300">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Confirmar
                    </button>
                </form>

                <div class="border-t pt-4">
                    <h3 class="font-semibold text-gray-800">Códigos de recuperação</h3>
                    <p class="text-xs text-gray-500 mb-2">Guarde estes códigos. Cada um vale uma vez se você perder acesso ao app.</p>
                    <ul class="font-mono text-sm grid grid-cols-2 gap-2">
                        @foreach ($recoveryCodes as $code)
                            <li class="bg-gray-50 px-3 py-1 rounded">{{ $code }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
