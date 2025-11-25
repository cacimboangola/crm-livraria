@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Novo Pedido Especial</h1>
        <a href="{{ route('special-orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <i class="bi bi-book"></i> Dados do Pedido Especial
                </div>
                <div class="card-body">
                    <form action="{{ route('special-orders.store') }}" method="POST">
                        @csrf

                        {{-- Cliente --}}
                        <div class="mb-4">
                            <label for="customer_id" class="form-label">Cliente *</label>
                            <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                <option value="">Selecione o cliente...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                                       id="book_title" name="book_title" value="{{ old('book_title') }}" 
                                       placeholder="Ex: Dom Quixote" required>
                                @error('book_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="quantity" class="form-label">Quantidade *</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" value="{{ old('quantity', 1) }}" 
                                       min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="book_author" class="form-label">Autor</label>
                                <input type="text" class="form-control @error('book_author') is-invalid @enderror" 
                                       id="book_author" name="book_author" value="{{ old('book_author') }}" 
                                       placeholder="Ex: Miguel de Cervantes">
                                @error('book_author')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="book_publisher" class="form-label">Editora</label>
                                <input type="text" class="form-control @error('book_publisher') is-invalid @enderror" 
                                       id="book_publisher" name="book_publisher" value="{{ old('book_publisher') }}" 
                                       placeholder="Ex: Companhia das Letras">
                                @error('book_publisher')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="book_isbn" class="form-label">ISBN</label>
                                <input type="text" class="form-control @error('book_isbn') is-invalid @enderror" 
                                       id="book_isbn" name="book_isbn" value="{{ old('book_isbn') }}" 
                                       placeholder="Ex: 978-85-359-0277-8">
                                @error('book_isbn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="estimated_price" class="form-label">Preço Estimado (Kz)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Kz</span>
                                    <input type="number" step="0.01" class="form-control @error('estimated_price') is-invalid @enderror" 
                                           id="estimated_price" name="estimated_price" value="{{ old('estimated_price') }}" 
                                           placeholder="0,00">
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
                                       id="pickup" value="pickup" {{ old('delivery_preference', 'pickup') == 'pickup' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pickup">
                                    <i class="bi bi-shop"></i> Retirada na Loja
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="delivery_preference" 
                                       id="delivery" value="delivery" {{ old('delivery_preference') == 'delivery' ? 'checked' : '' }}>
                                <label class="form-check-label" for="delivery">
                                    <i class="bi bi-truck"></i> Entrega em Domicílio
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="customer_notes" class="form-label">Observações do Cliente</label>
                            <textarea class="form-control @error('customer_notes') is-invalid @enderror" 
                                      id="customer_notes" name="customer_notes" rows="3" 
                                      placeholder="Informações adicionais sobre o pedido...">{{ old('customer_notes') }}</textarea>
                            @error('customer_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('special-orders.index') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Criar Pedido Especial
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-header bg-white">
                    <i class="bi bi-info-circle"></i> Informações
                </div>
                <div class="card-body">
                    <h6>Como funciona?</h6>
                    <ol class="small text-muted">
                        <li class="mb-2">Preencha os dados do livro solicitado pelo cliente</li>
                        <li class="mb-2">O pedido será criado com status "Aguardando Encomenda"</li>
                        <li class="mb-2">Faça a encomenda ao fornecedor e atualize o status</li>
                        <li class="mb-2">Quando o livro chegar, marque como "Recebido"</li>
                        <li class="mb-2">O cliente será notificado automaticamente por email</li>
                        <li>Finalize marcando como "Entregue" após a retirada/entrega</li>
                    </ol>

                    <hr>

                    <h6>Status do Pedido</h6>
                    <div class="small">
                        <span class="badge bg-warning text-dark mb-1">Aguardando Encomenda</span>
                        <span class="text-muted">→</span>
                        <span class="badge bg-info mb-1">Encomendado</span>
                        <span class="text-muted">→</span>
                        <span class="badge bg-primary mb-1">Recebido</span>
                        <span class="text-muted">→</span>
                        <span class="badge bg-secondary mb-1">Notificado</span>
                        <span class="text-muted">→</span>
                        <span class="badge bg-success mb-1">Entregue</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
