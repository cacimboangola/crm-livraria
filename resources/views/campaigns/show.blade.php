@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Detalhes da Campanha') }}</span>
                    <div>
                        <a href="{{ route('campaigns.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Voltar') }}
                        </a>
                        <a href="{{ route('campaigns.edit', $campaign->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> {{ __('Editar') }}
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

                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('Informações da Campanha') }}</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('Nome') }}</th>
                                    <td>{{ $campaign->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Tipo') }}</th>
                                    <td>
                                        @switch($campaign->type)
                                            @case('email')
                                                <span class="badge bg-primary">{{ __('Email') }}</span>
                                                @break
                                            @case('sms')
                                                <span class="badge bg-info">{{ __('SMS') }}</span>
                                                @break
                                            @case('desconto')
                                                <span class="badge bg-success">{{ __('Desconto') }}</span>
                                                @break
                                            @case('evento')
                                                <span class="badge bg-warning">{{ __('Evento') }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $campaign->type }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <td>
                                        @switch($campaign->status)
                                            @case('draft')
                                                <span class="badge bg-secondary">{{ __('Rascunho') }}</span>
                                                @break
                                            @case('active')
                                                <span class="badge bg-success">{{ __('Ativa') }}</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-primary">{{ __('Concluída') }}</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">{{ __('Cancelada') }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $campaign->status }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Data de Início') }}</th>
                                    <td>{{ $campaign->start_date->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Data de Término') }}</th>
                                    <td>{{ $campaign->end_date ? $campaign->end_date->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Descrição') }}</th>
                                    <td>{{ $campaign->description ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>{{ __('Métricas da Campanha') }}</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6>{{ __('Clientes Alvo') }}</h6>
                                            <h3>{{ $metrics['total_customers'] ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h6>{{ __('Emails Enviados') }}</h6>
                                            <h3>{{ $metrics['sent'] ?? 0 }}</h3>
                                            <small>{{ number_format($metrics['sent_rate'] ?? 0, 1) }}%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h6>{{ __('Emails Abertos') }}</h6>
                                            <h3>{{ $metrics['opened'] ?? 0 }}</h3>
                                            <small>{{ number_format($metrics['open_rate'] ?? 0, 1) }}%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h6>{{ __('Conversões') }}</h6>
                                            <h3>{{ $metrics['converted'] ?? 0 }}</h3>
                                            <small>{{ number_format($metrics['conversion_rate'] ?? 0, 1) }}%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('campaigns.metrics', $campaign->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-chart-line"></i> {{ __('Ver Métricas Detalhadas') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>{{ __('Conteúdo da Campanha') }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="border p-3 bg-light">
                                        {!! $campaign->content !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>{{ __('Clientes da Campanha') }}</span>
                                    <div>
                                        <a href="{{ route('campaigns.select-customers', $campaign->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-user-plus"></i> {{ __('Adicionar Clientes') }}
                                        </a>
                                        <form action="{{ route('campaigns.auto-select-customers', $campaign->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-magic"></i> {{ __('Seleção Automática') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Nome') }}</th>
                                                    <th>{{ __('Email') }}</th>
                                                    <th>{{ __('Enviado') }}</th>
                                                    <th>{{ __('Aberto') }}</th>
                                                    <th>{{ __('Clicado') }}</th>
                                                    <th>{{ __('Convertido') }}</th>
                                                    <th>{{ __('Ações') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($campaign->customers as $customer)
                                                    <tr>
                                                        <td>{{ $customer->name }}</td>
                                                        <td>{{ $customer->email }}</td>
                                                        <td>
                                                            @if ($customer->pivot->sent)
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check"></i> {{ $customer->pivot->sent_at ? $customer->pivot->sent_at->format('d/m/Y H:i') : '' }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">
                                                                    <i class="fas fa-times"></i>
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($customer->pivot->opened)
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check"></i> {{ $customer->pivot->opened_at ? $customer->pivot->opened_at->format('d/m/Y H:i') : '' }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">
                                                                    <i class="fas fa-times"></i>
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($customer->pivot->clicked)
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check"></i> {{ $customer->pivot->clicked_at ? $customer->pivot->clicked_at->format('d/m/Y H:i') : '' }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">
                                                                    <i class="fas fa-times"></i>
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($customer->pivot->converted)
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check"></i> {{ $customer->pivot->converted_at ? $customer->pivot->converted_at->format('d/m/Y H:i') : '' }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">
                                                                    <i class="fas fa-times"></i>
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <form action="{{ route('campaigns.remove-customers', $campaign->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="customer_ids[]" value="{{ $customer->id }}">
                                                                <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Remover') }}">
                                                                    <i class="fas fa-user-minus"></i>
                                                                </button>
                                                            </form>
                                                        </td>
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

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <span>{{ __('Ações da Campanha') }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex gap-2 flex-wrap">
                                        @if ($campaign->status === 'draft')
                                            <form action="{{ route('campaigns.activate', $campaign->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-play-fill"></i> {{ __('Ativar Campanha') }}
                                                </button>
                                            </form>
                                        @endif

                                        @if ($campaign->status === 'active')
                                            <form action="{{ route('campaigns.complete', $campaign->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-check-circle"></i> {{ __('Concluir Campanha') }}
                                                </button>
                                            </form>

                                            <form action="{{ route('campaigns.cancel', $campaign->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="bi bi-ban"></i> {{ __('Cancelar Campanha') }}
                                                </button>
                                            </form>
                                        @endif

                                        @if ($campaign->type === 'email' && ($campaign->status === 'active' || $campaign->status === 'draft'))
                                            <form action="{{ route('campaigns.send-emails', $campaign->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-info">
                                                    <i class="bi bi-envelope"></i> {{ __('Enviar Emails') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <span>{{ __('Programa de Fidelidade') }}</span>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('campaigns.distribute-points', $campaign->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="points" class="form-label">{{ __('Pontos a Distribuir') }}</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="points" name="points" min="1" value="100" required>
                                                <span class="input-group-text"><i class="bi bi-award"></i></span>
                                            </div>
                                            <div class="form-text">{{ __('Quantidade de pontos a distribuir para cada cliente da campanha.') }}</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="description" class="form-label">{{ __('Descrição') }}</label>
                                            <input type="text" class="form-control" id="description" name="description" 
                                                value="Pontos de fidelidade - Campanha {{ $campaign->name }}" required>
                                            <div class="form-text">{{ __('Motivo da distribuição de pontos.') }}</div>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-gift"></i> {{ __('Distribuir Pontos de Fidelidade') }}
                                            </button>
                                        </div>
                                        
                                        <div class="mt-3 alert alert-info">
                                            <i class="bi bi-info-circle"></i> 
                                            {{ __('Esta ação irá distribuir pontos de fidelidade para todos os ') }} 
                                            <strong>{{ $campaign->customers->count() }}</strong> 
                                            {{ __('clientes desta campanha.') }}
                                        </div>
                                    </form>
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
