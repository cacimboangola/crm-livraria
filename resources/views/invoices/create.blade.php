@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Nova Fatura</h1>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informações da Fatura</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                    <option value="">Selecione um cliente</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->document }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                    <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Pago</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="invoice_date" class="form-label">Data da Fatura <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" id="invoice_date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="due_date" class="form-label">Data de Vencimento</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="payment_date" class="form-label">Data de Pagamento</label>
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date') }}">
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Observações</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Itens da Fatura</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                            <i class="bi bi-plus-lg"></i> Adicionar Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Livro</th>
                                        <th style="width: 15%;">Quantidade</th>
                                        <th style="width: 20%;">Preço Unit.</th>
                                        <th style="width: 20%;">Total</th>
                                        <th style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="no-items-row">
                                        <td colspan="5" class="text-center">Nenhum item adicionado</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Resumo</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">Kz 0,00</span>
                        </div>
                        
                        <div class="mb-3">
                            <label for="discount" class="form-label">Desconto</label>
                            <div class="input-group">
                                <span class="input-group-text">Kz</span>
                                <input type="number" class="form-control @error('discount') is-invalid @enderror" id="discount" name="discount" value="{{ old('discount', 0) }}" step="0.01" min="0">
                                @error('discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tax" class="form-label">Impostos</label>
                            <div class="input-group">
                                <span class="input-group-text">Kz</span>
                                <input type="number" class="form-control @error('tax') is-invalid @enderror" id="tax" name="tax" value="{{ old('tax', 0) }}" step="0.01" min="0">
                                @error('tax')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total:</h5>
                            <h5 id="total">Kz 0,00</h5>
                        </div>
                        
                        <input type="hidden" name="subtotal" id="subtotal_input" value="0">
                        <input type="hidden" name="total" id="total_amount_input" value="0">
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success" id="saveInvoiceBtn">
                                <i class="bi bi-save"></i> Salvar Fatura
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Template para novos itens -->
    <template id="item-template">
        <tr class="item-row">
            <td>
                <select class="form-select book-select" name="items[INDEX][book_id]" required>
                    <option value="">Selecione um livro</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}" data-price="{{ $book->price }}" data-stock="{{ $book->stock }}">
                            {{ $book->title }} - {{ $book->author }} (Estoque: {{ $book->stock }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" class="form-control quantity-input" name="items[INDEX][quantity]" value="1" min="1" required>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">Kz</span>
                    <input type="number" class="form-control price-input" name="items[INDEX][price]" value="0.00" step="0.01" min="0" required>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">Kz</span>
                    <input type="text" class="form-control item-total" value="0.00" readonly>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    </template>
</div>

@push('scripts')
<script>
    function initializeInvoiceForm() {
        let itemIndex = 0;
        
        // Adicionar item
        document.getElementById('addItemBtn').addEventListener('click', function() {
            addNewItem();
        });
        
        // Remover a linha "nenhum item adicionado" se existir
        function removeNoItemsRow() {
            const noItemsRow = document.querySelector('.no-items-row');
            if (noItemsRow) {
                noItemsRow.remove();
            }
        }
        
        // Adicionar novo item
        function addNewItem() {
            removeNoItemsRow();
            
            const template = document.getElementById('item-template');
            const tbody = document.querySelector('#itemsTable tbody');
            
            // Clone o template
            const clone = template.content.cloneNode(true);
            
            // Atualizar os índices
            const inputs = clone.querySelectorAll('[name*="INDEX"]');
            inputs.forEach(input => {
                input.name = input.name.replace('INDEX', itemIndex);
            });
            
            // Adicionar à tabela
            tbody.appendChild(clone);
            
            // Configurar eventos para a nova linha
            const newRow = tbody.lastElementChild;
            setupRowEvents(newRow);
            
            itemIndex++;
            updateTotals();
        }
        
        // Configurar eventos para uma linha
        function setupRowEvents(row) {
            // Evento de remoção
            row.querySelector('.remove-item').addEventListener('click', function() {
                row.remove();
                updateTotals();
                
                // Se não houver mais itens, mostrar a linha "nenhum item adicionado"
                const itemRows = document.querySelectorAll('.item-row');
                if (itemRows.length === 0) {
                    const tbody = document.querySelector('#itemsTable tbody');
                    tbody.innerHTML = '<tr class="no-items-row"><td colspan="5" class="text-center">Nenhum item adicionado</td></tr>';
                }
            });
            
            // Evento de seleção de livro
            row.querySelector('.book-select').addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                if (option.value) {
                    const price = parseFloat(option.dataset.price);
                    row.querySelector('.price-input').value = price.toFixed(2);
                    updateRowTotal(row);
                }
            });
            
            // Eventos para atualizar totais
            row.querySelector('.quantity-input').addEventListener('input', function() {
                updateRowTotal(row);
            });
            
            row.querySelector('.price-input').addEventListener('input', function() {
                updateRowTotal(row);
            });
        }
        
        // Atualizar o total de uma linha
        function updateRowTotal(row) {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = quantity * price;
            
            row.querySelector('.item-total').value = total.toFixed(2);
            updateTotals();
        }
        
        // Atualizar totais gerais
        function updateTotals() {
            let subtotal = 0;
            
            // Calcular subtotal
            document.querySelectorAll('.item-total').forEach(function(element) {
                subtotal += parseFloat(element.value) || 0;
            });
            
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const tax = parseFloat(document.getElementById('tax').value) || 0;
            const total = subtotal - discount + tax;
            
            // Atualizar exibição
            document.getElementById('subtotal').textContent = 'Kz ' + subtotal.toFixed(2).replace('.', ',');
            document.getElementById('total').textContent = 'Kz ' + total.toFixed(2).replace('.', ',');
            
            // Atualizar inputs ocultos
            document.getElementById('subtotal_input').value = subtotal.toFixed(2);
            document.getElementById('total_amount_input').value = total.toFixed(2);
        }
        
        // Eventos para desconto e imposto
        document.getElementById('discount').addEventListener('input', updateTotals);
        document.getElementById('tax').addEventListener('input', updateTotals);
        
        // Validação do formulário
        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            const bookSelects = document.querySelectorAll('select[name*="book_id"]');
            const hasValidItems = Array.from(bookSelects).some(select => select.value !== '');
            
            if (!hasValidItems) {
                e.preventDefault();
                alert('Adicione pelo menos um item à fatura.');
                return false;
            }
        });
        
        // Adicionar um item inicial
        addNewItem();
    }

    // Executar quando o DOM estiver carregado
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeInvoiceForm);
    } else {
        initializeInvoiceForm();
    }
</script>
@endpush
@endsection
