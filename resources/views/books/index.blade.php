@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Livros</h1>
        <a href="{{ route('books.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Novo Livro
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('books.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar por título ou autor..." name="search" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">Todas as categorias</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Todos os status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativos</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="stock" class="form-select">
                        <option value="">Todos os estoques</option>
                        <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>Estoque baixo (< 10)</option>
                        <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>Sem estoque</option>
                    </select>
                </div>
                <div class="col-md-auto ms-auto">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if($books->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagem</th>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Categoria</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $book)
                                <tr>
                                    <td>{{ $book->id }}</td>
                                    <td>
                                        @if($book->cover_image)
                                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="img-thumbnail" style="max-height: 50px;">
                                        @else
                                            <div class="bg-light text-center p-2" style="width: 40px; height: 50px;">
                                                <i class="bi bi-book"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $book->title }}</td>
                                    <td>{{ $book->author }}</td>
                                    <td>{{ $book->category->name }}</td>
                                    <td>{{ number_format($book->price, 2, ',', '.') }}</td>
                                    <td>
                                        @if($book->stock == 0)
                                            <span class="badge bg-danger">Esgotado</span>
                                        @elseif($book->stock < 5)
                                            <span class="text-danger">{{ $book->stock }}</span>
                                        @elseif($book->stock < 10)
                                            <span class="text-warning">{{ $book->stock }}</span>
                                        @else
                                            <span>{{ $book->stock }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($book->active)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-danger">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('books.edit', $book->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#stockModal{{ $book->id }}">
                                                <i class="bi bi-box-seam"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $book->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal de atualização de estoque -->
                                        <div class="modal fade" id="stockModal{{ $book->id }}" tabindex="-1" aria-labelledby="stockModalLabel{{ $book->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="stockModalLabel{{ $book->id }}">Atualizar Estoque</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('books.update-stock', $book->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="current_stock" class="form-label">Estoque Atual</label>
                                                                <input type="text" class="form-control" id="current_stock" value="{{ $book->stock }}" disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="stock_action" class="form-label">Ação</label>
                                                                <select class="form-select" id="stock_action" name="stock_action">
                                                                    <option value="add">Adicionar ao estoque</option>
                                                                    <option value="subtract">Remover do estoque</option>
                                                                    <option value="set">Definir valor exato</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="stock_quantity" class="form-label">Quantidade</label>
                                                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" value="1" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="stock_notes" class="form-label">Observações</label>
                                                                <textarea class="form-control" id="stock_notes" name="stock_notes" rows="2"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-success">Atualizar Estoque</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal de exclusão -->
                                        <div class="modal fade" id="deleteModal{{ $book->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $book->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $book->id }}">Confirmar Exclusão</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Tem certeza que deseja excluir o livro <strong>{{ $book->title }}</strong>?
                                                        @if($book->invoice_items_count > 0)
                                                            <div class="alert alert-warning mt-3">
                                                                <i class="bi bi-exclamation-triangle"></i> Este livro está associado a {{ $book->invoice_items_count }} fatura(s) e não pode ser excluído.
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        @if($book->invoice_items_count == 0)
                                                            <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Excluir</button>
                                                            </form>
                                                        @else
                                                            <button type="button" class="btn btn-danger" disabled>Excluir</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $books->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Nenhum livro encontrado.
                    @if(request('search') || request('category') || request('status') || request('stock'))
                        <a href="{{ route('books.index') }}" class="alert-link">Limpar filtros</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
