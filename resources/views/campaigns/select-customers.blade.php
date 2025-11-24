@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Selecionar Clientes para Campanha') }}: {{ $campaign->name }}</span>
                    <div>
                        <a href="{{ route('campaigns.show', $campaign->id) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Voltar para Campanha') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('campaigns.add-customers', $campaign->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5>{{ __('Clientes Disponíveis') }}</h5>
                                <div class="input-group" style="max-width: 300px;">
                                    <input type="text" id="customerSearch" class="form-control form-control-sm" placeholder="{{ __('Buscar cliente...') }}">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" id="clearSearch">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped" id="customersTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                                </div>
                                            </th>
                                            <th>{{ __('Nome') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Telefone') }}</th>
                                            <th>{{ __('Compras') }}</th>
                                            <th>{{ __('Valor Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $campaignCustomerIds = $campaign->customers->pluck('id')->toArray();
                                        @endphp
                                        
                                        @forelse ($customers as $customer)
                                            @if (!in_array($customer->id, $campaignCustomerIds))
                                                <tr class="customer-row">
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input customer-checkbox" type="checkbox" name="customer_ids[]" value="{{ $customer->id }}">
                                                        </div>
                                                    </td>
                                                    <td>{{ $customer->name }}</td>
                                                    <td>{{ $customer->email }}</td>
                                                    <td>{{ $customer->phone }}</td>
                                                    <td>{{ $customer->invoices_count ?? $customer->invoices->count() }}</td>
                                                    <td>R$ {{ number_format($customer->total_spent ?? $customer->invoices->sum('total'), 2, ',', '.') }}</td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">{{ __('Nenhum cliente disponível.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span id="selectedCount" class="text-muted">0 {{ __('clientes selecionados') }}</span>
                                    <button type="submit" class="btn btn-primary" id="addCustomersBtn" disabled>
                                        <i class="fas fa-user-plus"></i> {{ __('Adicionar Clientes Selecionados') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const customerCheckboxes = document.querySelectorAll('.customer-checkbox');
        const selectedCountElement = document.getElementById('selectedCount');
        const addCustomersBtn = document.getElementById('addCustomersBtn');
        const customerSearch = document.getElementById('customerSearch');
        const clearSearch = document.getElementById('clearSearch');
        const customerRows = document.querySelectorAll('.customer-row');
        
        // Função para atualizar a contagem de clientes selecionados
        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('.customer-checkbox:checked').length;
            selectedCountElement.textContent = selectedCount + ' {{ __('clientes selecionados') }}';
            addCustomersBtn.disabled = selectedCount === 0;
        }
        
        // Event listener para o checkbox "Selecionar Todos"
        selectAllCheckbox.addEventListener('change', function() {
            customerCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                if (row.style.display !== 'none') {
                    checkbox.checked = selectAllCheckbox.checked;
                }
            });
            updateSelectedCount();
        });
        
        // Event listeners para os checkboxes individuais
        customerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedCount();
                
                // Verificar se todos os checkboxes visíveis estão marcados
                const visibleCheckboxes = Array.from(customerCheckboxes).filter(cb => {
                    return cb.closest('tr').style.display !== 'none';
                });
                
                const allChecked = visibleCheckboxes.every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked && visibleCheckboxes.length > 0;
            });
        });
        
        // Função de busca
        customerSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            customerRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                    // Desmarcar checkboxes de linhas ocultas
                    const checkbox = row.querySelector('.customer-checkbox');
                    if (checkbox.checked) {
                        checkbox.checked = false;
                        updateSelectedCount();
                    }
                }
            });
            
            // Atualizar o estado do checkbox "Selecionar Todos"
            const visibleRows = Array.from(customerRows).filter(row => row.style.display !== 'none');
            const visibleCheckboxes = visibleRows.map(row => row.querySelector('.customer-checkbox'));
            const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
        });
        
        // Limpar busca
        clearSearch.addEventListener('click', function() {
            customerSearch.value = '';
            customerRows.forEach(row => {
                row.style.display = '';
            });
            
            // Atualizar o estado do checkbox "Selecionar Todos"
            const allChecked = Array.from(customerCheckboxes).every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked && customerCheckboxes.length > 0;
        });
        
        // Inicializar contagem
        updateSelectedCount();
    });
</script>
@endpush
@endsection
