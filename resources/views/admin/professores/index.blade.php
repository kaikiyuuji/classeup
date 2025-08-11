<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">
                    {{ __('Gerenciar Professores') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Visualize e gerencie todos os professores cadastrados</p>
            </div>
            <a href="{{ route('admin.professores.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                Novo Professor
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-8">
            

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <!-- Filtros -->
                <x-admin.filtros 
                    :action="route('admin.professores.index')"
                    placeholder="Buscar por nome, email, especialidade ou formação..."
                    :show-search="true"
                    :show-status="true"
                    :show-disciplina="true"
                    :show-especialidade="true"
                    :disciplinas="$disciplinas"
                    :especialidades="$especialidades"
                />

                @if($professores->count() > 0)
                    <!-- Table View -->
                    <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <x-admin.sortable-header 
                                            field="nome" 
                                            :current-sort="$sortField ?? ''" 
                                            :current-direction="$sortDirection ?? 'asc'" 
                                            label="Professor" 
                                        />
                                        <x-admin.sortable-header 
                                            field="email" 
                                            :current-sort="$sortField ?? ''" 
                                            :current-direction="$sortDirection ?? 'asc'" 
                                            label="Contato" 
                                        />
                                        <x-admin.sortable-header 
                                            field="especialidade" 
                                            :current-sort="$sortField ?? ''" 
                                            :current-direction="$sortDirection ?? 'asc'" 
                                            label="Especialidade" 
                                        />
                                        <x-admin.sortable-header 
                                            field="ativo" 
                                            :current-sort="$sortField ?? ''" 
                                            :current-direction="$sortDirection ?? 'asc'" 
                                            label="Status" 
                                        />
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($professores as $professor)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($professor->foto_perfil_url)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $professor->foto_perfil_url }}" alt="{{ $professor->nome }}">
                                                        @else
                                                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                                <span class="text-purple-600 font-medium text-sm">{{ substr($professor->nome, 0, 2) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $professor->nome }}</div>
                                                        <div class="text-sm text-gray-500">ID: #{{ $professor->id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $professor->email }}</div>
                                                @if($professor->telefone)
                                                    <div class="text-sm text-gray-500" data-format="phone">{{ $professor->telefone }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $professor->especialidade }}</div>
                                                <div class="text-sm text-gray-500">{{ Str::limit($professor->formacao, 50) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $professor->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    <x-heroicon-o-stop class="w-1.5 h-1.5 mr-1.5" />
                                                    {{ $professor->ativo ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <a href="{{ route('admin.professores.show', $professor) }}" 
                                                   class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <x-heroicon-o-eye class="w-3 h-3 mr-1" />
                                                        Ver
                                                    </a>
                                                    <a href="{{ route('admin.professores.edit', $professor) }}" 
                                                       class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <x-heroicon-o-pencil class="w-3 h-3 mr-1" />
                                                        Editar
                                                    </a>
                                                    <form action="{{ route('admin.professores.destroy', $professor) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('Tem certeza que deseja excluir este professor?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                            <x-heroicon-o-trash class="w-3 h-3 mr-1" />
                                                            Excluir
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="px-8 py-3 border-t border-gray-200">
                        {{ $professores->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <x-heroicon-o-academic-cap class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum professor cadastrado</h3>
                        <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro professor.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.professores.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                                Cadastrar Primeiro Professor
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>