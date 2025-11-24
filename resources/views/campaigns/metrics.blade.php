@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Métricas da Campanha') }}: {{ $campaign->name }}</span>
                    <div>
                        <a href="{{ route('campaigns.show', $campaign->id) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Voltar para Campanha') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>{{ __('Clientes Alvo') }}</h6>
                                    <h3>{{ $metrics['total_customers'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6>{{ __('Emails Enviados') }}</h6>
                                    <h3>{{ $metrics['sent'] ?? 0 }}</h3>
                                    <small>{{ number_format($metrics['sent_rate'] ?? 0, 1) }}%</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6>{{ __('Emails Abertos') }}</h6>
                                    <h3>{{ $metrics['opened'] ?? 0 }}</h3>
                                    <small>{{ number_format($metrics['open_rate'] ?? 0, 1) }}%</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>{{ __('Conversões') }}</h6>
                                    <h3>{{ $metrics['converted'] ?? 0 }}</h3>
                                    <small>{{ number_format($metrics['conversion_rate'] ?? 0, 1) }}%</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    {{ __('Progresso da Campanha') }}
                                </div>
                                <div class="card-body">
                                    <h6>{{ __('Taxa de Envio') }}</h6>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $metrics['sent_rate'] ?? 0 }}%;" aria-valuenow="{{ $metrics['sent_rate'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">{{ number_format($metrics['sent_rate'] ?? 0, 1) }}%</div>
                                    </div>
                                    
                                    <h6>{{ __('Taxa de Abertura') }}</h6>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $metrics['open_rate'] ?? 0 }}%;" aria-valuenow="{{ $metrics['open_rate'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">{{ number_format($metrics['open_rate'] ?? 0, 1) }}%</div>
                                    </div>
                                    
                                    <h6>{{ __('Taxa de Clique') }}</h6>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $metrics['click_rate'] ?? 0 }}%;" aria-valuenow="{{ $metrics['click_rate'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">{{ number_format($metrics['click_rate'] ?? 0, 1) }}%</div>
                                    </div>
                                    
                                    <h6>{{ __('Taxa de Conversão') }}</h6>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $metrics['conversion_rate'] ?? 0 }}%;" aria-valuenow="{{ $metrics['conversion_rate'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">{{ number_format($metrics['conversion_rate'] ?? 0, 1) }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    {{ __('Comparação com Médias') }}
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Métrica') }}</th>
                                                    <th>{{ __('Esta Campanha') }}</th>
                                                    <th>{{ __('Média do Setor') }}</th>
                                                    <th>{{ __('Comparação') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ __('Taxa de Abertura') }}</td>
                                                    <td>{{ number_format($metrics['open_rate'] ?? 0, 1) }}%</td>
                                                    <td>20.0%</td>
                                                    <td>
                                                        @if(($metrics['open_rate'] ?? 0) > 20)
                                                            <span class="text-success"><i class="fas fa-arrow-up"></i> {{ number_format(($metrics['open_rate'] ?? 0) - 20, 1) }}%</span>
                                                        @elseif(($metrics['open_rate'] ?? 0) < 20)
                                                            <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ number_format(20 - ($metrics['open_rate'] ?? 0), 1) }}%</span>
                                                        @else
                                                            <span class="text-muted"><i class="fas fa-equals"></i> 0%</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('Taxa de Clique') }}</td>
                                                    <td>{{ number_format($metrics['click_rate'] ?? 0, 1) }}%</td>
                                                    <td>2.5%</td>
                                                    <td>
                                                        @if(($metrics['click_rate'] ?? 0) > 2.5)
                                                            <span class="text-success"><i class="fas fa-arrow-up"></i> {{ number_format(($metrics['click_rate'] ?? 0) - 2.5, 1) }}%</span>
                                                        @elseif(($metrics['click_rate'] ?? 0) < 2.5)
                                                            <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ number_format(2.5 - ($metrics['click_rate'] ?? 0), 1) }}%</span>
                                                        @else
                                                            <span class="text-muted"><i class="fas fa-equals"></i> 0%</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('Taxa de Conversão') }}</td>
                                                    <td>{{ number_format($metrics['conversion_rate'] ?? 0, 1) }}%</td>
                                                    <td>1.0%</td>
                                                    <td>
                                                        @if(($metrics['conversion_rate'] ?? 0) > 1)
                                                            <span class="text-success"><i class="fas fa-arrow-up"></i> {{ number_format(($metrics['conversion_rate'] ?? 0) - 1, 1) }}%</span>
                                                        @elseif(($metrics['conversion_rate'] ?? 0) < 1)
                                                            <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ number_format(1 - ($metrics['conversion_rate'] ?? 0), 1) }}%</span>
                                                        @else
                                                            <span class="text-muted"><i class="fas fa-equals"></i> 0%</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    {{ __('Detalhamento por Cliente') }}
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Cliente') }}</th>
                                                    <th>{{ __('Email') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Enviado em') }}</th>
                                                    <th>{{ __('Aberto em') }}</th>
                                                    <th>{{ __('Clicado em') }}</th>
                                                    <th>{{ __('Convertido em') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($campaign->customers as $customer)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('customers.show', $customer->id) }}">
                                                                {{ $customer->name }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $customer->email }}</td>
                                                        <td>
                                                            @if ($customer->pivot->converted)
                                                                <span class="badge bg-success">{{ __('Convertido') }}</span>
                                                            @elseif ($customer->pivot->clicked)
                                                                <span class="badge bg-warning">{{ __('Clicado') }}</span>
                                                            @elseif ($customer->pivot->opened)
                                                                <span class="badge bg-info">{{ __('Aberto') }}</span>
                                                            @elseif ($customer->pivot->sent)
                                                                <span class="badge bg-primary">{{ __('Enviado') }}</span>
                                                            @else
                                                                <span class="badge bg-secondary">{{ __('Pendente') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $customer->pivot->sent_at ? $customer->pivot->sent_at->format('d/m/Y H:i') : '-' }}</td>
                                                        <td>{{ $customer->pivot->opened_at ? $customer->pivot->opened_at->format('d/m/Y H:i') : '-' }}</td>
                                                        <td>{{ $customer->pivot->clicked_at ? $customer->pivot->clicked_at->format('d/m/Y H:i') : '-' }}</td>
                                                        <td>{{ $customer->pivot->converted_at ? $customer->pivot->converted_at->format('d/m/Y H:i') : '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">{{ __('Nenhum cliente adicionado a esta campanha.') }}</td>
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
    </div>
</div>
@endsection
