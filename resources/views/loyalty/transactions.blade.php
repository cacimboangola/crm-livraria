@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Histórico de Transações - {{ $customer->name }}</span>
                    <div>
                        <a href="{{ route('loyalty.dashboard', $customer) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar ao Painel de Fidelidade
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Informações do Cliente</h5>
                                    <p><strong>Nome:</strong> {{ $customer->name }}</p>
                                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                                    <p><strong>Telefone:</strong> {{ $customer->phone }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Status de Fidelidade</h5>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3 class="mb-0">{{ $loyaltyPoints->current_balance }} pontos</h3>
                                        <span class="badge bg-{{ $loyaltyPoints->level == 'platinum' ? 'primary' : ($loyaltyPoints->level == 'gold' ? 'warning' : ($loyaltyPoints->level == 'silver' ? 'secondary' : 'dark')) }} p-2">
                                            {{ ucfirst($loyaltyPoints->level) }}
                                        </span>
                                    </div>
                                    <p>
                                        <strong>Pontos acumulados:</strong> {{ $loyaltyPoints->points }}<br>
                                        <strong>Pontos utilizados:</strong> {{ $loyaltyPoints->points_spent }}<br>
                                        <strong>Pontos expirados:</strong> {{ $loyaltyPoints->points_expired }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Histórico Completo de Transações</span>
                                <div>
                                    @can('admin')
                                    <a href="{{ route('loyalty.add-points.form', $customer) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle"></i> Adicionar Pontos
                                    </a>
                                    @endcan
                                    <a href="{{ route('loyalty.redeem-points.form', $customer) }}" class="btn btn-sm btn-primary ms-2">
                                        <i class="bi bi-gift"></i> Resgatar Pontos
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Tipo</th>
                                            <th>Pontos</th>
                                            <th>Descrição</th>
                                            <th>Fatura</th>
                                            <th>Campanha</th>
                                            <th>Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @if($transaction->type == 'earn')
                                                        <span class="badge bg-success">Ganho</span>
                                                    @elseif($transaction->type == 'redeem')
                                                        <span class="badge bg-primary">Resgate</span>
                                                    @elseif($transaction->type == 'expire')
                                                        <span class="badge bg-danger">Expirado</span>
                                                    @elseif($transaction->type == 'adjust')
                                                        <span class="badge bg-warning">Ajuste</span>
                                                    @elseif($transaction->type == 'bonus')
                                                        <span class="badge bg-info">Bônus</span>
                                                    @elseif($transaction->type == 'campaign')
                                                        <span class="badge bg-secondary">Campanha</span>
                                                    @endif
                                                </td>
                                                <td class="{{ $transaction->points >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->points >= 0 ? '+' : '' }}{{ $transaction->points }}
                                                </td>
                                                <td>{{ $transaction->description }}</td>
                                                <td>
                                                    @if($transaction->invoice_id)
                                                        <a href="{{ route('invoices.show', $transaction->invoice_id) }}" class="btn btn-sm btn-outline-secondary">
                                                            Ver Fatura
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($transaction->campaign_id)
                                                        <a href="{{ route('campaigns.show', $transaction->campaign_id) }}" class="btn btn-sm btn-outline-secondary">
                                                            Ver Campanha
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $transaction->balance_after }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Nenhuma transação encontrada.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
