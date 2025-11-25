@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Editar Pedido Especial #{{ $specialOrder->id }}</h1>
        <a href="{{ route('special-orders.show', $specialOrder) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-pencil"></i> Editar Dados do Pedido</span>
                    <span class="badge {{ $specialOrder->status_badge_class }}">
                        {{ $specialOrder->status_formatted }}
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('special-orders.update', $specialOrder) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Cliente --}}
                        <div class="mb-4">
                            <label for="customer_id" class="form-label">Cliente *</label>
                            <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                <option value="">Selecione o cliente...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $specialOrder->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} - {{ $customer->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3"><i class="bi bi-book"></i> Informações do Livro</h5>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="book_title" class="form-label">Título do Livro *</label>
                                <input type="text" class="form-control @error('book_title') is-invalid @enderror" 
                                       id="book_title" name="book_title" 
                                       value="{{ old('book_title', $specialOrder->book_title) }}" required>
                                @error('book_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="quantity" class="form-label">Quantidade *</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" 
                                       value="{{ old('quantity', $specialOrder->quantity) }}" min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="book_author" class="form-label">Autor</label>
                                <input type="text" class="form-control @error('book_author') is-invalid @enderror" 
                                       id="book_author" name="book_author" 
                                       value="{{ old('book_author', $specialOrder->book_author) }}">
                                @error('book_author')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="book_publisher" class="form-label">Editora</label>
                                <input type="text" class="form-control @error('book_publisher') is-invalid @enderror" 
                                       id="book_publisher" name="book_publisher" 
                                       value="{{ old('book_publisher', $specialOrder->book_publisher) }}">
                                @error('book_publisher')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="book_isbn" class="form-label">ISBN</label>
                                <input type="text" class="form-control @error('book_isbn') is-invalid @enderror" 
                                       id="book_isbn" name="book_isbn" 
                                       value="{{ old('book_isbn', $specialOrder->book_isbn) }}">
                                @error('book_isbn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="estimated_price" class="form-label">Preço Estimado (Kz)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Kz</span>
                                    <input type="number" step="0.01" class="form-control @error('estimated_price') is-invalid @enderror" 
                                           id="estimated_price" name="estimated_price" 
                                           value="{{ old('estimated_price', $specialOrder->estimated_price) }}">
                                </div>
                                @error('estimated_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3"><i class="bi bi-truck"></i> Preferência de Entrega</h5>

                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="delivery_preference" 
                                       id="pickup" value="pickup" 
                                       {{ old('delivery_preference', $specialOrder->delivery_preference) == 'pickup' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pickup">
                                    <i class="bi bi-shop"></i> Retirada na Loja
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="delivery_preference" 
                                       id="delivery" value="delivery" 
                                       {{ old('delivery_preference', $specialOrder->delivery_preference) == 'delivery' ? 'checked' : '' }}>
                                <label class="form-check-label" for="delivery">
                                    <i class="bi bi-truck"></i> Entrega em Domicílio
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="customer_notes" class="form-label">Observações do Cliente</label>
                            <textarea class="form-control @error('customer_notes') is-invalid @enderror" 
                                      id="customer_notes" name="customer_notes" rows="3">{{ old('customer_notes', $specialOrder->customer_notes) }}</textarea>
                            @error('customer_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3"><i class="bi bi-sticky"></i> Notas Internas</h5>

                        <div class="mb-3">
                            <label for="supplier_notes" class="form-label">Notas sobre Fornecedor/Encomenda</label>
                            <textarea class="form-control @error('supplier_notes') is-invalid @enderror" 
                                      id="supplier_notes" name="supplier_notes" rows="3"
                                      placeholder="Informações sobre o fornecedor, prazo de entrega, etc.">{{ old('supplier_notes', $specialOrder->supplier_notes) }}</textarea>
                            @error('supplier_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <div>
                                @if($specialOrder->canBeCancelled())
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="if(confirm('Tem certeza que deseja excluir este pedido?')) document.getElementById('delete-form').submit();">
                                        <i class="bi bi-trash"></i> Excluir Pedido
                                    </button>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('special-orders.show', $specialOrder) }}" class="btn btn-outline-secondary">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($specialOrder->canBeCancelled())
                        <form id="delete-form" action="{{ route('special-orders.destroy', $specialOrder) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-header bg-white">
                    <i class="bi bi-info-circle"></i> Informações do Pedido
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Status Atual</dt>
                        <dd>
                            <span class="badge {{ $specialOrder->status_badge_class }}">
                                {{ $specialOrder->status_formatted }}
                            </span>
                        </dd>

                        <dt>Criado em</dt>
                        <dd>{{ $specialOrder->created_at->format('d/m/Y H:i') }}</dd>

                        <dt>Criado por</dt>
                        <dd>{{ $specialOrder->user->name }}</dd>

                        @if($specialOrder->ordered_at)
                            <dt>Encomendado em</dt>
                            <dd>{{ $specialOrder->ordered_at->format('d/m/Y H:i') }}</dd>
                        @endif

                        @if($specialOrder->received_at)
                            <dt>Recebido em</dt>
                            <dd>{{ $specialOrder->received_at->format('d/m/Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
