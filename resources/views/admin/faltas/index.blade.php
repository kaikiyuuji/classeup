<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Controle de Faltas') }}
        </h2>
    </x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Controle de Faltas</h1>
                <a href="{{ route('faltas.relatorio-aluno') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <x-icons.chart-bar class="w-4 h-4 mr-2 inline" />Relatórios
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Selecione uma turma para fazer a chamada:</h2>
                
                @if($turmasComVinculo->isEmpty())
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-icons.exclamation class="w-5 h-5 text-yellow-500 mr-3" />
                            <p class="text-yellow-700">
                                Nenhuma turma com professor e disciplina vinculados encontrada.
                                <a href="{{ route('professores.index') }}" class="underline hover:text-yellow-800">
                                    Configure os vínculos aqui
                                </a>
                            </p>
                        </div>
                    </div>
                @else
                    <div class="grid gap-4">
                        @foreach($turmasComVinculo as $turmaNome => $vinculos)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-3">
                                    <x-icons.users class="w-5 h-5 mr-2 text-blue-500 inline" />
                                    {{ $turmaNome }}
                                    <span class="text-sm font-normal text-gray-500">
                                        ({{ $vinculos->first()->serie }})
                                    </span>
                                </h3>
                                
                                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($vinculos as $vinculo)
                                        <div class="bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition-colors">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <p class="font-medium text-gray-800">{{ $vinculo->disciplina_nome }}</p>
                                                    <p class="text-sm text-gray-600">Prof. {{ $vinculo->professor_nome }}</p>
                                                </div>
                                            </div>
                                            
                                            <a href="{{ route('faltas.chamada', ['turma' => $vinculo->turma_id, 'disciplina' => $vinculo->disciplina_id]) }}?professor_id={{ $vinculo->professor_id }}" 
                                               class="inline-flex items-center text-sm bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded transition-colors">
                                                <x-icons.clipboard class="w-4 h-4 mr-1" />
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