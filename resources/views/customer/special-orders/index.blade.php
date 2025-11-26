@extends('layouts.customer')

@section('title', 'Meus Pedidos Especiais')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">📚 Meus Pedidos Especiais</h1>
                    <p class="text-muted mb-0">Acompanhe o status dos livros que você solicitou</p>
                </div>
                <a href="{{ route('customer.catalog') }}" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i> Buscar Mais Livros
                </a>
            </div>

            <!-- Cards de Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-primary mb-2">
                                <i class="fas fa-book-open fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ $counts['total'] }}</h4>
                            <small class="text-muted">Total de Pedidos</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-warning mb-2">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ $counts['pending'] }}</h4>
                            <small class="text-muted">Aguardando</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-info mb-2">
                                <i class="fas fa-shipping-fast fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ $counts['active'] }}</h4>
                            <small class="text-muted">Em Andamento</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ $counts['delivered'] }}</h4>
                            <small class="text-muted">Entregues</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Filtrar por Status</label>
                            <select name="status" class="form-select">
                                <option value="">Todos os Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Aguardando Encomenda</option>
                                <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Encomendado</option>
                                <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Recebido na Loja</option>
                                <option value="notified" {{ request('status') == 'notified' ? 'selected' : '' }}>Pronto para Retirada</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregue</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-8 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                            <a href="{{ route('customer.special-orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Pedidos -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($specialOrders->count() > 0)
                        <div class="row">
                            @foreach($specialOrders as $order)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Pedido #{{ $order->id }}</small>
                                            <span class="badge {{ $order->status_badge_class }}">
                                                {{ $order->status_formatted }}
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="card-title mb-2">{{ $order->book_title }}</h6>
                                            @if($order->book_author)
                                                <p class="text-muted small mb-2">
                                                    <i class="fas fa-user"></i> {{ $order->book_author }}
                                                </p>
                                            @endif
                                            <div class="row text-center mb-3">
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Quantidade</small>
                                                    <strong>{{ $order->quantity }}</strong>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Entrega</small>
                                                    <strong>{{ $order->delivery_preference_formatted }}</strong>
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> 
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="card-footer bg-transparent border-0">
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('customer.special-orders.show', $order->id) }}" 
                                                   class="btn btn-primary btn-sm flex-fill">
                                                    <i class="fas fa-eye"></i> Ver Detalhes
                                                </a>
                                                @if($order->canBeCancelled())
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm"
                                                            onclick="confirmCancel({{ $order->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginação -->
                        @if($specialOrders->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $specialOrders->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-book-open fa-4x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-3">Nenhum pedido especial encontrado</h5>
                            <p class="text-muted mb-4">
                                Você ainda não fez nenhum pedido especial. 
                                Use nosso chatbot para solicitar livros que não estão em estoque!
                            </p>
                            <a href="{{ route('customer.catalog') }}" class="btn btn-primary">
                                <i class="fas fa-search"></i> Explorar Catálogo
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Cancelamento -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Pedido Especial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja cancelar este pedido especial?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não, Manter</button>
                <form id="cancelForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">Sim, Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmCancel(orderId) {
    const form = document.getElementById('cancelForm');
    form.action = `/cliente/pedidos-especiais/${orderId}/cancelar`;
    
    const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    modal.show();
}
</script>
@endpush
@endsection
