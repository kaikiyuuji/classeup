<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Controle de Faltas') }}
        </h2>
    </x-slot>
<div class="py-8">
    <div class="max-w-7xl mx-auto px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-8 py-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Controle de Faltas</h1>
                        <p class="text-gray-600 mt-2">Gerencie a presença dos alunos e visualize relatórios</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.faltas.relatorio-aluno') }}" 
                           class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors shadow-sm font-medium">
                            <x-icons.chart-bar class="w-5 h-5 mr-2" />Ver Relatórios
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <x-icons.clipboard class="w-6 h-6 mr-3 text-blue-600" />
                        Selecione uma turma para fazer a chamada
                    </h2>
                    
                    @if($turmasComVinculo->isEmpty())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                            <div class="flex items-center">
                                <x-icons.exclamation class="w-6 h-6 text-yellow-500 mr-4" />
                                <div>
                                    <p class="text-yellow-800 font-medium mb-1">Nenhuma turma disponível</p>
                                    <p class="text-yellow-700 text-sm">
                                        Nenhuma turma com professor e disciplina vinculados encontrada.
                                        <a href="{{ route('admin.turmas.index') }}" class="underline hover:no-underline font-medium">
                                            Clique aqui para gerenciar turmas.
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="grid gap-6">
                            @foreach($turmasComVinculo as $turmaNome => $vinculos)
                                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                                    <h3 class="font-bold text-lg text-gray-900 mb-4 flex items-center">
                                        <x-icons.book-open class="w-5 h-5 mr-2 text-blue-600" />
                                        {{ $turmaNome }}
                                        <span class="text-sm font-normal text-gray-500 ml-2">
                                            ({{ $vinculos->first()->serie }})
                                        </span>
                                    </h3>
                                    <div class="grid gap-3">
                                        @foreach($vinculos as $vinculo)
                                            <div class="flex justify-between items-center bg-white rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-colors">
                                                <div class="flex items-center">
                                                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                                                        <x-icons.book-open class="w-6 h-6 text-blue-600" />
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-gray-900">{{ $vinculo->disciplina_nome }}</p>
                                                        <p class="text-sm text-gray-600">Prof. {{ $vinculo->professor_nome }}</p>
                                                    </div>
                                                </div>
                                                
                                                <a href="{{ route('admin.faltas.chamada', ['turma' => $vinculo->turma_id, 'disciplina' => $vinculo->disciplina_id]) }}?professor_id={{ $vinculo->professor_id }}" 
                                                   class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm">
                                                    <x-icons.clipboard class="w-4 h-4 mr-2" />
                                                    Fazer Chamada
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>