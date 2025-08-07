@extends('layouts.app')

@section('title', 'Gerenciar Usuários - Professores')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>
                        Gerenciar Usuários - Professores
                    </h3>
                    <a href="{{ route('admin.usuarios.alunos') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-graduate me-1"></i>
                        Ver Alunos
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Email</th>
                                    <th>Status do Usuário</th>
                                    <th>Email do Sistema</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($professores as $professor)
                                    <tr>
                                        <td>{{ $professor->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                                                {{ $professor->nome }}
                                            </div>
                                        </td>
                                        <td>{{ $professor->cpf }}</td>
                                        <td>{{ $professor->email }}</td>
                                        <td>
                                            @if($professor->user)
                                                @if($professor->user->email_verified_at)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>
                                                        Ativo
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-pause me-1"></i>
                                                        Inativo
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times me-1"></i>
                                                    Sem Usuário
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($professor->user)
                                                <code>{{ $professor->user->email }}</code>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if(!$professor->user)
                                                    <form action="{{ route('admin.usuarios.vincular-professor', $professor->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Deseja criar um usuário para este professor?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-user-plus me-1"></i>
                                                            Criar Usuário
                                                        </button>
                                                    </form>
                                                @else
                                                    @if($professor->user->email_verified_at)
                                                        <form action="{{ route('admin.usuarios.desativar', $professor->user->id) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Deseja desativar este usuário?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-user-slash me-1"></i>
                                                                Desativar
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('admin.usuarios.ativar', $professor->user->id) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Deseja ativar este usuário?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-user-check me-1"></i>
                                                                Ativar
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <p class="mb-0">Nenhum professor encontrado.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Total de professores: {{ $professores->count() }}
                            </small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                <i class="fas fa-key me-1"></i>
                                Senha padrão: CPF (apenas números)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}

.btn-group .btn {
    border-radius: 0.375rem;
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

code {
    background-color: #f8f9fa;
    color: #6c757d;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}
</style>
@endpush