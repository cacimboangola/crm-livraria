@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Nova Campanha de Marketing') }}</span>
                    <a href="{{ route('campaigns.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Voltar') }}
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('campaigns.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('Nome da Campanha') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">{{ __('Tipo de Campanha') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="" selected disabled>{{ __('Selecione um tipo') }}</option>
                                        <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>{{ __('Email') }}</option>
                                        <option value="sms" {{ old('type') == 'sms' ? 'selected' : '' }}>{{ __('SMS') }}</option>
                                        <option value="desconto" {{ old('type') == 'desconto' ? 'selected' : '' }}>{{ __('Desconto') }}</option>
                                        <option value="evento" {{ old('type') == 'evento' ? 'selected' : '' }}>{{ __('Evento') }}</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">{{ __('Data de Início') }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">{{ __('Data de Término') }}</label>
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}">
                                    <small class="form-text text-muted">{{ __('Deixe em branco para campanhas sem data de término definida.') }}</small>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Descrição da Campanha') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">{{ __('Conteúdo da Campanha') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                            <small class="form-text text-muted">{{ __('Para campanhas de email, você pode usar HTML para formatar o conteúdo.') }}</small>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">{{ __('Critérios de Segmentação (opcional)') }}</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="target_criteria[min_purchases]" class="form-label">{{ __('Mínimo de Compras') }}</label>
                                                <input type="number" class="form-control" id="target_criteria_min_purchases" name="target_criteria[min_purchases]" value="{{ old('target_criteria.min_purchases') }}" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="target_criteria[min_total_spent]" class="form-label">{{ __('Valor Mínimo Gasto (Kz)') }}</label>
                                                <input type="number" class="form-control" id="target_criteria_min_total_spent" name="target_criteria[min_total_spent]" value="{{ old('target_criteria.min_total_spent') }}" min="0" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="target_criteria[days_since_last_purchase]" class="form-label">{{ __('Dias desde a última compra') }}</label>
                                                <input type="number" class="form-control" id="target_criteria_days_since_last_purchase" name="target_criteria[days_since_last_purchase]" value="{{ old('target_criteria.days_since_last_purchase') }}" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="target_criteria[categories]" class="form-label">{{ __('Categorias de interesse') }}</label>
                                                <select class="form-select" id="target_criteria_categories" name="target_criteria[categories][]" multiple>
                                                    @foreach(\App\Models\BookCategory::all() as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">{{ __('Pressione Ctrl para selecionar múltiplas categorias.') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Salvar Campanha') }}
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
    // Inicializar editor de texto rico para o conteúdo da campanha, se disponível
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#content'))
                .catch(error => {
                    console.error(error);
                });
        }
    });
</script>
@endpush
@endsection
