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
                                        <td>R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
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
                                <p>Banco: Banco do Brasil<br>
                                Agência: 1234-5<br>
                                Conta: 12345-6<br>
                                NIF: 000000000<br>
                                Favorecido: Livraria CRM Ltda</p>
                                <p>Após realizar a transferência, envie o comprovante para <strong>financeiro@crm-livraria.com</strong> informando o número do seu pedido.</p>
                                @break
                            @case('pix')
                                <div class="text-center mb-3">
                                    <img src="{{ asset('images/qrcode-pix.png') }}" alt="QR Code PIX" style="max-width: 200px;">
                                </div>
                                <p class="mb-3">Referência de Pagamento: 000000000</p>
                                <p>Após realizar o pagamento, envie o comprovante para <strong>financeiro@crm-livraria.com</strong> informando o número do seu pedido.</p>
                                @break
                            @case('boleto')
                                <p>O boleto foi enviado para seu e-mail. Você também pode baixá-lo clicando no botão abaixo:</p>
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="fas fa-barcode me-2"></i> Baixar Boleto
                                </a>
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
                                @case('pix')
                                    PIX
                                    @break
                                @case('boleto')
                                    Boleto Bancário
                                    @break
                                @default
                                    {{ $invoice->payment_method }}
                            @endswitch
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <span>R$ {{ number_format($invoice->total, 2, ',', '.') }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Frete:</span>
                        <span>Grátis</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3 fw-bold">
                        <span>Total:</span>
                        <span>R$ {{ number_format($invoice->total, 2, ',', '.') }}</span>
                    </div>
                    
                    @if($invoice->status == 'pending')
                        <div class="d-grid gap-2">
                            <form action="{{ route('customer.order.pay', $invoice->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Confirmar pagamento deste pedido?')">
                                    <i class="fas fa-check-circle me-2"></i> Confirmar Pagamento
                                </button>
                            </form>
                            
                            <form action="{{ route('customer.order.cancel', $invoice->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Tem certeza que deseja cancelar este pedido?')">
                                    <i class="fas fa-times-circle me-2"></i> Cancelar Pedido
                                </button>
                            </form>
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
                    <p><i class="fas fa-envelope me-2"></i> atendimento@crm-livraria.com</p>
                    <p><i class="fas fa-phone me-2"></i> (11) 1234-5678</p>
                    <a href="#" class="btn btn-outline-primary w-100">
                        <i class="fas fa-comment me-2"></i> Iniciar Chat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
