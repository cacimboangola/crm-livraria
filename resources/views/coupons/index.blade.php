@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Cupons de Desconto</h1>
        <a href="{{ route('coupons.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Novo Cupom
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('coupons.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Buscar por código ou nome..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Todos os Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirados</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">Todos os Tipos</option>
                        <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Percentual</option>
                        <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Valor Fixo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Uso</th>
                            <th>Validade</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $coupon->code }}</code>
                                </td>
                                <td>{{ $coupon->name }}</td>
                                <td>
                                    <span class="badge {{ $coupon->type == 'percentage' ? 'bg-info' : 'bg-primary' }}">
                                        {{ $coupon->type_formatted }}
                                    </span>
                                </td>
                                <td>{{ $coupon->value_formatted }}</td>
                                <td>
                                    {{ $coupon->usage_count }}
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @else
                                        <span class="text-muted">/ ∞</span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->start_date || $coupon->end_date)
                                        <small>
                                            @if($coupon->start_date)
                                                {{ $coupon->start_date->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                            até
                                            @if($coupon->end_date)
                                                {{ $coupon->end_date->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">Sem limite</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $coupon->status_badge_class }}">
                                        {{ $coupon->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('coupons.show', $coupon) }}" class="btn btn-outline-info" title="Ver detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('coupons.toggle-status', $coupon) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-{{ $coupon->is_active ? 'warning' : 'success' }}" title="{{ $coupon->is_active ? 'Desativar' : 'Ativar' }}">
                                                <i class="bi bi-{{ $coupon->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        @if($coupon->usage_count == 0)
                                            <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este cupom?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-ticket-perforated fs-1 d-block mb-2"></i>
                                    Nenhum cupom encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($coupons->hasPages())
            <div class="card-footer">
                {{ $coupons->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
