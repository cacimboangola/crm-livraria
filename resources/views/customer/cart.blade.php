@extends('layouts.customer')

@section('content')
<div class="container">
    <h1 class="mb-4">Meu Carrinho</h1>
    
    @if(empty($cart))
        <div class="alert alert-info">
            <h4 class="alert-heading">Seu carrinho está vazio!</h4>
            <p>Você ainda não adicionou nenhum livro ao seu carrinho. Explore nosso <a href="{{ route('customer.catalog') }}" class="alert-link">catálogo</a> e encontre livros incríveis!</p>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Itens do Carrinho</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">Capa</th>
                                        <th>Livro</th>
                                        <th style="width: 120px;">Preço</th>
                                        <th style="width: 150px;">Quantidade</th>
                                        <th style="width: 120px;">Subtotal</th>
                                        <th style="width: 80px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $id => $item)
                                        <tr>
                                            <td>
                                                @if(isset($item['cover']) && $item['cover'])
                                                    <img src="{{ asset('storage/' . $item['cover']) }}" alt="{{ $item['title'] }}" class="img-thumbnail" style="max-height: 80px;">
                                                @else
                                                    <img src="{{ asset('images/book-placeholder.jpg') }}" alt="Capa não disponível" class="img-thumbnail" style="max-height: 80px;">
                                                @endif
                                            </td>
                                            <td>
                                                <h6 class="mb-0">{{ $item['title'] }}</h6>
                                            </td>
                                            <td>R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                                            <td>
                                                <form action="{{ route('customer.cart.update') }}" method="POST" class="d-flex">
                                                    @csrf
                                                    <input type="hidden" name="book_id" value="{{ $id }}">
                                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm me-2" style="width: 70px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}</td>
                                            <td>
                                                <form action="{{ route('customer.cart.remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="book_id" value="{{ $id }}">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('customer.catalog') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i> Continuar Comprando
                            </a>
                            <form action="{{ route('customer.cart.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="clear_cart" value="1">
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-trash me-2"></i> Limpar Carrinho
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Resumo do Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Frete:</span>
                            <span>Grátis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3 fw-bold">
                            <span>Total:</span>
                            <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                        
                        <form action="{{ route('customer.checkout') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Forma de Pagamento:</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="credit_card">Cartão de Crédito</option>
                                    <option value="debit_card">Cartão de Débito</option>
                                    <option value="bank_transfer">Transferência Bancária</option>
                                    <option value="pix">PIX</option>
                                    <option value="boleto">Boleto Bancário</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check-circle me-2"></i> Finalizar Pedido
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Cupom de Desconto</h5>
                    </div>
                    <div class="card-body">
                        <form action="#" method="POST">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Digite o código">
                                <button class="btn btn-outline-primary" type="submit">Aplicar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
