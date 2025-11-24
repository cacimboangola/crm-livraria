@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Detalhes do Livro</h1>
        <div class="btn-group" role="group">
            <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('books.edit', $book->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('invoices.create', ['book_id' => $book->id]) }}" class="btn btn-success">
                <i class="bi bi-cart-plus"></i> Vender
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informações Básicas</h5>
                </div>
                <div class="card-body">
                    <h4>{{ $book->title }}</h4>
                    <h6 class="text-muted">{{ $book->author }}</h6>
                    <p>
                        <span class="badge bg-primary">{{ $book->category->name }}</span>
                        <span class="badge bg-secondary">{{ $book->publisher }}</span>
                    </p>
                    <p><strong>ISBN:</strong> {{ $book->isbn }}</p>
                    <p><strong>Ano:</strong> {{ $book->year }}</p>
                    <p><strong>Edição:</strong> {{ $book->edition }}</p>
                    <p><strong>Preço:</strong> R$ {{ number_format($book->price, 2, ',', '.') }}</p>
                    <p>
                        <span class="badge bg-success">Estoque: {{ $book->stock }}</span>
                        <span class="badge {{ $book->active ? 'bg-success' : 'bg-danger' }}">
                            {{ $book->active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Ações Estratégicas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('recommendations.similar', $book->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-shuffle"></i> Ver Livros Similares
                        </a>
                        <a href="{{ route('recommendations.potential-customers', $book->id) }}" class="btn btn-outline-success">
                            <i class="bi bi-people"></i> Clientes Potenciais
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Descrição</h5>
                </div>
                <div class="card-body">
                    <p>{{ $book->description }}</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Histórico de Vendas</h5>
                </div>
                <div class="card-body">
                    @if($book->invoiceItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fatura</th>
                                        <th>Cliente</th>
                                        <th>Data</th>
                                        <th>Quantidade</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($book->invoiceItems as $item)
                                        <tr>
                                            <td>
                                                <a href="{{ route('invoices.show', $item->invoice->id) }}">
                                                    {{ $item->invoice->number }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('customers.show', $item->invoice->customer->id) }}">
                                                    {{ $item->invoice->customer->name }}
                                                </a>
                                            </td>
                                            <td>{{ $item->invoice->date->format('d/m/Y') }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Este livro ainda não foi vendido.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Livros Similares -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Livros Similares</h5>
            <a href="{{ route('recommendations.similar', $book->id) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-shuffle"></i> Ver Todos os Similares
            </a>
        </div>
        <div class="card-body">
            <p class="text-muted">Livros da mesma categoria ou autor que podem interessar aos clientes</p>
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status" id="similar-books-loading">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>
            <div class="row" id="similar-books-container" style="display: none;"></div>
        </div>
    </div>

    <!-- Seção de Clientes Potenciais -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Clientes Potenciais</h5>
            <a href="{{ route('recommendations.potential-customers', $book->id) }}" class="btn btn-sm btn-outline-success">
                <i class="bi bi-people"></i> Ver Todos os Clientes Potenciais
            </a>
        </div>
        <div class="card-body">
            <p class="text-muted">Clientes que podem ter interesse neste livro com base em compras anteriores</p>
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status" id="potential-customers-loading">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>
            <div class="row" id="potential-customers-container" style="display: none;"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Carregar livros similares via AJAX
        fetch('{{ route("recommendations.similar", $book->id) }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('similar-books-container');
            const loading = document.getElementById('similar-books-loading');
            
            loading.style.display = 'none';
            container.style.display = 'flex';
            
            if (data.similar_books && data.similar_books.length > 0) {
                // Mostrar apenas 4 livros similares na página de detalhes
                const books = data.similar_books.slice(0, 4);
                
                books.forEach(book => {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 mb-3';
                    
                    col.innerHTML = `
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title">${book.title}</h6>
                                <p class="card-text small text-muted">${book.author}</p>
                                <p class="card-text fw-bold">R$ ${parseFloat(book.price).toFixed(2).replace('.', ',')}</p>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <a href="/books/${book.id}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/invoices/create?book_id=${book.id}" class="btn btn-sm btn-success">
                                    <i class="bi bi-cart-plus"></i>
                                </a>
                            </div>
                        </div>
                    `;
                    
                    container.appendChild(col);
                });
            } else {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Não há livros similares disponíveis no momento.
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar livros similares:', error);
            const container = document.getElementById('similar-books-container');
            const loading = document.getElementById('similar-books-loading');
            
            loading.style.display = 'none';
            container.style.display = 'block';
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Erro ao carregar livros similares. Tente novamente mais tarde.
                    </div>
                </div>
            `;
        });

        // Carregar clientes potenciais via AJAX
        fetch('{{ route("recommendations.potential-customers", $book->id) }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('potential-customers-container');
            const loading = document.getElementById('potential-customers-loading');
            
            loading.style.display = 'none';
            container.style.display = 'block';
            
            if (data.potential_customers && data.potential_customers.length > 0) {
                // Mostrar apenas 5 clientes potenciais na página de detalhes
                const customers = data.potential_customers.slice(0, 5);
                
                const table = document.createElement('div');
                table.className = 'col-12';
                table.innerHTML = `
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="potential-customers-tbody">
                            </tbody>
                        </table>
                    </div>
                `;
                
                container.appendChild(table);
                const tbody = document.getElementById('potential-customers-tbody');
                
                customers.forEach(customer => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${customer.name}</td>
                        <td>${customer.email}</td>
                        <td>${customer.phone}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="/customers/${customer.id}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/invoices/create?customer_id=${customer.id}&book_id=${{{ $book->id }}}" class="btn btn-sm btn-success">
                                    <i class="bi bi-cart-plus"></i>
                                </a>
                            </div>
                        </td>
                    `;
                    
                    tbody.appendChild(tr);
                });
            } else {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Não há clientes potenciais identificados para este livro no momento.
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar clientes potenciais:', error);
            const container = document.getElementById('potential-customers-container');
            const loading = document.getElementById('potential-customers-loading');
            
            loading.style.display = 'none';
            container.style.display = 'block';
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Erro ao carregar clientes potenciais. Tente novamente mais tarde.
                    </div>
                </div>
            `;
        });
    });
</script>
@endpush
