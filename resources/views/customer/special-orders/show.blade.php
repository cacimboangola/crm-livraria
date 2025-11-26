@extends('layouts.customer')

@section('title', 'Pedido Especial #' . $specialOrder->id)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">📚 Pedido Especial #{{ $specialOrder->id }}</h1>
                    <p class="text-muted mb-0">{{ $specialOrder->book_title }}</p>
                </div>
                <div>
                    <span class="badge {{ $specialOrder->status_badge_class }} fs-6 me-2">
                        {{ $specialOrder->status_formatted }}
                    </span>
                    <a href="{{ route('customer.special-orders.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informações do Livro -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-book"></i> Informações do Livro
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4 class="mb-3">{{ $specialOrder->book_title }}</h4>
                                    
                                    @if($specialOrder->book_author)
                                        <div class="mb-3">
                                            <label class="text-muted small">Autor</label>
                                            <p class="mb-0"><strong>{{ $specialOrder->book_author }}</strong></p>
                                        </div>
                                    @endif
                                    
                                    @if($specialOrder->book_publisher)
                                        <div class="mb-3">
                                            <label class="text-muted small">Editora</label>
                                            <p class="mb-0">{{ $specialOrder->book_publisher }}</p>
                                        </div>
                                    @endif
                                    
                                    @if($specialOrder->book_isbn)
                                        <div class="mb-3">
                                            <label class="text-muted small">ISBN</label>
                                            <p class="mb-0"><code>{{ $specialOrder->book_isbn }}</code></p>
                                        </div>
                                    @endif
                                    
                                    @if($specialOrder->customer_notes)
                                        <div class="mb-3">
                                            <label class="text-muted small">Suas Observações</label>
                                            <div class="alert alert-light">
                                                <i class="fas fa-comment"></i> {{ $specialOrder->customer_notes }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="bg-light rounded p-4 mb-3">
                                            <i class="fas fa-boxes fa-3x text-primary mb-2"></i>
                                            <h3 class="mb-0">{{ $specialOrder->quantity }}</h3>
                                            <small class="text-muted">
                                                {{ $specialOrder->quantity == 1 ? 'exemplar' : 'exemplares' }}
                                            </small>
                                        </div>
                                        
                                        <div class="bg-light rounded p-3">
                                            <i class="fas fa-{{ $specialOrder->delivery_preference == 'pickup' ? 'store' : 'truck' }} text-info mb-2"></i>
                                            <p class="mb-0 small">
                                                <strong>{{ $specialOrder->delivery_preference_formatted }}</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline do Pedido -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-history"></i> Acompanhamento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <!-- Pedido Criado -->
                                <div class="timeline-item completed">
                                    <div class="timeline-marker bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Pedido Criado</h6>
                                        <p class="text-muted small mb-0">
                                            {{ $specialOrder->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Encomendado -->
                                <div class="timeline-item {{ $specialOrder->ordered_at ? 'completed' : 'pending' }}">
                                    <div class="timeline-marker {{ $specialOrder->ordered_at ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="fas fa-{{ $specialOrder->ordered_at ? 'check' : 'clock' }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Encomendado ao Fornecedor</h6>
                                        <p class="text-muted small mb-0">
                                            @if($specialOrder->ordered_at)
                                                {{ $specialOrder->ordered_at->format('d/m/Y H:i') }}
                                            @else
                                                Aguardando...
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Recebido -->
                                <div class="timeline-item {{ $specialOrder->received_at ? 'completed' : 'pending' }}">
                                    <div class="timeline-marker {{ $specialOrder->received_at ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="fas fa-{{ $specialOrder->received_at ? 'check' : 'clock' }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Recebido na Loja</h6>
                                        <p class="text-muted small mb-0">
                                            @if($specialOrder->received_at)
                                                {{ $specialOrder->received_at->format('d/m/Y H:i') }}
                                            @else
                                                Aguardando...
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Notificado -->
                                <div class="timeline-item {{ $specialOrder->notified_at ? 'completed' : 'pending' }}">
                                    <div class="timeline-marker {{ $specialOrder->notified_at ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="fas fa-{{ $specialOrder->notified_at ? 'check' : 'clock' }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Pronto para {{ $specialOrder->delivery_preference == 'pickup' ? 'Retirada' : 'Entrega' }}</h6>
                                        <p class="text-muted small mb-0">
                                            @if($specialOrder->notified_at)
                                                {{ $specialOrder->notified_at->format('d/m/Y H:i') }}
                                            @else
                                                Aguardando...
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Entregue -->
                                <div class="timeline-item {{ $specialOrder->delivered_at ? 'completed' : 'pending' }}">
                                    <div class="timeline-marker {{ $specialOrder->delivered_at ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="fas fa-{{ $specialOrder->delivered_at ? 'check' : 'clock' }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">{{ $specialOrder->delivery_preference == 'pickup' ? 'Retirado' : 'Entregue' }}</h6>
                                        <p class="text-muted small mb-0">
                                            @if($specialOrder->delivered_at)
                                                {{ $specialOrder->delivered_at->format('d/m/Y H:i') }}
                                            @else
                                                Aguardando...
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            @if($specialOrder->canBeCancelled())
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="mb-3">Ações Disponíveis</h6>
                                <button type="button" 
                                        class="btn btn-outline-danger"
                                        onclick="confirmCancel()">
                                    <i class="fas fa-times"></i> Cancelar Pedido
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Informações de Contato -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Precisa de Ajuda?</h6>
                        <p class="mb-2">Entre em contato conosco para mais informações sobre seu pedido:</p>
                        <div class="row">
                            <div class="col-md-4">
                                <strong><i class="fas fa-phone"></i> Telefone:</strong><br>
                                <a href="tel:{{ config('contact.phone.number') }}">{{ config('contact.phone.display') }}</a>
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-envelope"></i> Email:</strong><br>
                                <a href="mailto:{{ config('contact.email.general') }}">{{ config('contact.email.general') }}</a>
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fab fa-whatsapp"></i> WhatsApp:</strong><br>
                                <a href="https://wa.me/{{ config('contact.whatsapp.number') }}?text={{ urlencode('Olá! Gostaria de informações sobre meu pedido especial #' . $specialOrder->id) }}" 
                                   target="_blank">{{ config('contact.whatsapp.display') }}</a>
                            </div>
                        </div>
                    </div>
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
                <div class="alert alert-warning">
                    <strong>Atenção:</strong> Esta ação não pode ser desfeita e o pedido será removido permanentemente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não, Manter</button>
                <form method="POST" action="{{ route('customer.special-orders.cancel', $specialOrder->id) }}" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">Sim, Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content h6 {
    margin-bottom: 5px;
    color: #495057;
}

.timeline-item.completed .timeline-content h6 {
    color: #28a745;
}

.timeline-item.pending .timeline-content h6 {
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
function confirmCancel() {
    const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    modal.show();
}
</script>
@endpush
@endsection
