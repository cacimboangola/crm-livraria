@extends('layouts.customer')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold mb-3">Catálogo de Livros</h1>
                <p class="lead mb-4">Descubra mundos incríveis através da leitura. Encontre os melhores títulos com os melhores preços.</p>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-book me-1"></i>
                        {{ $books->total() ?? count($books) }} livros disponíveis
                    </span>
                    <span class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-shipping-fast me-1"></i>
                        Entrega rápida
                    </span>
                    <span class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-star me-1"></i>
                        Qualidade garantida
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <form action="{{ route('customer.catalog') }}" method="GET" class="mt-3">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control border-start-0 shadow-sm" 
                            placeholder="Buscar por título, autor..." 
                            value="{{ request('search') }}"
                        >
                        <button class="btn btn-light shadow-sm" type="submit">
                            Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">

    <div class="row">
        <div class="col-md-3 mb-4">
            <!-- Categories Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-tags text-primary me-2"></i>
                        Categorias
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('customer.catalog') }}" 
                           class="list-group-item list-group-item-action border-0 rounded-3 mb-2 {{ !request('category_id') ? 'active bg-primary text-white' : 'text-dark' }}">
                            <i class="fas fa-th-large me-2"></i>
                            Todas as Categorias
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('customer.catalog', ['category_id' => $category->id]) }}" 
                               class="list-group-item list-group-item-action border-0 rounded-3 mb-2 {{ request('category_id') == $category->id ? 'active bg-primary text-white' : 'text-dark' }}">
                                <i class="fas fa-bookmark me-2"></i>
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Filters Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-filter text-primary me-2"></i>
                        Filtros
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <form action="{{ route('customer.catalog') }}" method="GET">
                        @if(request('category_id'))
                            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                        @endif
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark">
                                <i class="fas fa-sort me-1"></i>
                                Ordenar por:
                            </label>
                            <select name="sort" class="form-select border-0 bg-light" onchange="this.form.submit()">
                                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>📚 Título (A-Z)</option>
                                <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>📚 Título (Z-A)</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>💰 Preço (Menor-Maior)</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>💰 Preço (Maior-Menor)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>⭐ Mais Recentes</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            @if($books->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-muted mb-3">Nenhum livro encontrado</h3>
                    <p class="text-muted mb-4">Não encontramos livros com os critérios especificados. Tente outros termos ou categorias.</p>
                    <a href="{{ route('customer.catalog') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>
                        Ver todos os livros
                    </a>
                </div>
            @else
                <div class="row g-4">
                    @foreach($books as $book)
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 border-0 shadow-sm book-card">
                                <div class="position-relative overflow-hidden">
                                    @if($book->cover_image)
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                             class="card-img-top book-cover" 
                                             alt="{{ $book->title }}" 
                                             style="height: 280px; object-fit: cover; transition: transform 0.3s ease;">
                                    @else
                                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                             style="height: 280px;">
                                            <div class="text-center text-muted">
                                                <i class="fas fa-book fa-3x mb-2"></i>
                                                <p class="small mb-0">Capa não disponível</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($book->discount > 0)
                                        <div class="position-absolute top-0 end-0">
                                            <span class="badge bg-danger rounded-0 rounded-start-3 px-3 py-2">
                                                <i class="fas fa-percentage me-1"></i>
                                                -{{ $book->discount }}%
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <!-- Quick View Overlay -->
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center book-overlay">
                                        <a href="{{ route('customer.book.details', $book) }}" 
                                           class="btn btn-light rounded-pill px-4 shadow">
                                            <i class="fas fa-eye me-2"></i>
                                            Ver Detalhes
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column p-4">
                                    <div class="mb-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 small">
                                            {{ $book->category->name ?? 'Sem categoria' }}
                                        </span>
                                    </div>
                                    
                                    <h5 class="card-title fw-bold mb-2" title="{{ $book->title }}">
                                        {{ Str::limit($book->title, 45) }}
                                    </h5>
                                    
                                    <p class="card-text text-muted mb-3">
                                        <i class="fas fa-user-edit me-1"></i>
                                        {{ $book->author }}
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            @if($book->discount > 0)
                                                <div>
                                                    <span class="text-decoration-line-through text-muted small">
                                                        Kz {{ number_format($book->price, 2, ',', '.') }}
                                                    </span>
                                                    <div class="text-primary fw-bold h5 mb-0">
                                                        Kz {{ number_format($book->price * (1 - $book->discount/100), 2, ',', '.') }}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-primary fw-bold h5 mb-0">
                                                    Kz {{ number_format($book->price, 2, ',', '.') }}
                                                </div>
                                            @endif
                                            
                                            <div class="text-end">
                                                <small class="text-muted">
                                                    <i class="fas fa-boxes me-1"></i>
                                                    {{ $book->stock }} em estoque
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <form action="{{ route('customer.cart.add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                                    <i class="fas fa-cart-plus me-2"></i>
                                                    Adicionar ao Carrinho
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if(method_exists($books, 'links'))
                    <div class="d-flex justify-content-center mt-5">
                        <nav aria-label="Navegação de páginas">
                            {{ $books->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </nav>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Modern Catalog Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
}

.book-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}

.book-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
}

.book-cover {
    transition: transform 0.3s ease;
}

.book-card:hover .book-cover {
    transform: scale(1.05);
}

.book-overlay {
    background: rgba(0, 0, 0, 0.7);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.book-card:hover .book-overlay {
    opacity: 1;
}

.list-group-item {
    transition: all 0.2s ease;
}

.list-group-item:hover:not(.active) {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    transform: translateX(5px);
}

.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.input-group:focus-within .input-group-text {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.05);
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.card {
    border-radius: 12px;
}

.badge {
    border-radius: 8px;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .display-4 {
        font-size: 2.5rem;
    }
    
    .book-card:hover {
        transform: none;
    }
    
    .book-overlay {
        opacity: 1;
        background: rgba(0, 0, 0, 0.5);
    }
    
    .list-group-item:hover:not(.active) {
        transform: none;
    }
}

/* Loading animation for images */
.book-cover {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

.book-cover[src] {
    animation: none;
    background: none;
}
</style>
@endpush
