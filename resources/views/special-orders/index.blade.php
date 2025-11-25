@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Pedidos Especiais</h1>
        <a href="{{ route('special-orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Novo Pedido Especial
        </a>
    </div>

    {{-- Cards de Resumo --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted">Aguardando Encomenda</h6>
                            <h2 class="card-title mb-0 text-warning">{{ $counts['pending'] }}</h2>
                        </div>
                        <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted">Encomendados</h6>
                            <h2 class="card-title mb-0 text-info">{{ $counts['ordered'] }}</h2>
                        </div>
                        <i class="bi bi-truck text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted">Recebidos na Loja</h6>
                            <h2 class="card-title mb-0 text-primary">{{ $counts['received'] }}</h2>
                        </div>
                        <i class="bi bi-box-seam text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted">Total Ativos</h6>
                            <h2 class="card-title mb-0">{{ $counts['total_active'] }}</h2>
                        </div>
                        <i class="bi bi-list-check text-secondary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('special-orders.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" 
                               placeholder="Buscar por título, autor, ISBN ou cliente..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Todos os Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Aguardando Encomenda</option>
                        <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Encomendado</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Recebido na Loja</option>
                        <option value="notified" {{ request('status') == 'notified' ? 'selected' : '' }}>Cliente Notificado</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregue</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <a href="{{ route('special-orders.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabela de Pedidos --}}
    <div class="card">
        <div class="card-header bg-white">
            <i class="bi bi-book"></i> Lista de Pedidos Especiais
        </div>
        <div class="card-body p-0">
            @if($specialOrders->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Nenhum pedido especial encontrado.</p>
                    <a href="{{ route('special-orders.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Criar Primeiro Pedido
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Livro</th>
                                <th>Cliente</th>
                                <th>Quantidade</th>
                                <th>Entrega</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($specialOrders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>
                                        <strong>{{ $order->book_title }}</strong>
                                        @if($order->book_author)
                                            <br><small class="text-muted">{{ $order->book_author }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $order->customer->name }}
                                        <br><small class="text-muted">{{ $order->customer->phone }}</small>
                                    </td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>
                                        @if($order->delivery_preference === 'pickup')
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-shop"></i> Retirada
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-truck"></i> Entrega
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $order->status_badge_class }}">
                                            {{ $order->status_formatted }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('special-orders.show', $order) }}" 
                                               class="btn btn-outline-primary" title="Ver detalhes">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($order->canAdvanceStatus())
                                                <form action="{{ route('special-orders.advance-status', $order) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-success" 
                                                            title="Avançar para: {{ $order->next_status ? \App\Models\SpecialOrder::where('status', $order->next_status)->first()?->status_formatted ?? ucfirst($order->next_status) : '' }}">
                                                        <i class="bi bi-arrow-right-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('special-orders.edit', $order) }}" 
                                               class="btn btn-outline-secondary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($specialOrders->hasPages())
            <div class="card-footer bg-white">
                {{ $specialOrders->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
