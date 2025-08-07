<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gerenciamento de Usuários
                </h2>
                <p class="text-gray-600 text-sm mt-1">Gerencie contas de acesso para alunos</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.usuarios.professores') }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg shadow-sm hover:bg-gray-50 transition-colors duration-200">
                   
                    <x-icons.academic-cap class="w-4 h-4 mr-2" />
                    Professores
                </a>
                <a href="{{ route('admin.usuarios.alunos') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <x-icons.user-group class="w-4 h-4 mr-2" />
                    Alunos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
           

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icons.user-group class="h-8 w-8 text-indigo-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total de Alunos
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        {{ $alunos->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icons.check-circle class="h-8 w-8 text-green-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Com Usuário Ativo
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        {{ $alunos->filter(fn($a) => $a->user && $a->user->email_verified_at)->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icons.pause-circle class="h-8 w-8 text-yellow-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Usuários Inativos
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        {{ $alunos->filter(fn($a) => $a->user && !$a->user->email_verified_at)->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icons.user-minus class="h-8 w-8 text-gray-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Sem Usuário
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        {{ $alunos->filter(fn($a) => !$a->user)->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">        
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                        <x-icons.check-circle class="w-5 h-5 mr-2 text-green-600" />
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                        <x-icons.x-circle class="w-5 h-5 mr-2 text-red-600" />
                        {{ session('error') }}
                    </div>
                @endif

                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    @if($alunos->count() > 0)
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Aluno
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Matrícula
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Turma
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Status do Usuário
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Email do Sistema
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($alunos as $aluno)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                            <x-icons.user class="h-5 w-5 text-indigo-600" />
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $aluno->nome }}</div>
                                                        <div class="text-sm text-gray-500 font-mono">{{ $aluno->cpf }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 font-mono">
                                                    {{ $aluno->numero_matricula }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($aluno->turma)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $aluno->turma->nome }}
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-500 italic">Sem turma</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($aluno->user)
                                                    @if($aluno->user->email_verified_at)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <x-icons.check-circle class="w-3 h-3 mr-1" />
                                                            Ativo
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <x-icons.clock class="w-3 h-3 mr-1" />
                                                            Inativo
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <x-icons.x-circle class="w-3 h-3 mr-1" />
                                                        Sem usuário
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($aluno->user)
                                                    <div class="text-sm text-gray-900 font-mono">{{ $aluno->user->email }}</div>
                                                @else
                                                    <div class="text-sm text-gray-500 italic">Não criado</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if(!$aluno->user)
                                                    <form action="{{ route('admin.usuarios.vincular-aluno', $aluno->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                            <x-icons.user-plus class="w-4 h-4 mr-1" />
                                                            Criar Usuário
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="flex justify-end space-x-2">
                                                        @if($aluno->user->email_verified_at)
                                                            <form action="{{ route('admin.usuarios.desativar', $aluno->user->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="inline-flex items-center p-2 border border-transparent rounded-md text-yellow-600 hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" title="Desativar usuário">
                                                                    <x-icons.x-circle class="w-4 h-4" />
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('admin.usuarios.ativar', $aluno->user->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="inline-flex items-center p-2 border border-transparent rounded-md text-green-600 hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" title="Ativar usuário">
                                                                    <x-icons.check class="w-4 h-4" />
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-500">
                                    <x-icons.information-circle class="w-4 h-4 mr-1" />
                                    Total de alunos: {{ $alunos->count() }}
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <x-icons.key class="w-4 h-4 mr-1" />
                                    Senha padrão: CPF (apenas números)
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <x-icons.user-group class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum aluno encontrado</h3>
                            <p class="text-gray-500">Cadastre alunos para gerenciar seus usuários do sistema.</p>
                        </div>
                    @endif
                </div>       
            </div>
        </div>
    </div>
</x-app-layout>