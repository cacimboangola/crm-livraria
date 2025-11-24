@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Faturas</h1>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nova Fatura
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('invoices.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar por número ou cliente..." name="search" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Todos os status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pago</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text">De</span>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text">Até</span>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="col-md-auto ms-auto mt-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if($invoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Vencimento</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->id }}</td>
                                    <td>{{ $invoice->customer->name }}</td>
                                    <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($invoice->due_date)
                                            {{ $invoice->due_date->format('d/m/Y') }}
                                            @if($invoice->status == 'pending' && $invoice->due_date < now())
                                                <span class="badge bg-danger">Atrasada</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>Kz {{ number_format($invoice->total, 2, ',', '.') }}</td>
                                    <td>
                                        @if($invoice->status == 'paid')
                                            <span class="badge bg-success">Pago</span>
                                        @elseif($invoice->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pendente</span>
                                        @elseif($invoice->status == 'draft')
                                            <span class="badge bg-secondary">Rascunho</span>
                                        @else
                                            <span class="badge bg-danger">Cancelado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($invoice->status != 'cancelled')
                                                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.open('{{ route('invoices.print', $invoice->id) }}', '_blank')">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#statusModal{{ $invoice->id }}">
                                                <i class="bi bi-gear"></i>
                                            </button>
                                        </div>

                                        <!-- Modal de alteração de status -->
                                        <div class="modal fade" id="statusModal{{ $invoice->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $invoice->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="statusModalLabel{{ $invoice->id }}">Alterar Status da Fatura #{{ $invoice->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Cliente: <strong>{{ $invoice->customer->name }}</strong></p>
                                                        <p>Total: <strong>Kz {{ number_format($invoice->total_amount, 2, ',', '.') }}</strong></p>
                                                        <p>Status atual: 
                                                            @if($invoice->status == 'paid')
                                                                <span class="badge bg-success">Pago</span>
                                                            @elseif($invoice->status == 'pending')
                                                                <span class="badge bg-warning text-dark">Pendente</span>
                                                            @elseif($invoice->status == 'draft')
                                                                <span class="badge bg-secondary">Rascunho</span>
                                                            @else
                                                                <span class="badge bg-danger">Cancelado</span>
                                                            @endif
                                                        </p>
                                                        
                                                        <form action="{{ route('invoices.update-status', $invoice->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="mb-3">
                                                                <label for="status" class="form-label">Novo Status</label>
                                                                <select class="form-select" id="status" name="status" required>
                                                                    <option value="draft" {{ $invoice->status == 'draft' ? 'selected' : '' }}>Rascunho</option>
                                                                    <option value="pending" {{ $invoice->status == 'pending' ? 'selected' : '' }}>Pendente</option>
                                                                    <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Pago</option>
                                                                    <option value="cancelled" {{ $invoice->status == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="payment_date" class="form-label">Data de Pagamento</label>
                                                                <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ $invoice->payment_date ? $invoice->payment_date->format('Y-m-d') : '' }}">
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="notes" class="form-label">Observações</label>
                                                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ $invoice->notes }}</textarea>
                                                            </div>
                                                            
                                                            <div class="d-grid gap-2">
                                                                <button type="submit" class="btn btn-primary">Atualizar Status</button>
                                                            </div>
                                                        </form>
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
                    {{ $invoices->appends(request()->query())->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Nenhuma fatura encontrada.
                    @if(request('search') || request('status') || request('date_from') || request('date_to'))
                        <a href="{{ route('invoices.index') }}" class="alert-link">Limpar filtros</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
