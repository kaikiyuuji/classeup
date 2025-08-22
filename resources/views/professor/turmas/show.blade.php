<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes da Turma') }} - {{ $turma->nome }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-session-messages />
            
            <!-- Navegação -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('professor.turmas.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <x-heroicon-o-building-office class="w-4 h-4 mr-2" />
                                Minhas Turmas
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-400" />
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $turma->nome }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Informações da Turma -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                <div class="px-6 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                <x-heroicon-o-building-office class="w-8 h-8 text-white" />
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">{{ $turma->nome }}</h1>
                                <div class="flex items-center space-x-4 mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <x-heroicon-o-academic-cap class="w-4 h-4 mr-1" />
                                        {{ $turma->serie }}
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                                        {{ ucfirst($turma->turno) }}
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        Capacidade: {{ $turma->capacidade_maxima }} alunos
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Professor</p>
                            <p class="font-semibold text-gray-900">{{ $professor->nome }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas da Turma -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <x-heroicon-o-users class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total de Alunos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $alunos->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <x-heroicon-o-book-open class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Disciplinas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $disciplinas->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <x-heroicon-o-clipboard-document-check class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ocupação</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format(($alunos->count() / $turma->capacidade_maxima) * 100, 1) }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <x-heroicon-o-calendar-days class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Vagas Restantes</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $turma->capacidade_maxima - $alunos->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disciplinas Ministradas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-book-open class="w-5 h-5 mr-2 text-green-600" />
                        Disciplinas Ministradas
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Disciplinas que você ministra nesta turma</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($disciplinas as $disciplina)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center mr-3">
                                        <x-heroicon-o-book-open class="w-5 h-5 text-white" />
                                    </div>
                                    <div>
                                        <h6 class="font-medium text-gray-900">{{ $disciplina->codigo }}</h6>
                                        <p class="text-sm text-gray-600">{{ $disciplina->nome }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('professor.chamada.fazer', ['turma' => $turma->id, 'disciplina' => $disciplina->id]) }}"
                                       class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md transition-colors text-sm font-medium">
                                        <x-heroicon-o-clipboard-document-check class="w-4 h-4 mr-1" />
                                        Chamada
                                    </a>
                                    
                                    <a href="{{ route('professor.chamadas.gerenciar', ['turma' => $turma->id, 'disciplina' => $disciplina->id]) }}"
                                       class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md transition-colors text-sm font-medium">
                                        <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-1" />
                                        Gerenciar
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Lista de Alunos -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-users class="w-5 h-5 mr-2 text-blue-600" />
                        Alunos Matriculados
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Lista completa dos alunos matriculados nesta turma</p>
                </div>
                
                @if($alunos->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aluno
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Matrícula
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data de Nascimento
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data de Matrícula
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($alunos as $aluno)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center">
                                                    <span class="text-white font-medium text-sm">
                                                        {{ strtoupper(substr($aluno->nome, 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $aluno->nome }}</div>
                                                    <div class="text-sm text-gray-500">{{ $aluno->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $aluno->numero_matricula }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $aluno->data_nascimento ? \Carbon\Carbon::parse($aluno->data_nascimento)->format('d/m/Y') : 'Não informado' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                                Ativo
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $aluno->created_at ? $aluno->created_at->format('d/m/Y') : 'Não informado' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <x-heroicon-o-users class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum aluno matriculado</h3>
                        <p class="text-gray-500">Esta turma ainda não possui alunos matriculados.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>