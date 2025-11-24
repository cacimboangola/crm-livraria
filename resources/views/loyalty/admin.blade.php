@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Administração do Programa de Fidelidade</span>
                    <div>
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar ao Dashboard
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
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total de Pontos</h5>
                                    <h2 class="mb-0">{{ number_format($totalPoints, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Clientes Participantes</h5>
                                    <h2 class="mb-0">{{ number_format($totalCustomers, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total de Transações</h5>
                                    <h2 class="mb-0">{{ number_format($totalTransactions, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Média de Pontos</h5>
                                    <h2 class="mb-0">{{ $totalCustomers > 0 ? number_format($totalPoints / $totalCustomers, 0, ',', '.') : 0 }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Distribuição de Níveis</div>
                                <div class="card-body">
                                    <canvas id="levelDistributionChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Expiração de Pontos</span>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('loyalty.process-expiration') }}" class="mb-3">
                                        @csrf
                                        <div class="row g-3 align-items-center">
                                            <div class="col-auto">
                                                <label for="months" class="col-form-label">Expirar pontos mais antigos que</label>
                                            </div>
                                            <div class="col-auto">
                                                <select class="form-select" id="months" name="months">
                                                    <option value="6">6 meses</option>
                                                    <option value="12" selected>12 meses</option>
                                                    <option value="18">18 meses</option>
                                                    <option value="24">24 meses</option>
                                                    <option value="36">36 meses</option>
                                                </select>
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-warning" onclick="return confirm('Tem certeza que deseja processar a expiração de pontos? Esta ação não pode ser desfeita.')">
                                                    <i class="bi bi-exclamation-triangle"></i> Processar Expiração
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    <div class="alert alert-info">
                                        <h5><i class="bi bi-info-circle"></i> Informações sobre Expiração</h5>
                                        <p>A expiração de pontos é um processo que remove pontos não utilizados após um determinado período. Este processo:</p>
                                        <ul>
                                            <li>Afeta apenas pontos ganhos antes do período selecionado</li>
                                            <li>Registra uma transação de expiração para cada cliente afetado</li>
                                            <li>Atualiza automaticamente o nível de fidelidade dos clientes</li>
                                            <li>Não pode ser desfeito após a execução</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">Transações Recentes</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Cliente</th>
                                            <th>Tipo</th>
                                            <th>Pontos</th>
                                            <th>Descrição</th>
                                            <th>Saldo</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTransactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('customers.show', $transaction->customer) }}">
                                                        {{ $transaction->customer->name }}
                                                    </a>
                                                </td>
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
                                                <td>
                                                    <a href="{{ route('loyalty.dashboard', $transaction->customer) }}" class="btn btn-sm btn-outline-primary">
                                                        Ver Fidelidade
                                                    </a>
                                                </td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dados para o gráfico de distribuição de níveis
        const levelData = @json($levelDistribution);
        
        // Configuração do gráfico
        const ctx = document.getElementById('levelDistributionChart').getContext('2d');
        const levelChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: Object.keys(levelData).map(level => level.charAt(0).toUpperCase() + level.slice(1)),
                datasets: [{
                    data: Object.values(levelData),
                    backgroundColor: [
                        '#343a40', // bronze
                        '#6c757d', // silver
                        '#ffc107', // gold
                        '#0d6efd'  // platinum
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Distribuição de Clientes por Nível'
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
