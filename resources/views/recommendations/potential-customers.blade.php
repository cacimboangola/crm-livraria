@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Clientes Potenciais para "{{ $book->title }}"</h1>
        <div class="btn-group" role="group">
            <a href="{{ route('books.show', $book->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar para Livro
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detalhes do Livro</h5>
                </div>
                <div class="card-body">
                    <h4>{{ $book->title }}</h4>
                    <h6 class="text-muted">{{ $book->author }}</h6>
                    <p>
                        <span class="badge bg-primary">{{ $book->category->name }}</span>
                        <span class="badge bg-secondary">{{ $book->publisher }}</span>
                    </p>
                    <p>{{ Str::limit($book->description, 200) }}</p>
                    <p class="fw-bold">Kz {{ number_format($book->price, 2, ',', '.') }}</p>
                    <p>
                        <span class="badge bg-success">Estoque: {{ $book->stock }}</span>
                        <span class="badge {{ $book->active ? 'bg-success' : 'bg-danger' }}">
                            {{ $book->active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('recommendations.similar', $book->id) }}" class="btn btn-outline-info">
                        <i class="bi bi-shuffle"></i> Ver Livros Similares
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Clientes Potenciais</h5>
                    <span class="badge bg-primary">{{ $potentialCustomers->count() }} cliente(s)</span>
                </div>
                <div class="card-body">
                    @if($potentialCustomers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th>Compras</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($potentialCustomers as $customer)
                                        <tr>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->phone }}</td>
                                            <td>{{ $customer->invoices->count() }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('recommendations.customer', $customer->id) }}" class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-lightbulb"></i>
                                                    </a>
                                                    <a href="{{ route('invoices.create', ['customer_id' => $customer->id, 'book_id' => $book->id]) }}" class="btn btn-sm btn-success">
                                                        <i class="bi bi-cart-plus"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Não há clientes potenciais identificados para este livro no momento.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Estratégias de Marketing</h5>
        </div>
        <div class="card-body">
            <p>Estes clientes foram identificados como potenciais compradores deste livro com base em:</p>
            <ul>
                <li>Histórico de compras de livros da mesma categoria</li>
                <li>Preferência pelo mesmo autor</li>
                <li>Padrões de compra similares a outros clientes que adquiriram este livro</li>
                <li>Interesses demonstrados em compras anteriores</li>
            </ul>
            <p>Sugestões de ação:</p>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5><i class="bi bi-envelope"></i> Email Marketing</h5>
                            <p>Envie um email personalizado destacando este livro e oferecendo um desconto especial.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5><i class="bi bi-telephone"></i> Contato Telefônico</h5>
                            <p>Ligue para informar sobre o lançamento ou disponibilidade deste livro.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5><i class="bi bi-tag"></i> Oferta Especial</h5>
                            <p>Crie uma promoção específica para este grupo de clientes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
