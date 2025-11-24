@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Editar Usuário: {{ $user->name }}</h1>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Nova Senha <small class="text-muted">(deixe em branco para manter a atual)</small></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="role" class="form-label">Papel no Sistema <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                            <option value="">Selecione um papel</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                            <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Gerente</option>
                            <option value="cashier" {{ old('role', $user->role) == 'cashier' ? 'selected' : '' }}>Caixa</option>
                        </select>
                        @if(auth()->id() == $user->id)
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <small class="text-muted">Você não pode alterar seu próprio papel no sistema.</small>
                        @endif
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input @error('active') is-invalid @enderror" type="checkbox" id="active" name="active" value="1" 
                                {{ old('active', $user->active) ? 'checked' : '' }}
                                {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                            <label class="form-check-label" for="active">
                                Usuário ativo
                            </label>
                            @if(auth()->id() == $user->id)
                                <input type="hidden" name="active" value="1">
                                <small class="text-muted">Você não pode desativar sua própria conta.</small>
                            @endif
                            @error('active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Informações do Usuário</h6>
                                <p class="card-text mb-1"><strong>Criado em:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                                <p class="card-text mb-1"><strong>Última atualização:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                                <p class="card-text mb-0"><strong>Último login:</strong> {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Informações importantes:
                            <ul class="mb-0">
                                <li>Administradores têm acesso completo ao sistema.</li>
                                <li>Gerentes podem gerenciar clientes, livros e visualizar relatórios.</li>
                                <li>Caixas podem criar e gerenciar faturas e clientes.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Atualizar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
