@extends('layouts.customer')

@section('content')
<!-- Page Header -->
<div class="page-header bg-gradient-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-receipt me-3"></i>
                    Meus Pedidos
                </h1>
                <p class="lead mb-0">Acompanhe o histórico e status de todos os seus pedidos</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="stats-card bg-white bg-opacity-10 rounded-4 p-3">
                    <div class="text-center">
                        <div class="h2 fw-bold mb-1">{{ $invoices->count() }}</div>
                        <small class="text-white-50">Total de Pedidos</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    @if($invoices->isEmpty())
        <!-- Empty State -->
        <div class="empty-state-modern text-center py-5">
            <div class="mb-4">
                <i class="fas fa-shopping-cart text-muted" style="font-size: 5rem;"></i>
            </div>
            <h3 class="text-muted mb-3">Nenhum pedido realizado</h3>
            <p class="text-muted mb-4 lead">Você ainda não realizou nenhum pedido. Explore nosso catálogo e encontre livros incríveis!</p>
            <a href="{{ route('customer.catalog') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-books me-2"></i>
                Explorar Catálogo
            </a>
        </div>
    @else
        <!-- Orders Grid -->
        <div class="row g-4">
            @foreach($invoices as $invoice)
                <div class="col-12">
                    <div class="order-card card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <!-- Order Info -->
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <div class="order-icon me-3">
                                            <i class="fas fa-file-invoice text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold">Pedido #{{ $invoice->id }}</h6>
                                            <small class="text-muted">{{ $invoice->invoice_number ?? 'INV-' . $invoice->id }}</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date -->
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <div class="fw-semibold">{{ $invoice->invoice_date->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $invoice->invoice_date->format('H:i') }}</small>
                                    </div>
                                </div>

                                <!-- Total -->
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <div class="h5 mb-0 fw-bold text-success">Kz {{ number_format($invoice->total, 2, ',', '.') }}</div>
                                        <small class="text-muted">Total</small>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-2">
                                    <div class="text-center">
                                        @if($invoice->status == 'paid')
                                            <span class="badge-modern bg-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Pago
                                            </span>
                                        @elseif($invoice->status == 'pending')
                                            <span class="badge-modern bg-warning">
                                                <i class="fas fa-clock me-1"></i>
                                                Pendente
                                            </span>
                                        @elseif($invoice->status == 'cancelled')
                                            <span class="badge-modern bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>
                                                Cancelado
                                            </span>
                                        @else
                                            <span class="badge-modern bg-secondary">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Payment Method -->
                                <div class="col-md-2">
                                    <div class="text-center">
                                        @switch($invoice->payment_method)
                                            @case('credit_card')
                                                <i class="fas fa-credit-card text-primary me-1"></i>
                                                <span class="small">Cartão de Crédito</span>
                                                @break
                                            @case('debit_card')
                                                <i class="fas fa-credit-card text-info me-1"></i>
                                                <span class="small">Cartão de Débito</span>
                                                @break
                                            @case('bank_transfer')
                                                <i class="fas fa-university text-success me-1"></i>
                                                <span class="small">Transferência</span>
                                                @break
                                            @case('pix')
                                                <i class="fas fa-qrcode text-success me-1"></i>
                                                <span class="small">PIX</span>
                                                @break
                                            @case('boleto')
                                                <i class="fas fa-barcode text-warning me-1"></i>
                                                <span class="small">Boleto</span>
                                                @break
                                            @default
                                                <i class="fas fa-money-bill text-muted me-1"></i>
                                                <span class="small">{{ $invoice->payment_method }}</span>
                                        @endswitch
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="col-md-1">
                                    <div class="text-end">
                                        <a href="{{ route('customer.order.details', $invoice->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($invoices->hasPages())
            <div class="d-flex justify-content-center mt-5">
                <div class="pagination-modern">
                    {{ $invoices->links() }}
                </div>
            </div>
        @endif
    @endif
</div>
@endsection

@push('styles')
<style>
/* Modern Orders Page Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
}

.page-header {
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.1;
}

.stats-card {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
}

.empty-state-modern {
    padding: 4rem 2rem;
    background: linear-gradient(135deg, rgba(26, 26, 46, 0.02) 0%, rgba(233, 69, 96, 0.02) 100%);
    border-radius: 20px;
    border: 1px solid rgba(26, 26, 46, 0.1);
}

.order-card {
    border-radius: 16px;
    transition: all 0.3s ease;
    border: 1px solid rgba(26, 26, 46, 0.1);
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    border-color: var(--accent-color);
}

.order-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, rgba(26, 26, 46, 0.1) 0%, rgba(26, 26, 46, 0.05) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.badge-modern {
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.875rem;
    border: 2px solid transparent;
    display: inline-flex;
    align-items: center;
}

.badge-modern.bg-success {
    background: linear-gradient(135deg, rgba(15, 52, 96, 0.1) 0%, rgba(15, 52, 96, 0.05) 100%) !important;
    color: #0f3460 !important;
    border-color: #0f3460;
}

.badge-modern.bg-warning {
    background: linear-gradient(135deg, rgba(233, 69, 96, 0.1) 0%, rgba(233, 69, 96, 0.05) 100%) !important;
    color: #e94560 !important;
    border-color: #e94560;
}

.badge-modern.bg-danger {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%) !important;
    color: #dc3545 !important;
    border-color: #dc3545;
}

.badge-modern.bg-secondary {
    background: linear-gradient(135deg, rgba(108, 117, 125, 0.1) 0%, rgba(108, 117, 125, 0.05) 100%) !important;
    color: #6c757d !important;
    border-color: #6c757d;
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

.btn-outline-primary {
    border-color: #1a1a2e;
    color: #1a1a2e;
}

.btn-outline-primary:hover {
    background: #1a1a2e;
    border-color: #1a1a2e;
    color: white;
}

.pagination-modern .pagination {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.pagination-modern .page-link {
    border: none;
    padding: 0.75rem 1rem;
    color: #1a1a2e;
    font-weight: 500;
}

.pagination-modern .page-link:hover {
    background: #1a1a2e;
    color: white;
}

.pagination-modern .page-item.active .page-link {
    background: #1a1a2e;
    border-color: #1a1a2e;
}

/* Responsive Design */
@media (max-width: 768px) {
    .order-card .row > div {
        text-align: center !important;
        margin-bottom: 1rem;
    }
    
    .order-card .row > div:last-child {
        margin-bottom: 0;
    }
    
    .page-header .col-md-4 {
        margin-top: 2rem;
    }
}
</style>
@endpush
