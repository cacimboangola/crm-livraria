@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Campanhas de Marketing') }}</span>
                    <a href="{{ route('campaigns.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Nova Campanha') }}
                    </a>
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

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Nome') }}</th>
                                    <th>{{ __('Tipo') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Data de Início') }}</th>
                                    <th>{{ __('Data de Término') }}</th>
                                    <th>{{ __('Clientes') }}</th>
                                    <th>{{ __('Ações') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($campaigns as $campaign)
                                    <tr>
                                        <td>{{ $campaign->name }}</td>
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
                                        <td>{{ $campaign->start_date->format('d/m/Y') }}</td>
                                        <td>{{ $campaign->end_date ? $campaign->end_date->format('d/m/Y') : 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $campaign->customers->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('campaigns.show', $campaign->id) }}" class="btn btn-sm btn-info" title="{{ __('Visualizar') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('campaigns.edit', $campaign->id) }}" class="btn btn-sm btn-primary" title="{{ __('Editar') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('campaigns.metrics', $campaign->id) }}" class="btn btn-sm btn-success" title="{{ __('Métricas') }}">
                                                    <i class="fas fa-chart-line"></i>
                                                </a>
                                                <form action="{{ route('campaigns.destroy', $campaign->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Tem certeza que deseja excluir esta campanha?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Excluir') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('Nenhuma campanha encontrada.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $campaigns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
