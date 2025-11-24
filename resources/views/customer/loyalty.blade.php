@extends('layouts.customer')

@section('content')
<!-- Hero Section -->
<div class="loyalty-hero bg-gradient-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-crown me-3"></i>
                    Programa de Fidelidade
                </h1>
                <p class="lead mb-4">Ganhe pontos a cada compra e desbloqueie benefícios exclusivos!</p>
                <div class="d-flex gap-3">
                    <div class="benefit-badge">
                        <i class="fas fa-coins me-2"></i>
                        Pontos por compra
                    </div>
                    <div class="benefit-badge">
                        <i class="fas fa-percentage me-2"></i>
                        Descontos exclusivos
                    </div>
                    <div class="benefit-badge">
                        <i class="fas fa-gift me-2"></i>
                        Recompensas especiais
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="current-level-card">
                    <div class="level-icon">
                        @if($points->level == 'bronze')
                            <i class="fas fa-medal text-warning"></i>
                        @elseif($points->level == 'silver')
                            <i class="fas fa-medal text-light"></i>
                        @else
                            <i class="fas fa-crown text-warning"></i>
                        @endif
                    </div>
                    <h4 class="mb-1">Nível {{ ucfirst($points->level) }}</h4>
                    <p class="mb-0 text-white-50">Seu nível atual</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">

    <!-- Points Overview -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="points-card card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <div class="points-circle mx-auto mb-4">
                        <div class="display-3 fw-bold text-primary">{{ $points->current_balance }}</div>
                        <p class="text-muted mb-0">Pontos Disponíveis</p>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="stat-item">
                                <div class="h5 fw-bold text-success">{{ $points->points }}</div>
                                <small class="text-muted">Ganhos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <div class="h5 fw-bold text-danger">{{ $points->points_spent }}</div>
                                <small class="text-muted">Utilizados</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Nível válido até {{ \Carbon\Carbon::parse($points->level_expires_at)->format('d/m/Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="levels-card card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-trophy me-2 text-warning"></i>
                        Níveis de Fidelidade
                    </h5>
                    
                    <!-- Bronze Level -->
                    <div class="level-item {{ $points->level == 'bronze' ? 'active' : '' }} mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="level-badge bronze me-3">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">Nível Bronze</h6>
                                <small class="text-muted">1 ponto para cada Kz 100</small>
                            </div>
                            @if($points->level == 'bronze')
                                <span class="badge bg-warning">Atual</span>
                            @endif
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <!-- Silver Level -->
                    <div class="level-item {{ $points->level == 'silver' ? 'active' : '' }} mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="level-badge silver me-3">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">Nível Prata</h6>
                                <small class="text-muted">1,5 pontos + 5% desconto</small>
                            </div>
                            @if($points->level == 'silver')
                                <span class="badge bg-secondary">Atual</span>
                            @endif
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-secondary" style="width: {{ $points->level == 'silver' || $points->level == 'gold' ? '100' : min(100, ($points->current_balance / 1000) * 100) }}%"></div>
                        </div>
                        <small class="text-muted">1.000+ pontos</small>
                    </div>
                    
                    <!-- Gold Level -->
                    <div class="level-item {{ $points->level == 'gold' ? 'active' : '' }}">
                        <div class="d-flex align-items-center mb-2">
                            <div class="level-badge gold me-3">
                                <i class="fas fa-crown"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">Nível Ouro</h6>
                                <small class="text-muted">2 pontos + 10% desconto</small>
                            </div>
                            @if($points->level == 'gold')
                                <span class="badge bg-warning">Atual</span>
                            @endif
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning" style="width: {{ $points->level == 'gold' ? '100' : min(100, ($points->current_balance / 5000) * 100) }}%"></div>
                        </div>
                        <small class="text-muted">5.000+ pontos</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions History -->
    <div class="row">
        <div class="col-12">
            <div class="transactions-card card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-history me-2 text-primary"></i>
                        Histórico de Transações
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($transactions->isEmpty())
                        <div class="empty-transactions text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-receipt text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted mb-3">Nenhuma transação encontrada</h5>
                            <p class="text-muted mb-4">Suas transações de pontos aparecerão aqui quando você fizer compras.</p>
                            <a href="{{ route('customer.catalog') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Começar a Comprar
                            </a>
                        </div>
                    @else
                        <div class="transactions-list">
                            @foreach($transactions as $transaction)
                                <div class="transaction-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="transaction-date">
                                                <div class="fw-semibold">{{ $transaction->created_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            @if($transaction->type == 'earn')
                                                <span class="transaction-badge earn">
                                                    <i class="fas fa-plus-circle me-1"></i>
                                                    Ganho
                                                </span>
                                            @elseif($transaction->type == 'redeem')
                                                <span class="transaction-badge redeem">
                                                    <i class="fas fa-minus-circle me-1"></i>
                                                    Resgate
                                                </span>
                                            @elseif($transaction->type == 'expire')
                                                <span class="transaction-badge expire">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Expirado
                                                </span>
                                            @elseif($transaction->type == 'bonus')
                                                <span class="transaction-badge bonus">
                                                    <i class="fas fa-gift me-1"></i>
                                                    Bônus
                                                </span>
                                            @else
                                                <span class="transaction-badge other">
                                                    <i class="fas fa-cog me-1"></i>
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-2">
                                            <div class="transaction-points">
                                                @if(in_array($transaction->type, ['earn', 'bonus', 'campaign']))
                                                    <span class="points-positive">+{{ $transaction->points }}</span>
                                                @else
                                                    <span class="points-negative">-{{ $transaction->points }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="transaction-balance">
                                                <span class="fw-semibold">{{ $transaction->balance_after }}</span>
                                                <small class="text-muted d-block">saldo após</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="transaction-description">
                                                {{ $transaction->description }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Modern Loyalty Page Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
}

.loyalty-hero {
    position: relative;
    overflow: hidden;
}

.loyalty-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="stars" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23stars)"/></svg>');
    opacity: 0.3;
}

.benefit-badge {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 12px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: white;
}

.current-level-card {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
}

.level-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.points-card {
    border-radius: 20px;
    transition: all 0.3s ease;
}

.points-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.1) !important;
}

.points-circle {
    background: linear-gradient(135deg, rgba(26, 26, 46, 0.05) 0%, rgba(233, 69, 96, 0.05) 100%);
    border-radius: 20px;
    padding: 2rem;
    border: 2px solid rgba(26, 26, 46, 0.1);
}

.stat-item {
    padding: 1rem 0;
}

.levels-card {
    border-radius: 20px;
}

.level-item {
    padding: 1rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.level-item.active {
    background: linear-gradient(135deg, rgba(26, 26, 46, 0.05) 0%, rgba(233, 69, 96, 0.05) 100%);
    border-color: var(--accent-color);
}

.level-badge {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.level-badge.bronze {
    background: linear-gradient(135deg, #cd7f32 0%, #b8860b 100%);
}

.level-badge.silver {
    background: linear-gradient(135deg, #c0c0c0 0%, #a8a8a8 100%);
}

.level-badge.gold {
    background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
}

.progress-sm {
    height: 6px;
    border-radius: 3px;
}

.transactions-card {
    border-radius: 20px;
}

.empty-transactions {
    padding: 3rem 2rem;
}

.transactions-list {
    padding: 0;
}

.transaction-item {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    transition: all 0.2s ease;
}

.transaction-item:hover {
    background: rgba(26, 26, 46, 0.02);
}

.transaction-item:last-child {
    border-bottom: none;
}

.transaction-badge {
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
}

.transaction-badge.earn {
    background: linear-gradient(135deg, rgba(15, 52, 96, 0.1) 0%, rgba(15, 52, 96, 0.05) 100%);
    color: #0f3460;
    border: 1px solid #0f3460;
}

.transaction-badge.redeem {
    background: linear-gradient(135deg, rgba(233, 69, 96, 0.1) 0%, rgba(233, 69, 96, 0.05) 100%);
    color: #e94560;
    border: 1px solid #e94560;
}

.transaction-badge.expire {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
    color: #dc3545;
    border: 1px solid #dc3545;
}

.transaction-badge.bonus {
    background: linear-gradient(135deg, rgba(26, 26, 46, 0.1) 0%, rgba(26, 26, 46, 0.05) 100%);
    color: #1a1a2e;
    border: 1px solid #1a1a2e;
}

.transaction-badge.other {
    background: linear-gradient(135deg, rgba(108, 117, 125, 0.1) 0%, rgba(108, 117, 125, 0.05) 100%);
    color: #6c757d;
    border: 1px solid #6c757d;
}

.points-positive {
    color: #0f3460;
    font-weight: 700;
    font-size: 1.1rem;
}

.points-negative {
    color: #e94560;
    font-weight: 700;
    font-size: 1.1rem;
}

.transaction-date {
    text-align: center;
}

.transaction-balance {
    text-align: center;
}

.transaction-points {
    text-align: center;
}

.btn {
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #16213e 0%, #0f3460 100%);
    box-shadow: 0 4px 12px rgba(26, 26, 46, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .benefit-badge {
        margin-bottom: 0.5rem;
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
    
    .current-level-card {
        margin-top: 2rem;
        padding: 1.5rem;
    }
    
    .transaction-item .row > div {
        text-align: center !important;
        margin-bottom: 0.5rem;
    }
    
    .transaction-item .row > div:last-child {
        margin-bottom: 0;
    }
}
</style>
@endpush
