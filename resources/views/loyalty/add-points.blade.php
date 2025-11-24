@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Adicionar Pontos - {{ $customer->name }}</span>
                    <div>
                        <a href="{{ route('loyalty.dashboard', $customer) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar ao Painel de Fidelidade
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Informações do Cliente</h5>
                                    <p><strong>Nome:</strong> {{ $customer->name }}</p>
                                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                                    <p><strong>Telefone:</strong> {{ $customer->phone }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Status de Fidelidade</h5>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3 class="mb-0">{{ $loyaltyPoints->current_balance }} pontos</h3>
                                        <span class="badge bg-{{ $loyaltyPoints->level == 'platinum' ? 'primary' : ($loyaltyPoints->level == 'gold' ? 'warning' : ($loyaltyPoints->level == 'silver' ? 'secondary' : 'dark')) }} p-2">
                                            {{ ucfirst($loyaltyPoints->level) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">Adicionar Pontos</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('loyalty.add-points', $customer) }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="points" class="form-label">Quantidade de Pontos</label>
                                    <input type="number" class="form-control @error('points') is-invalid @enderror" id="points" name="points" value="{{ old('points') }}" required min="1">
                                    @error('points')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Descrição</label>
                                    <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" required>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="form-text">
                                        Descreva o motivo da adição de pontos (ex: "Ajuste manual", "Bônus de aniversário", etc.)
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('loyalty.dashboard', $customer) }}" class="btn btn-outline-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-plus-circle"></i> Adicionar Pontos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
