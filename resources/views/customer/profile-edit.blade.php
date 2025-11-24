@extends('layouts.customer')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Perfil</h1>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Meus Dados</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <form action="{{ route('customer.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $customer->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="tax_id" class="form-label">NIF</label>
                        <input type="text" class="form-control @error('tax_id') is-invalid @enderror" id="tax_id" name="tax_id" value="{{ old('tax_id', $customer->tax_id) }}" readonly>
                        <small class="form-text text-muted">O NIF não pode ser alterado. Entre em contato com o suporte se precisar atualizar este dado.</small>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="address" class="form-label">Endereço</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $customer->address) }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="city" class="form-label">Cidade</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $customer->city) }}">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="state" class="form-label">Estado</label>
                        <select class="form-select @error('state') is-invalid @enderror" id="state" name="state">
                            <option value="">Selecione...</option>
                            <option value="AC" {{ old('state', $customer->state) == 'AC' ? 'selected' : '' }}>Acre</option>
                            <option value="AL" {{ old('state', $customer->state) == 'AL' ? 'selected' : '' }}>Alagoas</option>
                            <option value="AP" {{ old('state', $customer->state) == 'AP' ? 'selected' : '' }}>Amapá</option>
                            <option value="AM" {{ old('state', $customer->state) == 'AM' ? 'selected' : '' }}>Amazonas</option>
                            <option value="BA" {{ old('state', $customer->state) == 'BA' ? 'selected' : '' }}>Bahia</option>
                            <option value="CE" {{ old('state', $customer->state) == 'CE' ? 'selected' : '' }}>Ceará</option>
                            <option value="DF" {{ old('state', $customer->state) == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                            <option value="ES" {{ old('state', $customer->state) == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                            <option value="GO" {{ old('state', $customer->state) == 'GO' ? 'selected' : '' }}>Goiás</option>
                            <option value="MA" {{ old('state', $customer->state) == 'MA' ? 'selected' : '' }}>Maranhão</option>
                            <option value="MT" {{ old('state', $customer->state) == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                            <option value="MS" {{ old('state', $customer->state) == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                            <option value="MG" {{ old('state', $customer->state) == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                            <option value="PA" {{ old('state', $customer->state) == 'PA' ? 'selected' : '' }}>Pará</option>
                            <option value="PB" {{ old('state', $customer->state) == 'PB' ? 'selected' : '' }}>Paraíba</option>
                            <option value="PR" {{ old('state', $customer->state) == 'PR' ? 'selected' : '' }}>Paraná</option>
                            <option value="PE" {{ old('state', $customer->state) == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                            <option value="PI" {{ old('state', $customer->state) == 'PI' ? 'selected' : '' }}>Piauí</option>
                            <option value="RJ" {{ old('state', $customer->state) == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                            <option value="RN" {{ old('state', $customer->state) == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                            <option value="RS" {{ old('state', $customer->state) == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                            <option value="RO" {{ old('state', $customer->state) == 'RO' ? 'selected' : '' }}>Rondônia</option>
                            <option value="RR" {{ old('state', $customer->state) == 'RR' ? 'selected' : '' }}>Roraima</option>
                            <option value="SC" {{ old('state', $customer->state) == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                            <option value="SP" {{ old('state', $customer->state) == 'SP' ? 'selected' : '' }}>São Paulo</option>
                            <option value="SE" {{ old('state', $customer->state) == 'SE' ? 'selected' : '' }}>Sergipe</option>
                            <option value="TO" {{ old('state', $customer->state) == 'TO' ? 'selected' : '' }}>Tocantins</option>
                        </select>
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="postal_code" class="form-label">Código Postal</label>
                        <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}">
                        @error('postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="birth_date" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $customer->birth_date ? $customer->birth_date->format('Y-m-d') : '') }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <hr class="my-4">
                
                <h5 class="mb-3">Alterar Senha</h5>
                <p class="text-muted mb-3">Preencha apenas se desejar alterar sua senha atual</p>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="current_password" class="form-label">Senha Atual</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="password" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
