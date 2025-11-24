@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Detalhes do Cliente</h1>
        <div class="btn-group" role="group">
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}" class="btn btn-success">
                <i class="bi bi-receipt"></i> Nova Fatura
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informações Pessoais</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nome:</strong> {{ $customer->name }}</p>
                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                    <p><strong>Telefone:</strong> {{ $customer->phone }}</p>
                    <p><strong>NIF:</strong> {{ $customer->document }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge {{ $customer->active ? 'bg-success' : 'bg-danger' }}">
                            {{ $customer->active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </p>
                    <p><strong>Data de Cadastro:</strong> {{ $customer->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            
            <!-- Card de Fidelidade -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Programa de Fidelidade</h5>
                    <a href="{{ route('loyalty.dashboard', $customer) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-award"></i> Painel Completo
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($customer->loyaltyPoints))
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="mb-0">{{ $customer->loyaltyPoints->current_balance }} pontos</h3>
                            <span class="badge bg-{{ $customer->loyaltyPoints->level == 'platinum' ? 'primary' : ($customer->loyaltyPoints->level == 'gold' ? 'warning' : ($customer->loyaltyPoints->level == 'silver' ? 'secondary' : 'dark')) }} p-2">
                                {{ ucfirst($customer->loyaltyPoints->level) }}
                            </span>
                        </div>
                        <div class="progress mb-3" style="height: 10px;">
                            @php
                                $percentage = 0;
                                if ($customer->loyaltyPoints->level == 'bronze') {
                                    $percentage = min(($customer->loyaltyPoints->current_balance / 500) * 100, 100);
                                } elseif ($customer->loyaltyPoints->level == 'silver') {
                                    $percentage = min(($customer->loyaltyPoints->current_balance / 2000) * 100, 100);
                                } elseif ($customer->loyaltyPoints->level == 'gold') {
                                    $percentage = min(($customer->loyaltyPoints->current_balance / 5000) * 100, 100);
                                } else {
                                    $percentage = 100;
                                }
                            @endphp
                            <div class="progress-bar bg-{{ $customer->loyaltyPoints->level == 'platinum' ? 'primary' : ($customer->loyaltyPoints->level == 'gold' ? 'warning' : ($customer->loyaltyPoints->level == 'silver' ? 'secondary' : 'dark')) }}" 
                                role="progressbar" 
                                style="width: {{ $percentage }}%" 
                                aria-valuenow="{{ $percentage }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('loyalty.transactions', $customer) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-list-ul"></i> Histórico
                            </a>
                            <a href="{{ route('loyalty.redeem-points.form', $customer) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-gift"></i> Resgatar
                            </a>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Cliente ainda não participa do programa de fidelidade.
                            <div class="mt-2">
                                <a href="{{ route('loyalty.dashboard', $customer) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle"></i> Cadastrar no Programa
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Endereço</h5>
                </div>
                <div class="card-body">
                    <p><strong>Endereço:</strong> {{ $customer->address }}</p>
                    <p><strong>Complemento:</strong> {{ $customer->address_complement ?? 'N/A' }}</p>
                    <p><strong>Bairro:</strong> {{ $customer->district }}</p>
                    <p><strong>Cidade:</strong> {{ $customer->city }}</p>
                    <p><strong>Estado:</strong> {{ $customer->state }}</p>
                    <p><strong>Código Postal:</strong> {{ $customer->zip_code }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Histórico de Faturas</h5>
            <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-success">
                <i class="bi bi-plus-circle"></i> Nova Fatura
            </a>
        </div>
        <div class="card-body">
            @if($customer->invoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Data</th>
                                <th>Valor Total</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->number }}</td>
                                    <td>{{ $invoice->date->format('d/m/Y') }}</td>
                                    <td>R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $invoice->status == 'paid' ? 'bg-success' : ($invoice->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $invoice->status == 'paid' ? 'Paga' : ($invoice->status == 'pending' ? 'Pendente' : 'Cancelada') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('invoices.pdf', $invoice->id) }}" class="btn btn-sm btn-outline-danger" target="_blank">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Este cliente ainda não possui faturas.
                </div>
            @endif
        </div>
    </div>

    <!-- Seção de Recomendações -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recomendações Personalizadas</h5>
            <a href="{{ route('recommendations.customer', $customer->id) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-lightbulb"></i> Ver Todas as Recomendações
            </a>
        </div>
        <div class="card-body">
            <p class="text-muted">Baseado no histórico de compras e preferências do cliente</p>
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status" id="recommendations-loading">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>
            <div class="row" id="recommendations-container" style="display: none;"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Carregar recomendações via AJAX
        fetch('{{ route("recommendations.customer", $customer->id) }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('recommendations-container');
            const loading = document.getElementById('recommendations-loading');
            
            loading.style.display = 'none';
            container.style.display = 'flex';
            
            if (data.recommendations && data.recommendations.length > 0) {
                // Mostrar apenas 4 recomendações na página de detalhes
                const books = data.recommendations.slice(0, 4);
                
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
                                <a href="/invoices/create?customer_id=${{{ $customer->id }}}&book_id=${book.id}" class="btn btn-sm btn-success">
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
                            <i class="bi bi-info-circle"></i> Não há recomendações disponíveis para este cliente no momento.
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar recomendações:', error);
            const container = document.getElementById('recommendations-container');
            const loading = document.getElementById('recommendations-loading');
            
            loading.style.display = 'none';
            container.style.display = 'block';
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Erro ao carregar recomendações. Tente novamente mais tarde.
                    </div>
                </div>
            `;
        });
    });
</script>
@endpush
