@extends('layouts.customer')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="fas fa-user-circle me-3"></i>
                    Olá, {{ explode(' ', $customer->name)[0] }}!
                </h1>
                <p class="lead mb-4">Bem-vindo ao seu painel pessoal. Gerencie seus pedidos, pontos de fidelidade e perfil.</p>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-shopping-bag me-1"></i>
                        {{ $invoices->count() }} pedidos realizados
                    </span>
                    <span class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-star me-1"></i>
                        {{ $loyaltyPoints->current_balance }} pontos
                    </span>
                    <span class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-crown me-1"></i>
                        Nível {{ ucfirst($loyaltyPoints->level) }}
                    </span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="bg-white bg-opacity-10 rounded-4 p-4">
                    <div class="text-center">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                        </div>
                        <h5 class="mb-1">{{ $customer->name }}</h5>
                        <p class="mb-0 text-white-50">{{ $customer->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">

    <!-- Quick Actions -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3">
                            <i class="fas fa-user fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold">Meu Perfil</h5>
                            <p class="text-muted small mb-0">Gerencie suas informações</p>
                        </div>
                    </div>
                    
                    <div class="profile-info">
                        <div class="info-item">
                            <i class="fas fa-envelope text-muted me-2"></i>
                            <span class="small">{{ $customer->email }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone text-muted me-2"></i>
                            <span class="small">{{ $customer->phone }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            <span class="small">{{ $customer->city }}, {{ $customer->state }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('customer.profile.edit') }}" class="btn btn-primary w-100">
                            <i class="fas fa-edit me-2"></i>
                            Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body p-4 text-center">
                    <div class="icon-circle bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                        <i class="fas fa-star fa-lg"></i>
                    </div>
                    
                    <h5 class="card-title fw-bold mb-2">Pontos de Fidelidade</h5>
                    
                    <div class="loyalty-display">
                        <div class="points-circle mx-auto mb-3">
                            <div class="display-4 fw-bold text-primary">{{ $loyaltyPoints->current_balance }}</div>
                            <p class="text-muted small mb-0">pontos disponíveis</p>
                        </div>
                        
                        <div class="level-badge">
                            <span class="badge bg-gradient-warning px-3 py-2">
                                <i class="fas fa-crown me-1"></i>
                                Nível {{ ucfirst($loyaltyPoints->level) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('customer.loyalty') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-gift me-2"></i>
                            Ver Programa
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success bg-opacity-10 text-success me-3">
                            <i class="fas fa-shopping-bag fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold">Meus Pedidos</h5>
                            <p class="text-muted small mb-0">Acompanhe seus pedidos</p>
                        </div>
                    </div>
                    
                    <div class="orders-stats">
                        <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-shopping-cart text-primary me-2"></i>
                                <span class="small">Total de Pedidos</span>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $invoices->count() }}</span>
                        </div>
                        
                        <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <span class="small">Pendentes</span>
                            </div>
                            <span class="badge bg-warning rounded-pill">{{ $invoices->where('status', 'pending')->count() }}</span>
                        </div>
                        
                        <div class="stat-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="small">Concluídos</span>
                            </div>
                            <span class="badge bg-success rounded-pill">{{ $invoices->where('status', 'paid')->count() }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('customer.orders') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-list me-2"></i>
                            Ver Todos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="fas fa-history text-primary me-2"></i>
                                Pedidos Recentes
                            </h5>
                            <p class="text-muted small mb-0">Acompanhe o status dos seus últimos pedidos</p>
                        </div>
                        <a href="{{ route('customer.orders') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i>
                            Ver Todos
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($invoices->isEmpty())
                        <div class="empty-state text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-shopping-cart text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted mb-3">Nenhum pedido realizado</h5>
                            <p class="text-muted mb-4">Você ainda não realizou nenhum pedido. Explore nosso catálogo e encontre produtos incríveis!</p>
                            <a href="{{ route('customer.catalog') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Começar a Comprar
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-semibold">Pedido</th>
                                        <th class="border-0 fw-semibold">Data</th>
                                        <th class="border-0 fw-semibold">Total</th>
                                        <th class="border-0 fw-semibold">Status</th>
                                        <th class="border-0 fw-semibold text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices->take(5) as $invoice)
                                        <tr class="order-row">
                                            <td class="border-0 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="order-icon me-3">
                                                        <i class="fas fa-receipt text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">#{{ $invoice->id }}</div>
                                                        <small class="text-muted">{{ $invoice->invoice_number ?? 'INV-' . $invoice->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0 py-3">
                                                <div class="fw-medium">{{ $invoice->invoice_date->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $invoice->invoice_date->format('H:i') }}</small>
                                            </td>
                                            <td class="border-0 py-3">
                                                <div class="fw-semibold text-success">Kz {{ number_format($invoice->total, 2, ',', '.') }}</div>
                                            </td>
                                            <td class="border-0 py-3">
                                                @if($invoice->status == 'paid')
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        Pago
                                                    </span>
                                                @elseif($invoice->status == 'pending')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2">
                                                        <i class="fas fa-clock me-1"></i>
                                                        Pendente
                                                    </span>
                                                @elseif($invoice->status == 'cancelled')
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2">
                                                        <i class="fas fa-times-circle me-1"></i>
                                                        Cancelado
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2">
                                                        {{ ucfirst($invoice->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="border-0 py-3 text-end">
                                                <a href="{{ route('customer.order.details', $invoice) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>
                                                    Detalhes
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
/* Modern Dashboard Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
}

.dashboard-card {
    transition: all 0.3s ease;
    border-radius: 16px;
    overflow: hidden;
}

.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.dashboard-card:hover .icon-circle {
    transform: scale(1.1);
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    padding: 8px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.points-circle {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(255, 193, 7, 0.1) 100%);
    border-radius: 20px;
    padding: 20px;
    margin: 20px 0;
}

.level-badge .badge {
    font-size: 0.875rem;
    border-radius: 12px;
}

.stat-item {
    padding: 8px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-item:hover {
    background-color: rgba(13, 110, 253, 0.02);
    border-radius: 8px;
    padding-left: 8px;
    padding-right: 8px;
}

.order-row {
    transition: all 0.2s ease;
}

.order-row:hover {
    background-color: rgba(13, 110, 253, 0.02);
}

.order-icon {
    width: 32px;
    height: 32px;
    background: rgba(13, 110, 253, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-state {
    padding: 60px 20px;
}

.btn {
    border-radius: 12px;
    transition: all 0.2s ease;
    font-weight: 500;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
}

.card {
    border-radius: 16px;
}

.badge {
    border-radius: 8px;
    font-weight: 500;
}

.table th {
    font-size: 0.875rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Avatar improvements */
.avatar-lg {
    transition: transform 0.3s ease;
}

.dashboard-card:hover .avatar-lg {
    transform: scale(1.05);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .dashboard-card:hover {
        transform: none;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
    
    .icon-circle {
        width: 40px;
        height: 40px;
    }
    
    .empty-state {
        padding: 40px 20px;
    }
}

/* Loading animation for better UX */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dashboard-card {
    animation: fadeInUp 0.6s ease forwards;
}

.dashboard-card:nth-child(1) { animation-delay: 0.1s; }
.dashboard-card:nth-child(2) { animation-delay: 0.2s; }
.dashboard-card:nth-child(3) { animation-delay: 0.3s; }
</style>
@endpush
