@extends('layouts.customer')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.catalog') }}">Catálogo</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $book->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="position-relative">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" class="card-img-top" alt="{{ $book->title }}" style="width: 100%; object-fit: cover;">
                    @else
                        <img src="{{ asset('images/book-placeholder.jpg') }}" class="card-img-top" alt="Capa não disponível" style="width: 100%; object-fit: cover;">
                    @endif
                    
                    @if($book->discount > 0)
                        <div class="position-absolute top-0 end-0 bg-danger text-white p-2">
                            -{{ $book->discount }}%
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <h1 class="mb-2">{{ $book->title }}</h1>
            <p class="text-muted mb-3">por <strong>{{ $book->author }}</strong></p>
            
            <div class="mb-4">
                <span class="badge bg-primary">{{ $book->category->name }}</span>
                @if($book->stock > 0)
                    <span class="badge bg-success">Em estoque</span>
                @else
                    <span class="badge bg-danger">Fora de estoque</span>
                @endif
            </div>
            
            <div class="mb-4">
                @if($book->discount > 0)
                    <p class="mb-1">
                        <span class="text-decoration-line-through text-muted fs-4">R$ {{ number_format($book->price, 2, ',', '.') }}</span>
                        <span class="text-danger fw-bold fs-2">R$ {{ number_format($book->price * (1 - $book->discount/100), 2, ',', '.') }}</span>
                    </p>
                    <p class="text-success">Você economiza: R$ {{ number_format($book->price * ($book->discount/100), 2, ',', '.') }}</p>
                @else
                    <p class="mb-1 fw-bold fs-2">R$ {{ number_format($book->price, 2, ',', '.') }}</p>
                @endif
            </div>
            
            <div class="mb-4">
                <form action="{{ route('customer.cart.add') }}" method="POST" class="d-flex align-items-center">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <div class="input-group me-3" style="width: 150px;">
                        <span class="input-group-text">Qtd</span>
                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ $book->stock }}">
                    </div>
                    <button type="submit" class="btn btn-primary" {{ $book->stock <= 0 ? 'disabled' : '' }}>
                        <i class="fas fa-cart-plus me-2"></i> Adicionar ao Carrinho
                    </button>
                </form>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Detalhes do Livro</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th style="width: 150px;">ISBN:</th>
                                <td>{{ $book->isbn }}</td>
                            </tr>
                            <tr>
                                <th>Editora:</th>
                                <td>{{ $book->publisher }}</td>
                            </tr>
                            <tr>
                                <th>Edição:</th>
                                <td>{{ $book->edition }}</td>
                            </tr>
                            <tr>
                                <th>Ano:</th>
                                <td>{{ $book->year }}</td>
                            </tr>
                            <tr>
                                <th>Páginas:</th>
                                <td>{{ $book->pages }}</td>
                            </tr>
                            <tr>
                                <th>Idioma:</th>
                                <td>{{ $book->language }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Descrição</h5>
                </div>
                <div class="card-body">
                    <p>{{ $book->description }}</p>
                </div>
            </div>
        </div>
    </div>
    
    @if(count($relatedBooks) > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Livros Relacionados</h3>
            <div class="row">
                @foreach($relatedBooks as $relatedBook)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <div class="position-relative">
                                @if($relatedBook->cover_image)
                                    <img src="{{ asset('storage/' . $relatedBook->cover_image) }}" class="card-img-top" alt="{{ $relatedBook->title }}" style="height: 200px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('images/book-placeholder.jpg') }}" class="card-img-top" alt="Capa não disponível" style="height: 200px; object-fit: cover;">
                                @endif
                                
                                @if($relatedBook->discount > 0)
                                    <div class="position-absolute top-0 end-0 bg-danger text-white p-2">
                                        -{{ $relatedBook->discount }}%
                                    </div>
                                @endif
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ Str::limit($relatedBook->title, 40) }}</h5>
                                <p class="card-text text-muted">{{ $relatedBook->author }}</p>
                                
                                <div class="mt-auto">
                                    @if($relatedBook->discount > 0)
                                        <p class="mb-1">
                                            <span class="text-decoration-line-through text-muted">R$ {{ number_format($relatedBook->price, 2, ',', '.') }}</span>
                                            <span class="text-danger fw-bold">R$ {{ number_format($relatedBook->price * (1 - $relatedBook->discount/100), 2, ',', '.') }}</span>
                                        </p>
                                    @else
                                        <p class="mb-1 fw-bold">R$ {{ number_format($relatedBook->price, 2, ',', '.') }}</p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('customer.book.details', $relatedBook) }}" class="btn btn-sm btn-outline-primary">Detalhes</a>
                                        
                                        <form action="{{ route('customer.cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $relatedBook->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-sm btn-primary" {{ $relatedBook->stock <= 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
