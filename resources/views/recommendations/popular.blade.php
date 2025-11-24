@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Livros Mais Populares</h1>
        <div class="btn-group" role="group">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar para Dashboard
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Top Vendas</h5>
            <span class="badge bg-primary">{{ $popularBooks->count() }} livro(s)</span>
        </div>
        <div class="card-body">
            @if($popularBooks->count() > 0)
                <div class="row">
                    @foreach($popularBooks as $index => $book)
                        <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                            <div class="card h-100 {{ $index < 3 ? 'border-primary' : '' }}">
                                @if($index < 3)
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            @if($index == 0)
                                                <i class="bi bi-trophy-fill"></i> 1º Lugar
                                            @elseif($index == 1)
                                                <i class="bi bi-award-fill"></i> 2º Lugar
                                            @else
                                                <i class="bi bi-star-fill"></i> 3º Lugar
                                            @endif
                                        </h5>
                                    </div>
                                @endif
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
                                    <p class="card-text">
                                        <span class="badge bg-success">Estoque: {{ $book->stock }}</span>
                                    </p>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detalhes
                                    </a>
                                    <a href="{{ route('recommendations.similar', $book->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-shuffle"></i> Similares
                                    </a>
                                    <a href="{{ route('recommendations.potential-customers', $book->id) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-people"></i> Clientes
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Não há dados de vendas suficientes para determinar os livros mais populares.
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Insights de Vendas</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Tendências de Vendas</h6>
                    <p>Os livros mais populares são determinados com base no volume total de vendas. Esta análise ajuda a identificar tendências de mercado e preferências dos clientes.</p>
                    <ul>
                        <li>Monitore regularmente os livros mais vendidos</li>
                        <li>Mantenha estoque adequado dos itens populares</li>
                        <li>Considere promoções para impulsionar vendas de livros similares</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Estratégias de Marketing</h6>
                    <p>Use estes dados para:</p>
                    <ul>
                        <li>Destacar os livros populares em sua loja física e online</li>
                        <li>Criar campanhas de marketing focadas nos autores mais vendidos</li>
                        <li>Desenvolver pacotes promocionais combinando bestsellers com livros menos conhecidos</li>
                        <li>Analisar o que torna estes livros populares e aplicar insights em futuras aquisições</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
