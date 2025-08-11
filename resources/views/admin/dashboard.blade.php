<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Administrativo') }}
        </h2>
        <p class="text-gray-600 text-sm mt-1">Visão geral do sistema ClasseUp</p>
    </x-slot>

    <div class="py-">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cards de Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <x-icons.user class="w-6 h-6"/>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total de Alunos</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalAlunos }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <x-icons.academic-cap class="w-6 h-6"/>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total de Professores</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalProfessores }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total de Turmas</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalTurmas }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total de Disciplinas</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalDisciplinas }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links de Ação Rápida -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ações Rápidas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('admin.alunos.index') }}" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 transition-colors">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                <x-icons.user class="w-6 h-6"/>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Gerenciar Alunos</p>
                                <p class="text-xs text-gray-500">Cadastrar e editar alunos</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.professores.index') }}" class="bg-green-50 hover:bg-green-100 rounded-lg p-4 transition-colors">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg text-green-600">
                                <x-icons.academic-cap class="w-6 h-6"/>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Gerenciar Professores</p>
                                <p class="text-xs text-gray-500">Cadastrar e editar professores</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.turmas.index') }}" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-4 transition-colors">
                        <div class="flex items-center">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Gerenciar Turmas</p>
                                <p class="text-xs text-gray-500">Criar e organizar turmas</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.disciplinas.index') }}" class="bg-yellow-50 hover:bg-yellow-100 rounded-lg p-4 transition-colors">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Gerenciar Disciplinas</p>
                                <p class="text-xs text-gray-500">Cadastrar disciplinas</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>