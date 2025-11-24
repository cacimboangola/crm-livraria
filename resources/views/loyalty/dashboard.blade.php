@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Programa de Fidelidade - {{ $customer->name }}</span>
                    <div>
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar ao Cliente
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
                                    <p><strong>Cliente desde:</strong> {{ $customer->created_at->format('d/m/Y') }}</p>
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
                                    <div class="progress mb-3" style="height: 10px;">
                                        @php
                                            $percentage = 0;
                                            if ($loyaltyPoints->level == 'bronze') {
                                                $percentage = min(($loyaltyPoints->current_balance / 500) * 100, 100);
                                            } elseif ($loyaltyPoints->level == 'silver') {
                                                $percentage = min(($loyaltyPoints->current_balance / 2000) * 100, 100);
                                            } elseif ($loyaltyPoints->level == 'gold') {
                                                $percentage = min(($loyaltyPoints->current_balance / 5000) * 100, 100);
                                            } else {
                                                $percentage = 100;
                                            }
                                        @endphp
                                        <div class="progress-bar bg-{{ $loyaltyPoints->level == 'platinum' ? 'primary' : ($loyaltyPoints->level == 'gold' ? 'warning' : ($loyaltyPoints->level == 'silver' ? 'secondary' : 'dark')) }}" 
                                            role="progressbar" 
                                            style="width: {{ $percentage }}%" 
                                            aria-valuenow="{{ $percentage }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <p>
                                        <strong>Pontos acumulados:</strong> {{ $loyaltyPoints->points }}<br>
                                        <strong>Pontos utilizados:</strong> {{ $loyaltyPoints->points_spent }}<br>
                                        <strong>Pontos expirados:</strong> {{ $loyaltyPoints->points_expired }}<br>
                                        <strong>Nível válido até:</strong> {{ $loyaltyPoints->level_expires_at->format('d/m/Y') }}
                                    </p>
                                    <div class="d-flex justify-content-between mt-3">
                                        <a href="{{ route('loyalty.transactions', $customer) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-list-ul"></i> Histórico Completo
                                        </a>
                                        <a href="{{ route('loyalty.redeem-points.form', $customer) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-gift"></i> Resgatar Pontos
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Últimas Transações</span>
                            @can('admin')
                            <a href="{{ route('loyalty.add-points.form', $customer) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-plus-circle"></i> Adicionar Pontos
                            </a>
                            @endcan
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
                                                <td>{{ $transaction->balance_after }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Nenhuma transação encontrada.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">Benefícios do Programa de Fidelidade</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-dark text-white">Bronze</div>
                                        <div class="card-body">
                                            <ul class="list-unstyled">
                                                <li><i class="bi bi-check-circle-fill text-success"></i> 1 ponto por R$ 1</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Acesso a ofertas exclusivas</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-secondary text-white">Silver</div>
                                        <div class="card-body">
                                            <ul class="list-unstyled">
                                                <li><i class="bi bi-check-circle-fill text-success"></i> 1,5 pontos por R$ 1</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Todos os benefícios Bronze</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Frete grátis em compras acima de R$ 100</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-warning">Gold</div>
                                        <div class="card-body">
                                            <ul class="list-unstyled">
                                                <li><i class="bi bi-check-circle-fill text-success"></i> 2 pontos por R$ 1</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Todos os benefícios Silver</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Frete grátis em todas as compras</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Acesso antecipado a lançamentos</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-primary text-white">Platinum</div>
                                        <div class="card-body">
                                            <ul class="list-unstyled">
                                                <li><i class="bi bi-check-circle-fill text-success"></i> 2,5 pontos por R$ 1</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Todos os benefícios Gold</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Atendimento prioritário</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Eventos exclusivos</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> 5% de desconto em todas as compras</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
