@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Pedido Especial #{{ $specialOrder->id }}</h1>
        <div>
            <a href="{{ route('special-orders.edit', $specialOrder) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('special-orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- Informações do Livro --}}
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-book"></i> Informações do Livro</span>
                    <span class="badge {{ $specialOrder->status_badge_class }} fs-6">
                        {{ $specialOrder->status_formatted }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ $specialOrder->book_title }}</h4>
                            @if($specialOrder->book_author)
                                <p class="text-muted mb-2">
                                    <i class="bi bi-person"></i> {{ $specialOrder->book_author }}
                                </p>
                            @endif
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-light text-dark fs-6">
                                {{ $specialOrder->quantity }} {{ $specialOrder->quantity > 1 ? 'exemplares' : 'exemplar' }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        @if($specialOrder->book_publisher)
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Editora</small>
                                <strong>{{ $specialOrder->book_publisher }}</strong>
                            </div>
                        @endif
                        @if($specialOrder->book_isbn)
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">ISBN</small>
                                <code>{{ $specialOrder->book_isbn }}</code>
                            </div>
                        @endif
                        @if($specialOrder->estimated_price)
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Preço Estimado</small>
                                <strong class="text-success">Kz {{ number_format($specialOrder->estimated_price, 2, ',', '.') }}</strong>
                            </div>
                        @endif
                    </div>

                    @if($specialOrder->customer_notes)
                        <div class="alert alert-light mb-0">
                            <small class="text-muted d-block mb-1"><i class="bi bi-chat-left-text"></i> Observações do Cliente</small>
                            {{ $specialOrder->customer_notes }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline de Status --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <i class="bi bi-clock-history"></i> Histórico do Pedido
                </div>
                <div class="card-body">
                    <div class="timeline">
                        {{-- Criado --}}
                        <div class="timeline-item {{ $specialOrder->created_at ? 'completed' : '' }}">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Pedido Criado</h6>
                                <small class="text-muted">
                                    {{ $specialOrder->created_at->format('d/m/Y H:i') }}
                                    - por {{ $specialOrder->user->name }}
                                </small>
                            </div>
                        </div>

                        {{-- Encomendado --}}
                        <div class="timeline-item {{ $specialOrder->ordered_at ? 'completed' : ($specialOrder->status == 'pending' ? 'current' : '') }}">
                            <div class="timeline-marker {{ $specialOrder->ordered_at ? 'bg-success' : 'bg-secondary' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Encomendado ao Fornecedor</h6>
                                @if($specialOrder->ordered_at)
                                    <small class="text-muted">{{ $specialOrder->ordered_at->format('d/m/Y H:i') }}</small>
                                @else
                                    <small class="text-muted">Aguardando...</small>
                                @endif
                            </div>
                        </div>

                        {{-- Recebido --}}
                        <div class="timeline-item {{ $specialOrder->received_at ? 'completed' : ($specialOrder->status == 'ordered' ? 'current' : '') }}">
                            <div class="timeline-marker {{ $specialOrder->received_at ? 'bg-success' : 'bg-secondary' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Recebido na Loja</h6>
                                @if($specialOrder->received_at)
                                    <small class="text-muted">{{ $specialOrder->received_at->format('d/m/Y H:i') }}</small>
                                @else
                                    <small class="text-muted">Aguardando...</small>
                                @endif
                            </div>
                        </div>

                        {{-- Notificado --}}
                        <div class="timeline-item {{ $specialOrder->notified_at ? 'completed' : ($specialOrder->status == 'received' ? 'current' : '') }}">
                            <div class="timeline-marker {{ $specialOrder->notified_at ? 'bg-success' : 'bg-secondary' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Cliente Notificado</h6>
                                @if($specialOrder->notified_at)
                                    <small class="text-muted">{{ $specialOrder->notified_at->format('d/m/Y H:i') }}</small>
                                @else
                                    <small class="text-muted">Aguardando...</small>
                                @endif
                            </div>
                        </div>

                        {{-- Entregue --}}
                        <div class="timeline-item {{ $specialOrder->delivered_at ? 'completed' : ($specialOrder->status == 'notified' ? 'current' : '') }}">
                            <div class="timeline-marker {{ $specialOrder->delivered_at ? 'bg-success' : 'bg-secondary' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Entregue ao Cliente</h6>
                                @if($specialOrder->delivered_at)
                                    <small class="text-muted">{{ $specialOrder->delivered_at->format('d/m/Y H:i') }}</small>
                                @else
                                    <small class="text-muted">Aguardando...</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notas do Fornecedor --}}
            @if($specialOrder->supplier_notes)
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <i class="bi bi-sticky"></i> Notas do Fornecedor
                    </div>
                    <div class="card-body">
                        {{ $specialOrder->supplier_notes }}
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            {{-- Dados do Cliente --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <i class="bi bi-person"></i> Dados do Cliente
                </div>
                <div class="card-body">
                    <h5 class="mb-1">{{ $specialOrder->customer->name }}</h5>
                    <p class="text-muted mb-3">
                        <i class="bi bi-envelope"></i> {{ $specialOrder->customer->email }}
                    </p>
                    @if($specialOrder->customer->phone)
                        <p class="mb-2">
                            <i class="bi bi-telephone"></i> {{ $specialOrder->customer->phone }}
                        </p>
                    @endif
                    @if($specialOrder->customer->address)
                        <p class="mb-0 small text-muted">
                            <i class="bi bi-geo-alt"></i> {{ $specialOrder->customer->address }}
                            @if($specialOrder->customer->city)
                                , {{ $specialOrder->customer->city }}
                            @endif
                        </p>
                    @endif

                    <hr>

                    <div class="d-flex align-items-center">
                        <i class="bi bi-truck me-2"></i>
                        <div>
                            <small class="text-muted d-block">Preferência de Entrega</small>
                            <strong>{{ $specialOrder->delivery_preference_formatted }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ações --}}
            <div class="card">
                <div class="card-header bg-white">
                    <i class="bi bi-lightning"></i> Ações Rápidas
                </div>
                <div class="card-body">
                    @if($specialOrder->canAdvanceStatus())
                        <form action="{{ route('special-orders.advance-status', $specialOrder) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-arrow-right-circle"></i> 
                                @switch($specialOrder->status)
                                    @case('pending')
                                        Marcar como Encomendado
                                        @break
                                    @case('ordered')
                                        Marcar como Recebido
                                        @break
                                    @case('received')
                                        Notificar Cliente
                                        @break
                                    @case('notified')
                                        Marcar como Entregue
                                        @break
                                @endswitch
                            </button>
                        </form>
                    @endif

                    @if($specialOrder->canBeCancelled())
                        <form action="{{ route('special-orders.cancel', $specialOrder) }}" method="POST" 
                              onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle"></i> Cancelar Pedido
                            </button>
                        </form>
                    @endif

                    @if($specialOrder->status === 'cancelled')
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle"></i> Este pedido foi cancelado.
                        </div>
                    @endif

                    @if($specialOrder->status === 'delivered')
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> Este pedido foi concluído com sucesso!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
    border-left: 2px solid #dee2e6;
    padding-left: 20px;
    margin-left: 8px;
}
.timeline-item:last-child {
    border-left: 2px solid transparent;
    padding-bottom: 0;
}
.timeline-item.completed {
    border-left-color: #198754;
}
.timeline-item.current {
    border-left-color: #0d6efd;
}
.timeline-marker {
    position: absolute;
    left: -10px;
    top: 0;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}
.timeline-item.completed .timeline-marker {
    box-shadow: 0 0 0 2px #198754;
}
.timeline-item.current .timeline-marker {
    box-shadow: 0 0 0 2px #0d6efd;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 2px #0d6efd; }
    50% { box-shadow: 0 0 0 6px rgba(13, 110, 253, 0.3); }
    100% { box-shadow: 0 0 0 2px #0d6efd; }
}
</style>
@endsection
