@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Logo/Brand Section -->
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-book-open text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h1 class="h3 fw-bold text-dark mb-2">Criar Conta</h1>
                    <p class="text-muted mb-0">Junte-se ao CRM Livraria</p>
                </div>

                <!-- Register Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Welcome Message -->
                        <div class="alert alert-light border border-success bg-success bg-opacity-10 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-gift text-success me-2"></i>
                                <div>
                                    <strong class="text-success">Bem-vindo!</strong>
                                    <span class="text-success ms-1">Crie sua conta gratuita e comece a explorar nosso catálogo de livros.</span>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                            @csrf

                            <!-- Name Field -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium text-dark">
                                    Nome Completo
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input 
                                        id="name" 
                                        type="text" 
                                        class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                        name="name" 
                                        value="{{ old('name') }}" 
                                        required 
                                        autocomplete="name" 
                                        autofocus
                                        placeholder="Digite seu nome completo"
                                    >
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email Field -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium text-dark">
                                    Email
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input 
                                        id="email" 
                                        type="email" 
                                        class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required 
                                        autocomplete="email"
                                        placeholder="seu@email.com"
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium text-dark">
                                    Senha
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input 
                                        id="password" 
                                        type="password" 
                                        class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                        name="password" 
                                        required 
                                        autocomplete="new-password"
                                        placeholder="Mínimo 8 caracteres"
                                    >
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Use pelo menos 8 caracteres com letras e números
                                    </small>
                                </div>
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="mb-4">
                                <label for="password-confirm" class="form-label fw-medium text-dark">
                                    Confirmar Senha
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input 
                                        id="password-confirm" 
                                        type="password" 
                                        class="form-control border-start-0" 
                                        name="password_confirmation" 
                                        required 
                                        autocomplete="new-password"
                                        placeholder="Digite a senha novamente"
                                    >
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label text-muted" for="terms">
                                        Eu concordo com os 
                                        <a href="#" class="text-primary text-decoration-none">Termos de Uso</a> 
                                        e 
                                        <a href="#" class="text-primary text-decoration-none">Política de Privacidade</a>
                                        <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Register Button -->
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                <i class="fas fa-user-plus me-2"></i>
                                Criar Conta
                            </button>
                        </form>

                        <!-- Login Link -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="text-muted mb-2">Já tem uma conta?</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Fazer Login
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-muted small mb-0">
                        © {{ date('Y') }} CRM Livraria. Sistema de gestão para livrarias.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Custom styles for modern register */
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.input-group-text {
    border-color: #ced4da;
}

.form-control.border-start-0 {
    border-left: 0;
}

.form-control.border-start-0:focus {
    border-left: 0;
}

.input-group:focus-within .input-group-text {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.05);
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.card {
    border-radius: 12px;
    transition: all 0.3s ease;
}

.alert {
    border-radius: 8px;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

@media (max-width: 576px) {
    .container {
        padding: 1rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
}
</style>
@endpush
@endsection
