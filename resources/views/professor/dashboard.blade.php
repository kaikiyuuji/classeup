<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard do Professor') }}
        </h2>
    </x-slot>

    @section('content')
    <div class="container mx-auto px-4 py-6">
        <x-session-messages />
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard do Professor</h1>
            <p class="text-gray-600 mt-2">Bem-vindo, {{ $professor->nome }}!</p>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Minhas Turmas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $turmas->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total de Alunos</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalAlunos }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Disciplinas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $disciplinas->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção de Turmas e Disciplinas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Minhas Turmas -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Minhas Turmas</h3>
                </div>
                <div class="p-6">
                    @if($turmas->count() > 0)
                        <div class="space-y-4">
                            @foreach($turmas as $turma)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $turma->nome }}</h4>
                                            <p class="text-sm text-gray-500">{{ $turma->serie }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $turma->alunos->count() }} alunos</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Ver Detalhes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma turma atribuída</h3>
                            <p class="mt-1 text-sm text-gray-500">Entre em contato com o administrador para vincular suas turmas.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Minhas Disciplinas -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Minhas Disciplinas</h3>
                </div>
                <div class="p-6">
                    @if($disciplinas->count() > 0)
                        <div class="space-y-4">
                            @foreach($disciplinas as $disciplina)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $disciplina->nome }}</h4>
                                            <p class="text-sm text-gray-500">{{ $disciplina->codigo }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $disciplina->carga_horaria }}h</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Gerenciar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma disciplina atribuída</h3>
                            <p class="mt-1 text-sm text-gray-500">Entre em contato com o administrador para vincular suas disciplinas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-app-layout>