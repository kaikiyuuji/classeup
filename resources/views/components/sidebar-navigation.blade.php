@props(['userType' => 'admin'])

<div class="bg-white">
    <!-- Sidebar -->
    <div class="sidebar flex flex-col">
        
        <!-- Logo -->
        <div class="flex items-center justify-center p-4 border-b border-gray-200">
            <div class="flex items-center space-x-4 mr-2">
                <x-application-logo class="w-8 h-8 fill-current text-gray-500" />
                <span class="text-xl font-bold text-gray-800">ClasseUp</span>
            </div>
        </div>

        <!-- Informações do Usuário -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    @if($userType === 'admin')
                        <x-heroicon-o-cog class="w-6 h-6 text-blue-600" />
                    @elseif($userType === 'professor')
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-blue-600" />
                    @else
                        <x-heroicon-o-user class="w-6 h-6 text-blue-600" />
                    @endif
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ $userType }}</p>
                </div>
            </div>
        </div>

        <!-- Menu de Navegação -->
        <nav class="mt-6 px-3">
            @if($userType === 'admin')
                <!-- Menu Administrativo -->
                <div class="space-y-1">
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                        Dashboard
                    </x-sidebar-link>
                    
                    <div class="pt-4">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestão</p>
                        <div class="mt-2 space-y-1">
                            <x-sidebar-link :href="route('admin.alunos.index')" :active="request()->routeIs('admin.alunos.*')" icon="user">
                                Alunos
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.professores.index')" :active="request()->routeIs('admin.professores.*')" icon="academic-cap">
                                Professores
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.disciplinas.index')" :active="request()->routeIs('admin.disciplinas.*')" icon="book-open">
                                Disciplinas
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.turmas.index')" :active="request()->routeIs('admin.turmas.*')" icon="user-group">
                                Turmas
                            </x-sidebar-link>
                        </div>
                    </div>
                    
                    <div class="pt-4">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sistema</p>
                        <div class="mt-2 space-y-1">
                            <x-sidebar-link :href="route('admin.chamadas.index')" :active="request()->routeIs('admin.chamadas.*')" icon="clipboard-document-list">
                                Chamadas
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.usuarios.professores')" :active="request()->routeIs('admin.usuarios.*')" icon="users">
                                Usuários
                            </x-sidebar-link>
                        </div>
                    </div>
                </div>
            @elseif($userType === 'professor')
                <!-- Menu do Professor -->
                <div class="space-y-1">
                    <x-sidebar-link :href="route('professor.dashboard')" :active="request()->routeIs('professor.dashboard')" icon="home">
                        Meu Dashboard
                    </x-sidebar-link>
                    
                    <div class="pt-4">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ensino</p>
                        <div class="mt-2 space-y-1">
                            <x-sidebar-link :href="route('professor.turmas.index')" :active="request()->routeIs('professor.turmas.*')" icon="user-group">
                                Minhas Turmas
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('professor.chamadas.index')" :active="request()->routeIs('professor.chamadas.*')" icon="clipboard-document-list">
                                Chamadas
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('professor.notas.index')" :active="request()->routeIs('professor.notas.*')" icon="document-text">
                                Notas
                            </x-sidebar-link>
                        </div>
                    </div>
                </div>
            @else
                <!-- Menu do Aluno -->
                <div class="space-y-1">
                    <x-sidebar-link :href="route('aluno.dashboard')" :active="request()->routeIs('aluno.dashboard')" icon="home">
                        Meu Painel
                    </x-sidebar-link>
                    
                    <div class="pt-4">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Acadêmico</p>
                        <div class="mt-2 space-y-1">
                            <x-sidebar-link :href="route('aluno.boletim')" :active="request()->routeIs('aluno.boletim')" icon="document-text">
                                Meu Boletim
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('aluno.chamadas.index')" :active="request()->routeIs('aluno.chamadas.*')" icon="calendar">
                                Minhas Presenças
                            </x-sidebar-link>
                        </div>
                    </div>
                </div>
            @endif
        </nav>

        <!-- Menu de Configurações -->
        <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-gray-200">
            <div class="space-y-1">
                <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="cog-6-tooth">
                    Configurações
                </x-sidebar-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-150">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 mr-3" />
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="main-content">
        <!-- Header -->
        @isset($header)
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="h-16 px-4 sm:px-6 lg:px-8 flex items-center">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Conteúdo da página -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-white">
            {{ $slot }}
        </main>
    </div>
</div>