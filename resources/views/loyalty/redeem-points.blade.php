@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Resgatar Pontos - {{ $customer->name }}</span>
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
                        <div class="card-header">Resgatar Pontos</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('loyalty.redeem-points', $customer) }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="points" class="form-label">Quantidade de Pontos a Resgatar</label>
                                    <input type="number" class="form-control @error('points') is-invalid @enderror" id="points" name="points" value="{{ old('points') }}" required min="1" max="{{ $loyaltyPoints->current_balance }}">
                                    @error('points')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="form-text">
                                        Máximo disponível: {{ $loyaltyPoints->current_balance }} pontos
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Descrição do Resgate</label>
                                    <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" required>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="form-text">
                                        Descreva o motivo do resgate (ex: "Desconto em compra", "Brinde", etc.)
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header">Opções de Resgate</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-primary text-white">Desconto em Compra</div>
                                                    <div class="card-body">
                                                        <p>100 pontos = R$ 10 de desconto</p>
                                                        <button type="button" class="btn btn-sm btn-outline-primary redemption-option" data-points="100" data-description="Desconto de R$ 10 em compra">
                                                            Resgatar 100 pontos
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-success text-white">Frete Grátis</div>
                                                    <div class="card-body">
                                                        <p>50 pontos = Frete grátis em uma compra</p>
                                                        <button type="button" class="btn btn-sm btn-outline-success redemption-option" data-points="50" data-description="Frete grátis em uma compra">
                                                            Resgatar 50 pontos
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-info text-white">Livro Promocional</div>
                                                    <div class="card-body">
                                                        <p>200 pontos = Livro promocional gratuito</p>
                                                        <button type="button" class="btn btn-sm btn-outline-info redemption-option" data-points="200" data-description="Livro promocional gratuito">
                                                            Resgatar 200 pontos
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-warning">Cupom de Desconto</div>
                                                    <div class="card-body">
                                                        <p>300 pontos = Cupom de 15% de desconto</p>
                                                        <button type="button" class="btn btn-sm btn-outline-warning redemption-option" data-points="300" data-description="Cupom de 15% de desconto">
                                                            Resgatar 300 pontos
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('loyalty.dashboard', $customer) }}" class="btn btn-outline-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-gift"></i> Resgatar Pontos
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const redemptionOptions = document.querySelectorAll('.redemption-option');
        const pointsInput = document.getElementById('points');
        const descriptionInput = document.getElementById('description');
        
        redemptionOptions.forEach(option => {
            option.addEventListener('click', function() {
                const points = this.getAttribute('data-points');
                const description = this.getAttribute('data-description');
                
                pointsInput.value = points;
                descriptionInput.value = description;
            });
        });
    });
</script>
@endpush
@endsection
