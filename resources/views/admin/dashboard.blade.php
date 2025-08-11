<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard Administrativo') }}
                </h2>
                <p class="text-gray-600 text-sm mt-1">Visão geral completa do sistema ClasseUp</p>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <x-heroicon-o-clock class="w-4 h-4" />
                <span>Última atualização: {{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Cards de Estatísticas Principais -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total de Alunos -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 overflow-hidden shadow-lg rounded-xl">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total de Alunos</p>
                                <p class="text-3xl font-bold">{{ $totalAlunos }}</p>
                                <p class="text-blue-100 text-xs mt-1">{{ $alunosAtivos }} com acesso ao sistema</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-full">
                                <x-heroicon-o-user class="w-8 h-8" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total de Professores -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 overflow-hidden shadow-lg rounded-xl">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Total de Professores</p>
                                <p class="text-3xl font-bold">{{ $totalProfessores }}</p>
                                <p class="text-green-100 text-xs mt-1">{{ $professoresAtivos }} ativos</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-full">
                                <x-heroicon-o-academic-cap class="w-8 h-8" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total de Turmas -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 overflow-hidden shadow-lg rounded-xl">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Total de Turmas</p>
                                <p class="text-3xl font-bold">{{ $totalTurmas }}</p>
                                <p class="text-purple-100 text-xs mt-1">{{ $turmasComAlunos }} com alunos</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-full">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total de Disciplinas -->
                <div class="bg-gradient-to-br from-yellow-500 to-orange-500 overflow-hidden shadow-lg rounded-xl">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm font-medium">Total de Disciplinas</p>
                                <p class="text-3xl font-bold">{{ $totalDisciplinas }}</p>
                                <p class="text-yellow-100 text-xs mt-1">Cadastradas no sistema</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-full">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas de Frequência -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-chart-bar class="w-5 h-5 mr-2 text-indigo-600" />
                        Estatísticas de Frequência Geral
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ $percentualFrequencia }}%</div>
                            <div class="text-sm text-gray-600 mt-1">Taxa de Frequência</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($totalPresencas) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Total de Presenças</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ number_format($totalFaltas) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Total de Faltas</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-600">{{ number_format($totalChamadas) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Total de Chamadas</div>
                        </div>
                    </div>
                    
                    <!-- Barra de Progresso da Frequência -->
                    <div class="mt-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Frequência Geral</span>
                            <span>{{ $percentualFrequencia }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500" 
                                 style="width: {{ $percentualFrequencia }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos e Análises -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Distribuição de Alunos por Turma -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <x-heroicon-o-user-group class="w-5 h-5 mr-2 text-blue-600" />
                            Top 5 Turmas por Número de Alunos
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($alunosPorTurma->count() > 0)
                            <div class="space-y-4">
                                @foreach($alunosPorTurma as $index => $turma)
                                    @php
                                        $percentage = $totalAlunos > 0 ? ($turma['total'] / $totalAlunos) * 100 : 0;
                                        $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-yellow-500', 'bg-red-500'];
                                        $color = $colors[$index % count($colors)];
                                    @endphp
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm font-medium text-gray-700">{{ $turma['nome'] }}</span>
                                                <span class="text-sm text-gray-500">{{ $turma['total'] }} alunos</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="{{ $color }} h-2 rounded-full transition-all duration-500" 
                                                     style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <x-heroicon-o-chart-bar class="w-12 h-12 mx-auto mb-2 text-gray-300" />
                                <p>Nenhum dado disponível</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Distribuição por Nível Educacional -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-green-600" />
                            Distribuição por Nível Educacional
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($alunosPorNivel->count() > 0)
                            <div class="space-y-4">
                                @foreach($alunosPorNivel as $index => $nivel)
                                    @php
                                        $percentage = $totalAlunos > 0 ? ($nivel['total'] / $totalAlunos) * 100 : 0;
                                        $colors = ['bg-emerald-500', 'bg-teal-500', 'bg-cyan-500', 'bg-sky-500', 'bg-indigo-500'];
                                        $color = $colors[$index % count($colors)];
                                    @endphp
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($nivel['nivel']) }}</span>
                                                <span class="text-sm text-gray-500">{{ $nivel['total'] }} alunos</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="{{ $color }} h-2 rounded-full transition-all duration-500" 
                                                     style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <x-heroicon-o-chart-bar class="w-12 h-12 mx-auto mb-2 text-gray-300" />
                                <p>Nenhum dado disponível</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Professores Mais Ativos e Alunos com Mais Faltas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Professores Mais Ativos -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <x-heroicon-o-star class="w-5 h-5 mr-2 text-yellow-600" />
                            Professores Mais Ativos
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Baseado no número de chamadas realizadas</p>
                    </div>
                    <div class="p-6">
                        @if($professoresMaisAtivos->count() > 0)
                            <div class="space-y-3">
                                @foreach($professoresMaisAtivos as $index => $professor)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                                {{ $index + 1 }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $professor['nome'] }}</span>
                                        </div>
                                        <span class="text-sm text-gray-600 bg-white px-2 py-1 rounded">
                                            {{ $professor['total_chamadas'] }} Chamadas
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <x-heroicon-o-user-group class="w-12 h-12 mx-auto mb-2 text-gray-300" />
                                <p>Nenhum dado disponível</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Alunos com Mais Faltas -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 mr-2 text-red-600" />
                            Alunos com Mais Faltas
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Requer atenção especial</p>
                    </div>
                    <div class="p-6">
                        @if($alunosComMaisFaltas->count() > 0)
                            <div class="space-y-3">
                                @foreach($alunosComMaisFaltas as $index => $aluno)
                                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                                {{ $index + 1 }}
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-900 block">{{ $aluno['nome'] }}</span>
                                                <span class="text-xs text-gray-500">Mat: {{ $aluno['matricula'] }}</span>
                                            </div>
                                        </div>
                                        <span class="text-sm text-red-600 bg-white px-2 py-1 rounded font-medium">
                                            {{ $aluno['total_faltas'] }} faltas
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <x-heroicon-o-check-circle class="w-12 h-12 mx-auto mb-2 text-green-300" />
                                <p>Nenhuma falta registrada</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas Melhoradas -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-bolt class="w-5 h-5 mr-2 text-purple-600" />
                        Ações Rápidas
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Acesso rápido às principais funcionalidades</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('admin.alunos.index') }}" class="group bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-xl p-4 transition-all duration-200 transform hover:scale-105">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-500 rounded-lg text-white group-hover:bg-blue-600 transition-colors">
                                    <x-heroicon-o-user class="w-6 h-6" />
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-gray-900">Gerenciar Alunos</p>
                                    <p class="text-xs text-gray-600">Cadastrar e editar alunos</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.professores.index') }}" class="group bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-xl p-4 transition-all duration-200 transform hover:scale-105">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-500 rounded-lg text-white group-hover:bg-green-600 transition-colors">
                                    <x-heroicon-o-academic-cap class="w-6 h-6" />
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-gray-900">Gerenciar Professores</p>
                                    <p class="text-xs text-gray-600">Cadastrar e editar professores</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.turmas.index') }}" class="group bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-xl p-4 transition-all duration-200 transform hover:scale-105">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-500 rounded-lg text-white group-hover:bg-purple-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-gray-900">Gerenciar Turmas</p>
                                    <p class="text-xs text-gray-600">Criar e organizar turmas</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.disciplinas.index') }}" class="group bg-gradient-to-br from-yellow-50 to-orange-100 hover:from-yellow-100 hover:to-orange-200 rounded-xl p-4 transition-all duration-200 transform hover:scale-105">
                            <div class="flex items-center">
                                <div class="p-3 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg text-white group-hover:from-yellow-600 group-hover:to-orange-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-gray-900">Gerenciar Disciplinas</p>
                                    <p class="text-xs text-gray-600">Cadastrar disciplinas</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Ações Adicionais -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ route('admin.chamadas.index') }}" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-indigo-600 mr-3" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Relatórios de Chamada</p>
                                    <p class="text-xs text-gray-600">Visualizar frequência</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.usuarios.alunos') }}" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                <x-heroicon-o-users class="w-5 h-5 text-blue-600 mr-3" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Usuários do Sistema</p>
                                    <p class="text-xs text-gray-600">Gerenciar acessos</p>
                                </div>
                            </a>
                            
                            <a href="#" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                <x-heroicon-o-document-text class="w-5 h-5 text-green-600 mr-3" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Relatórios Gerais</p>
                                    <p class="text-xs text-gray-600">Análises detalhadas</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atividades Recentes -->
            @if($atividadesRecentes->count() > 0)
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-clock class="w-5 h-5 mr-2 text-gray-600" />
                        Atividades Recentes
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Últimas chamadas realizadas no sistema</p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($atividadesRecentes as $atividade)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4">
                                        <x-heroicon-o-calendar class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($atividade['data'])->format('d/m/Y') }}</p>
                                        <p class="text-sm text-gray-600">{{ $atividade['total_chamadas'] }} Chamada(s) realizadas</p>
                                    </div>
                                </div>
                                <div class="flex space-x-4 text-sm">
                                    <span class="text-green-600 font-medium">{{ $atividade['presencas'] }} Presenças</span>
                                    <span class="text-red-600 font-medium">{{ $atividade['faltas'] }} Faltas</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Scripts para animações e interatividade -->
    <script>
        // Animação dos cards ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.grid > div');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });
        });
        
        // Auto-refresh da página a cada 5 minutos
        setTimeout(() => {
            location.reload();
        }, 300000); // 5 minutos
    </script>
</x-app-layout>