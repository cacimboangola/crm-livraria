@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Novo Cupom de Desconto</h1>
                <a href="{{ route('coupons.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('coupons.store') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="code" class="form-label">Código do Cupom <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-uppercase @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required maxlength="50" placeholder="Ex: DESCONTO10">
                                    <button type="button" class="btn btn-outline-secondary" id="generateCode" title="Gerar código aleatório">
                                        <i class="bi bi-shuffle"></i>
                                    </button>
                                </div>
                                @error('code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Código único que o cliente irá digitar.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome do Cupom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Ex: Desconto de Boas-vindas">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" placeholder="Descrição opcional do cupom">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Tipo de Desconto <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentual (%)</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Valor Fixo (Kz)</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="value" class="form-label">Valor do Desconto <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="valuePrefix">%</span>
                                    <input type="number" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value') }}" required min="0.01" step="0.01" placeholder="10">
                                </div>
                                @error('value')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="min_order_value" class="form-label">Valor Mínimo do Pedido</label>
                                <div class="input-group">
                                    <span class="input-group-text">Kz</span>
                                    <input type="number" class="form-control @error('min_order_value') is-invalid @enderror" id="min_order_value" name="min_order_value" value="{{ old('min_order_value') }}" min="0" step="0.01" placeholder="0.00">
                                </div>
                                @error('min_order_value')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Deixe vazio para não exigir valor mínimo.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="max_discount" class="form-label">Desconto Máximo</label>
                                <div class="input-group">
                                    <span class="input-group-text">Kz</span>
                                    <input type="number" class="form-control @error('max_discount') is-invalid @enderror" id="max_discount" name="max_discount" value="{{ old('max_discount') }}" min="0" step="0.01" placeholder="0.00">
                                </div>
                                @error('max_discount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Útil para cupons percentuais. Deixe vazio para não limitar.</div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="usage_limit" class="form-label">Limite Total de Usos</label>
                                <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}" min="1" placeholder="Ilimitado">
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Quantas vezes o cupom pode ser usado no total.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="usage_limit_per_user" class="form-label">Limite por Cliente</label>
                                <input type="number" class="form-control @error('usage_limit_per_user') is-invalid @enderror" id="usage_limit_per_user" name="usage_limit_per_user" value="{{ old('usage_limit_per_user') }}" min="1" placeholder="Ilimitado">
                                @error('usage_limit_per_user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Quantas vezes cada cliente pode usar.</div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Data de Início</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Deixe vazio para começar imediatamente.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Data de Término</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Deixe vazio para não expirar.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Cupom ativo</label>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('coupons.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Criar Cupom
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const valuePrefix = document.getElementById('valuePrefix');
    const generateBtn = document.getElementById('generateCode');
    const codeInput = document.getElementById('code');

    // Atualizar prefixo baseado no tipo
    function updatePrefix() {
        if (typeSelect.value === 'percentage') {
            valuePrefix.textContent = '%';
        } else {
            valuePrefix.textContent = 'Kz';
        }
    }

    typeSelect.addEventListener('change', updatePrefix);
    updatePrefix();

    // Gerar código aleatório
    generateBtn.addEventListener('click', function() {
        fetch('{{ route("coupons.generate-code") }}')
            .then(response => response.json())
            .then(data => {
                codeInput.value = data.code;
            })
            .catch(error => {
                console.error('Erro ao gerar código:', error);
            });
    });

    // Converter código para maiúsculas
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endpush
@endsection
