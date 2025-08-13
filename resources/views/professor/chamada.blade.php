@extends('layouts.app')

@section('title', 'Lançar Chamada')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho da Turma -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Chamada - {{ $turma->nome }} | {{ $disciplina->nome }}
                    </h4>
                    <small>Data: {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Turma:</strong> {{ $turma->nome }}
                        </div>
                        <div class="col-md-3">
                            <strong>Disciplina:</strong> {{ $disciplina->nome }}
                        </div>
                        <div class="col-md-3">
                            <strong>Professor:</strong> {{ $professor->nome }}
                        </div>
                        <div class="col-md-3">
                            <strong>Total de Alunos:</strong> {{ $alunos->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning') && session('mostrar_confirmacao'))
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <div class="mt-3">
                <form method="POST" action="{{ route('professor.chamada.salvar') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="turma_id" value="{{ $turma->id }}">
                    <input type="hidden" name="disciplina_id" value="{{ $disciplina->id }}">
                    <input type="hidden" name="data_chamada" value="{{ $data }}">
                    <input type="hidden" name="confirmar_reenvio" value="1">
                    @foreach(old('presencas', []) as $matricula)
                        <input type="hidden" name="presencas[]" value="{{ $matricula }}">
                    @endforeach
                    <button type="submit" class="btn btn-warning me-2">
                        <i class="fas fa-check me-1"></i>Sim, Substituir
                    </button>
                </form>
                <a href="{{ route('dashboard.professor') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Cancelar
                </a>
            </div>
        </div>
    @endif

    <!-- Formulário de Chamada -->
    <form method="POST" action="{{ route('professor.chamada.salvar') }}" id="formChamada">
        @csrf
        <input type="hidden" name="turma_id" value="{{ $turma->id }}">
        <input type="hidden" name="disciplina_id" value="{{ $disciplina->id }}">
        <input type="hidden" name="data_chamada" value="{{ $data }}">

        <!-- Controles de Chamada -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Controles de Chamada
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success" onclick="marcarTodosPresentes()">
                                        <i class="fas fa-check-double me-1"></i>
                                        Marcar Todos Presentes
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="marcarTodosFaltas()">
                                        <i class="fas fa-times-circle me-1"></i>
                                        Marcar Todos Faltas
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <span class="badge bg-info fs-6" id="contadorPresentes">
                                    Presentes: <span id="numeroPresentes">{{ count($presencasExistentes) }}</span>
                                </span>
                                <span class="badge bg-warning fs-6 ms-2" id="contadorFaltas">
                                    Faltas: <span id="numeroFaltas">{{ $alunos->count() - count($presencasExistentes) }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Alunos -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Lista de Alunos ({{ $alunos->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($alunos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="60">#</th>
                                            <th>Matrícula</th>
                                            <th>Nome do Aluno</th>
                                            <th width="120" class="text-center">Presença</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($alunos as $index => $aluno)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $aluno->numero_matricula }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($aluno->foto)
                                                            <img src="{{ asset('storage/' . $aluno->foto) }}" 
                                                                 alt="Foto de {{ $aluno->nome }}" 
                                                                 class="rounded-circle me-2" 
                                                                 width="32" height="32">
                                                        @else
                                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                                 style="width: 32px; height: 32px;">
                                                                <i class="fas fa-user text-white"></i>
                                                            </div>
                                                        @endif
                                                        <span>{{ $aluno->nome }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input presenca-checkbox" 
                                                               type="checkbox" 
                                                               name="presencas[]" 
                                                               value="{{ $aluno->numero_matricula }}"
                                                               id="presenca_{{ $aluno->numero_matricula }}"
                                                               {{ in_array($aluno->numero_matricula, $presencasExistentes) ? 'checked' : '' }}
                                                               onchange="atualizarContadores()">
                                                        <label class="form-check-label" for="presenca_{{ $aluno->numero_matricula }}">
                                                            <span class="badge bg-success presente-label" style="display: {{ in_array($aluno->numero_matricula, $presencasExistentes) ? 'inline' : 'none' }};">Presente</span>
                                                            <span class="badge bg-danger falta-label" style="display: {{ in_array($aluno->numero_matricula, $presencasExistentes) ? 'none' : 'inline' }};">Falta</span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum aluno encontrado nesta turma</h5>
                                <p class="text-muted">Verifique se existem alunos matriculados na turma {{ $turma->nome }}.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        @if($alunos->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="submit" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>
                                Salvar Chamada
                            </button>
                            <a href="{{ route('dashboard.professor') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>
                                Voltar ao Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </form>
</div>

<script>
function marcarTodosPresentes() {
    document.querySelectorAll('.presenca-checkbox').forEach(checkbox => {
        checkbox.checked = true;
        const row = checkbox.closest('tr');
        row.querySelector('.presente-label').style.display = 'inline';
        row.querySelector('.falta-label').style.display = 'none';
    });
    atualizarContadores();
}

function marcarTodosFaltas() {
    document.querySelectorAll('.presenca-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        const row = checkbox.closest('tr');
        row.querySelector('.presente-label').style.display = 'none';
        row.querySelector('.falta-label').style.display = 'inline';
    });
    atualizarContadores();
}

function atualizarContadores() {
    const checkboxes = document.querySelectorAll('.presenca-checkbox');
    const totalAlunos = checkboxes.length;
    let presentes = 0;
    
    checkboxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const presenteLabel = row.querySelector('.presente-label');
        const faltaLabel = row.querySelector('.falta-label');
        
        if (checkbox.checked) {
            presentes++;
            presenteLabel.style.display = 'inline';
            faltaLabel.style.display = 'none';
        } else {
            presenteLabel.style.display = 'none';
            faltaLabel.style.display = 'inline';
        }
    });
    
    const faltas = totalAlunos - presentes;
    
    document.getElementById('numeroPresentes').textContent = presentes;
    document.getElementById('numeroFaltas').textContent = faltas;
}

// Inicializar contadores ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    atualizarContadores();
});
</script>
@endsection