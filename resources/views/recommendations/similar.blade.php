@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Livros Similares a "{{ $book->title }}"</h1>
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
                    <h5 class="mb-0">Livro de Referência</h5>
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
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Livros Similares</h5>
                </div>
                <div class="card-body">
                    @if($similarBooks->count() > 0)
                        <div class="row">
                            @foreach($similarBooks as $similarBook)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $similarBook->title }}</h5>
                                            <h6 class="card-subtitle mb-2 text-muted">{{ $similarBook->author }}</h6>
                                            <p class="card-text">
                                                <span class="badge bg-primary">{{ $similarBook->category->name }}</span>
                                                <span class="badge bg-secondary">{{ $similarBook->publisher }}</span>
                                            </p>
                                            <p class="card-text">
                                                <small class="text-muted">{{ Str::limit($similarBook->description, 100) }}</small>
                                            </p>
                                            <p class="card-text fw-bold">Kz {{ number_format($similarBook->price, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="card-footer">
                                            <a href="{{ route('books.show', $similarBook->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Detalhes
                                            </a>
                                            <a href="{{ route('recommendations.similar', $similarBook->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-shuffle"></i> Ver Similares
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Não há livros similares disponíveis para este livro no momento.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Critérios de Similaridade</h5>
        </div>
        <div class="card-body">
            <p>Os livros similares são determinados com base nos seguintes critérios:</p>
            <ul>
                <li>Mesma categoria literária</li>
                <li>Mesmo autor</li>
                <li>Temas relacionados</li>
                <li>Padrões de compra de clientes que adquiriram este livro</li>
            </ul>
        </div>
    </div>
</div>
@endsection
