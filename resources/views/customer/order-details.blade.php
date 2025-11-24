@extends('layouts.customer')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.orders') }}">Meus Pedidos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pedido #{{ $invoice->id }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-3">Detalhes do Pedido #{{ $invoice->id }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('customer.order.pdf', $invoice) }}" class="btn btn-outline-primary me-2" target="_blank">
                <i class="fas fa-file-pdf me-2"></i> Baixar PDF
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Itens do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Capa</th>
                                    <th>Livro</th>
                                    <th>Preço</th>
                                    <th>Qtd</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->invoiceItems as $item)
                                    <tr>
                                        <td>
                                            @if($item->book && $item->book->cover_image)
                                                <img src="{{ asset('storage/' . $item->book->cover_image) }}" alt="{{ $item->description }}" class="img-thumbnail" style="max-height: 80px;">
                                            @else
                                                <img src="{{ asset('images/book-placeholder.jpg') }}" alt="Capa não disponível" class="img-thumbnail" style="max-height: 80px;">
                                            @endif
                                        </td>
                                        <td>
                                            <h6 class="mb-0">{{ $item->description }}</h6>
                                            @if($item->book)
                                                <small class="text-muted">{{ $item->book->author }}</small>
                                            @endif
                                        </td>
                                        <td>Kz {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>Kz {{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            @if($invoice->status == 'pending')
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">Instruções de Pagamento</h5>
                    </div>
                    <div class="card-body">
                        @switch($invoice->payment_method)
                            @case('credit_card')
                                <p>Seu pagamento com cartão de crédito está sendo processado. Você receberá uma confirmação por e-mail assim que for aprovado.</p>
                                @break
                            @case('debit_card')
                                <p>Seu pagamento com cartão de débito está sendo processado. Você receberá uma confirmação por e-mail assim que for aprovado.</p>
                                @break
                            @case('bank_transfer')
                                <h6>Dados para Transferência:</h6>
                                <p>Banco: Banco BIC<br>
                                IBAN: AO06 0000 0000 0000 0000 0000 0<br>
                                NIF: 000000000<br>
                                Favorecido: Livraria CRM Ltda</p>
                                <p>Após realizar a transferência, envie o comprovante para <strong>financeiro@crm-livraria.com</strong> informando o número do seu pedido.</p>
                                @break
                            @case('multicaixa')
                                <h6>Pagamento via Multicaixa Express:</h6>
                                <p>Referência: <strong>{{ str_pad($invoice->id, 9, '0', STR_PAD_LEFT) }}</strong></p>
                                <p>Entidade: <strong>11223</strong></p>
                                <p>Valor: <strong>Kz {{ number_format($invoice->total, 2, ',', '.') }}</strong></p>
                                <p>Use a referência acima para efetuar o pagamento em qualquer terminal Multicaixa Express ou através do aplicativo.</p>
                                @break
                            @case('cash')
                                <h6>Pagamento em Dinheiro:</h6>
                                <p>O pagamento será realizado na entrega do pedido.</p>
                                <p>Certifique-se de ter o valor exato: <strong>Kz {{ number_format($invoice->total, 2, ',', '.') }}</strong></p>
                                @break
                            @default
                                <p>Entre em contato com nossa equipe para obter instruções sobre como realizar o pagamento.</p>
                        @endswitch
                    </div>
                </div>
            @endif
            
            @if($invoice->status == 'paid' && $loyaltyPoints > 0)
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-star me-2"></i> Pontos de Fidelidade</h5>
                    </div>
                    <div class="card-body">
                        <p>Você ganhou <strong>{{ $loyaltyPoints }} pontos</strong> com este pedido!</p>
                        <p>Acumule pontos e troque por descontos em compras futuras.</p>
                        <a href="{{ route('customer.loyalty') }}" class="btn btn-outline-success">
                            <i class="fas fa-gift me-2"></i> Ver Meus Pontos
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Status:</span>
                        @if($invoice->status == 'paid')
                            <span class="badge bg-success">Pago</span>
                        @elseif($invoice->status == 'pending')
                            <span class="badge bg-warning">Pendente</span>
                        @elseif($invoice->status == 'cancelled')
                            <span class="badge bg-danger">Cancelado</span>
                        @else
                            <span class="badge bg-secondary">{{ $invoice->status }}</span>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Data do Pedido:</span>
                        <span>{{ $invoice->invoice_date->format('d/m/Y') }}</span>
                    </div>
                    
                    @if($invoice->payment_date)
                        <div class="d-flex justify-content-between mb-3">
                            <span>Data do Pagamento:</span>
                            <span>{{ $invoice->payment_date->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Método de Pagamento:</span>
                        <span>
                            @switch($invoice->payment_method)
                                @case('credit_card')
                                    Cartão de Crédito
                                    @break
                                @case('debit_card')
                                    Cartão de Débito
                                    @break
                                @case('bank_transfer')
                                    Transferência Bancária
                                    @break
                                @case('multicaixa')
                                    Multicaixa Express
                                    @break
                                @case('cash')
                                    Pagamento em Dinheiro
                                    @break
                                @default
                                    {{ $invoice->payment_method }}
                            @endswitch
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <span>Kz {{ number_format($invoice->total, 2, ',', '.') }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Frete:</span>
                        <span>Grátis</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3 fw-bold">
                        <span>Total:</span>
                        <span>Kz {{ number_format($invoice->total, 2, ',', '.') }}</span>
                    </div>
                    
                    @if($invoice->status == 'pending')
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#confirmPaymentModal">
                                <i class="fas fa-check-circle me-2"></i> Confirmar Pagamento
                            </button>
                            
                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                                <i class="fas fa-times-circle me-2"></i> Cancelar Pedido
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Endereço de Entrega</h5>
                </div>
                <div class="card-body">
                    <address>
                        <strong>{{ $invoice->customer->name }}</strong><br>
                        {{ $invoice->customer->address }}<br>
                        {{ $invoice->customer->city }}, {{ $invoice->customer->state }}<br>
                        Código Postal: {{ $invoice->customer->postal_code }}<br>
                        <abbr title="Telefone">Tel:</abbr> {{ $invoice->customer->phone }}
                    </address>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Precisa de Ajuda?</h5>
                </div>
                <div class="card-body">
                    <p>Se você tiver alguma dúvida sobre seu pedido, entre em contato conosco:</p>
                    <p><i class="fas fa-envelope me-2"></i> contato@livraria-crm.com</p>
                    <p><i class="fas fa-phone me-2"></i> (+244) 923-456-789</p>
                    <a href="#" class="btn btn-outline-primary w-100">
                        <i class="fas fa-comment me-2"></i> Iniciar Chat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Pagamento -->
<div class="modal fade" id="confirmPaymentModal" tabindex="-1" aria-labelledby="confirmPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title" id="confirmPaymentModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Confirmar Pagamento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="fas fa-money-bill-wave text-success" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center mb-3">Você está prestes a confirmar o pagamento deste pedido.</p>
                <div class="alert alert-info border-0">
                    <strong>Pedido #{{ $invoice->id }}</strong><br>
                    <strong>Valor:</strong> Kz {{ number_format($invoice->total, 2, ',', '.') }}<br>
                    <strong>Método:</strong> 
                    @switch($invoice->payment_method)
                        @case('credit_card') Cartão de Crédito @break
                        @case('debit_card') Cartão de Débito @break
                        @case('bank_transfer') Transferência Bancária @break
                        @case('multicaixa') Multicaixa Express @break
                        @case('cash') Pagamento em Dinheiro @break
                        @default {{ $invoice->payment_method }}
                    @endswitch
                </div>
                <p class="text-muted small mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Após a confirmação, você receberá pontos de fidelidade e o status do pedido será atualizado.
                </p>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <form action="{{ route('customer.order.pay', $invoice->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-2"></i>Confirmar Pagamento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cancelamento de Pedido -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="cancelOrderModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Cancelar Pedido
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="fas fa-times-circle text-danger" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center mb-3"><strong>Tem certeza que deseja cancelar este pedido?</strong></p>
                <div class="alert alert-warning border-0">
                    <strong>Pedido #{{ $invoice->id }}</strong><br>
                    <strong>Valor:</strong> Kz {{ number_format($invoice->total, 2, ',', '.') }}
                </div>
                <p class="text-danger small mb-0">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    <strong>Atenção:</strong> Esta ação não pode ser desfeita. O pedido será cancelado permanentemente.
                </p>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </button>
                <form action="{{ route('customer.order.cancel', $invoice->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-2"></i>Sim, Cancelar Pedido
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
