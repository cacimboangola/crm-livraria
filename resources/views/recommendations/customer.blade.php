@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Recomendações para {{ $customer->name }}</h1>
        <div class="btn-group" role="group">
            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar para Cliente
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Livros Recomendados</h5>
        </div>
        <div class="card-body">
            @if($recommendations->count() > 0)
                <div class="row">
                    @foreach($recommendations as $book)
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $book->title }}</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">{{ $book->author }}</h6>
                                    <p class="card-text">
                                        <span class="badge bg-primary">{{ $book->category->name }}</span>
                                        <span class="badge bg-secondary">{{ $book->publisher }}</span>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">{{ Str::limit($book->description, 100) }}</small>
                                    </p>
                                    <p class="card-text fw-bold">Kz {{ number_format($book->price, 2, ',', '.') }}</p>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detalhes
                                    </a>
                                    <a href="{{ route('invoices.create', ['customer_id' => $customer->id, 'book_id' => $book->id]) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-cart-plus"></i> Vender
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Não há recomendações disponíveis para este cliente no momento.
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Por que estas recomendações?</h5>
        </div>
        <div class="card-body">
            <p>Estas recomendações são baseadas em:</p>
            <ul>
                <li>Histórico de compras do cliente</li>
                <li>Preferências de categorias</li>
                <li>Livros populares que o cliente ainda não adquiriu</li>
                <li>Autores preferidos do cliente</li>
            </ul>
            <p>As recomendações são atualizadas automaticamente conforme o cliente realiza novas compras.</p>
        </div>
    </div>
</div>
@endsection
