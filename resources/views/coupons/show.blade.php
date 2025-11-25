@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detalhes do Cupom</h1>
        <div>
            <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Editar
            </a>
            <a href="{{ route('coupons.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Informações do Cupom</span>
                    <span class="badge {{ $coupon->status_badge_class }}">{{ $coupon->status }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Código</label>
                            <div><code class="fs-4 bg-light px-3 py-2 rounded">{{ $coupon->code }}</code></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Nome</label>
                            <div class="fw-semibold">{{ $coupon->name }}</div>
                        </div>
                    </div>

                    @if($coupon->description)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Descrição</label>
                        <div>{{ $coupon->description }}</div>
                    </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Tipo de Desconto</label>
                            <div>
                                <span class="badge {{ $coupon->type == 'percentage' ? 'bg-info' : 'bg-primary' }}">
                                    {{ $coupon->type_formatted }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Valor</label>
                            <div class="fs-5 fw-bold text-success">{{ $coupon->value_formatted }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Desconto Máximo</label>
                            <div>
                                @if($coupon->max_discount)
                                    Kz {{ number_format($coupon->max_discount, 2, ',', '.') }}
                                @else
                                    <span class="text-muted">Sem limite</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Valor Mínimo do Pedido</label>
                            <div>
                                @if($coupon->min_order_value)
                                    Kz {{ number_format($coupon->min_order_value, 2, ',', '.') }}
                                @else
                                    <span class="text-muted">Sem mínimo</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Limite Total de Usos</label>
                            <div>
                                @if($coupon->usage_limit)
                                    {{ $coupon->usage_limit }}
                                @else
                                    <span class="text-muted">Ilimitado</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Limite por Cliente</label>
                            <div>
                                @if($coupon->usage_limit_per_user)
                                    {{ $coupon->usage_limit_per_user }}
                                @else
                                    <span class="text-muted">Ilimitado</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Período de Validade</label>
                            <div>
                                @if($coupon->start_date || $coupon->end_date)
                                    {{ $coupon->start_date ? $coupon->start_date->format('d/m/Y') : 'Início imediato' }}
                                    até
                                    {{ $coupon->end_date ? $coupon->end_date->format('d/m/Y') : 'Sem expiração' }}
                                @else
                                    <span class="text-muted">Sem limite de período</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Criado em</label>
                            <div>{{ $coupon->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico de Uso -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history me-1"></i> Histórico de Uso
                </div>
                <div class="card-body p-0">
                    @if($coupon->customers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Desconto Aplicado</th>
                                        <th>Data de Uso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($coupon->customers as $customer)
                                        <tr>
                                            <td>{{ $customer->name }}</td>
                                            <td>Kz {{ number_format($customer->pivot->discount_applied, 2, ',', '.') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($customer->pivot->used_at)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Este cupom ainda não foi utilizado.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Estatísticas -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-graph-up me-1"></i> Estatísticas
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Total de Usos</span>
                        <span class="badge bg-primary fs-6">{{ $coupon->usage_count }}</span>
                    </div>
                    @if($coupon->usage_limit)
                        <div class="progress mb-3" style="height: 8px;">
                            @php $percentage = min(100, ($coupon->usage_count / $coupon->usage_limit) * 100); @endphp
                            <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%"></div>
                        </div>
                        <small class="text-muted">{{ $coupon->usage_count }} de {{ $coupon->usage_limit }} usos</small>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <span>Total em Descontos</span>
                        <span class="fw-bold text-success">
                            Kz {{ number_format($coupon->customers->sum('pivot.discount_applied'), 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="card">
                <div class="card-header">Ações Rápidas</div>
                <div class="card-body d-grid gap-2">
                    <form action="{{ route('coupons.toggle-status', $coupon) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $coupon->is_active ? 'warning' : 'success' }} w-100">
                            <i class="bi bi-{{ $coupon->is_active ? 'pause' : 'play' }} me-1"></i>
                            {{ $coupon->is_active ? 'Desativar Cupom' : 'Ativar Cupom' }}
                        </button>
                    </form>
                    
                    <button type="button" class="btn btn-outline-secondary" onclick="copyCode()">
                        <i class="bi bi-clipboard me-1"></i> Copiar Código
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyCode() {
    navigator.clipboard.writeText('{{ $coupon->code }}').then(() => {
        alert('Código copiado: {{ $coupon->code }}');
    });
}
</script>
@endpush
@endsection
