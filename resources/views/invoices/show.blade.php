@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Fatura #{{ $invoice->id }}</h1>
        <div class="btn-group" role="group">
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            @if($invoice->status != 'cancelled')
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            @endif
            <button type="button" class="btn btn-outline-secondary" onclick="window.open('{{ route('invoices.print', $invoice->id) }}', '_blank')">
                <i class="bi bi-printer"></i> Imprimir
            </button>
            <button type="button" class="btn btn-outline-success" onclick="window.open('{{ route('invoices.pdf', $invoice->id) }}', '_blank')">
                <i class="bi bi-file-pdf"></i> PDF
            </button>
            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#emailModal">
                <i class="bi bi-envelope"></i> Enviar por Email
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detalhes da Fatura</h5>
                    <span class="badge {{ 
                        $invoice->status == 'paid' ? 'bg-success' : 
                        ($invoice->status == 'pending' ? 'bg-warning text-dark' : 
                        ($invoice->status == 'draft' ? 'bg-secondary' : 'bg-danger')) 
                    }} fs-6">
                        {{ 
                            $invoice->status == 'paid' ? 'Pago' : 
                            ($invoice->status == 'pending' ? 'Pendente' : 
                            ($invoice->status == 'draft' ? 'Rascunho' : 'Cancelado')) 
                        }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Informações do Cliente</h6>
                            <p class="mb-1"><strong>Nome:</strong> {{ $invoice->customer->name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $invoice->customer->email }}</p>
                            <p class="mb-1"><strong>Telefone:</strong> {{ $invoice->customer->phone }}</p>
                            <p class="mb-1"><strong>Documento:</strong> {{ $invoice->customer->document }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Informações da Fatura</h6>
                            <p class="mb-1"><strong>Número:</strong> {{ $invoice->id }}</p>
                            <p class="mb-1"><strong>Data:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</p>
                            <p class="mb-1"><strong>Vencimento:</strong> {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}</p>
                            <p class="mb-1"><strong>Pagamento:</strong> {{ $invoice->payment_date ? $invoice->payment_date->format('d/m/Y') : '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Livro</th>
                                    <th>Autor</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Preço Unit.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                    <tr>
                                        <td>{{ $item->book->title }}</td>
                                        <td>{{ $item->book->author }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">Kz {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                        <td class="text-end">Kz {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end">Kz {{ number_format($invoice->subtotal, 2, ',', '.') }}</td>
                                </tr>
                                @if($invoice->discount > 0)
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Desconto:</strong></td>
                                        <td class="text-end">Kz {{ number_format($invoice->discount, 2, ',', '.') }}</td>
                                    </tr>
                                @endif
                                @if($invoice->tax > 0)
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Impostos:</strong></td>
                                        <td class="text-end">Kz {{ number_format($invoice->tax, 2, ',', '.') }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong>Kz {{ number_format($invoice->total, 2, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    @if($invoice->notes)
                        <div class="mt-3">
                            <h6>Observações</h6>
                            <p class="border p-2 rounded bg-light">{{ $invoice->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Ações</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoices.update-status', $invoice->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="status" class="form-label">Status da Fatura</label>
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
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ $invoice->notes }}</textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Atualizar Status
                            </button>
                        </div>
                        
                        @if($invoice->status != 'paid')
                        <div class="alert alert-info mt-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-award me-2 fs-4"></i>
                                <div>
                                    <strong>Programa de Fidelidade</strong><br>
                                    Ao marcar esta fatura como paga, o cliente receberá automaticamente pontos de fidelidade!
                                </div>
                            </div>
                        </div>
                        @elseif($invoice->status == 'paid')
                        <div class="alert alert-success mt-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-award me-2 fs-4"></i>
                                <div>
                                    <strong>Pontos de Fidelidade Adicionados!</strong><br>
                                    @php
                                        $points = floor($invoice->total / 10);
                                        if ($points < 1) $points = 1;
                                    @endphp
                                    O cliente recebeu <strong>{{ $points }} pontos</strong> no programa de fidelidade por esta compra.
                                    <div class="mt-2">
                                        <a href="{{ route('loyalty.dashboard', $invoice->customer_id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-eye"></i> Ver Programa de Fidelidade
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Histórico</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Criação da Fatura</div>
                                {{ $invoice->created_at->format('d/m/Y H:i') }}
                            </div>
                            <span class="badge bg-secondary rounded-pill">
                                <i class="bi bi-plus-circle"></i>
                            </span>
                        </li>
                        @if($invoice->status != 'draft')
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Emissão da Fatura</div>
                                    {{ $invoice->invoice_date->format('d/m/Y') }}
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    <i class="bi bi-file-earmark-text"></i>
                                </span>
                            </li>
                        @endif
                        @if($invoice->status == 'paid' && $invoice->payment_date)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Pagamento Recebido</div>
                                    {{ $invoice->payment_date->format('d/m/Y') }}
                                </div>
                                <span class="badge bg-success rounded-pill">
                                    <i class="bi bi-cash"></i>
                                </span>
                            </li>
                        @endif
                        @if($invoice->status == 'cancelled')
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Fatura Cancelada</div>
                                    {{ $invoice->updated_at->format('d/m/Y H:i') }}
                                </div>
                                <span class="badge bg-danger rounded-pill">
                                    <i class="bi bi-x-circle"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de envio de email -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Enviar Fatura por Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('invoices.send-email', $invoice->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email do Destinatário</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $invoice->customer->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">O PDF da fatura será anexado automaticamente ao email.</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Um email será enviado para o destinatário contendo:
                        <ul class="mb-0">
                            <li>Detalhes da fatura #{{ $invoice->id }}</li>
                            <li>Lista de itens comprados</li>
                            <li>Valores e status de pagamento</li>
                            <li>PDF da fatura anexado</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Email</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
