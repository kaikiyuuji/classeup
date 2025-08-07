<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meu Painel') }}
        </h2>
    </x-slot>

    @section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Meu Painel</h1>
            <p class="text-gray-600 mt-2">Bem-vindo, {{ $aluno->nome }}!</p>
        </div>

        <!-- Informações do Aluno -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Minhas Informações</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Matrícula</p>
                        <p class="text-sm text-gray-900">{{ $aluno->numero_matricula ?? 'Não informado' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Turma</p>
                        <p class="text-sm text-gray-900">{{ $turma->nome ?? 'Não vinculado' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção de Notas e Faltas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Minhas Notas Recentes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Notas Recentes</h3>
                    </div>
                </div>
                <div class="p-6">
                    @if($avaliacoes->count() > 0)
                        <div class="space-y-4">
                            @foreach($avaliacoes->take(5) as $avaliacao)
                                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $avaliacao->disciplina->nome }}</p>
                                        <p class="text-xs text-gray-500">{{ $avaliacao->tipo_avaliacao }}</p>
                                        <p class="text-xs text-gray-400">{{ $avaliacao->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($avaliacao->nota >= 7) bg-green-100 text-green-800
                                            @elseif($avaliacao->nota >= 5) bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ number_format($avaliacao->nota, 1) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma nota lançada</h3>
                            <p class="mt-1 text-sm text-gray-500">Suas notas aparecerão aqui quando forem lançadas pelos professores.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Minhas Faltas Recentes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Faltas Recentes</h3>
                </div>
                <div class="p-6">
                    @if($faltas->count() > 0)
                        <div class="space-y-4">
                            @foreach($faltas->take(5) as $falta)
                                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $falta->disciplina->nome }}</p>
                                        <p class="text-xs text-gray-500">Professor: {{ $falta->professor->nome }}</p>
                                        <p class="text-xs text-gray-400">{{ $falta->data_falta->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($falta->justificada) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            @if($falta->justificada)
                                                Justificada
                                            @else
                                                Não Justificada
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma falta registrada</h3>
                            <p class="mt-1 text-sm text-gray-500">Continue assim! Suas faltas aparecerão aqui se registradas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-app-layout>