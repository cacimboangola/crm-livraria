@extends('layouts.app')

@section('styles')
<style>
    .stat-card {
        transition: all 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }
    .progress-thin {
        height: 5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4">Dashboard Analítico</h1>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Total de Clientes</h6>
                            <h2 class="card-title mb-0">{{ $totalClientes ?? \App\Models\Customer::count() }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('customers.index') }}" class="text-white">Ver detalhes <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Total de Vendas</h6>
                            <h2 class="card-title mb-0">R$ {{ number_format($totalVendas ?? \App\Models\Invoice::where('status', 'paid')->sum('total'), 2, ',', '.') }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('invoices.index') }}?status=paid" class="text-white">Ver detalhes <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Faturas Pendentes</h6>
                            <h2 class="card-title mb-0">{{ \App\Models\Invoice::where('status', 'pending')->count() }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('invoices.index') }}?status=pending" class="text-dark">Ver detalhes <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Estoque Total</h6>
                            <h2 class="card-title mb-0">{{ \App\Models\Book::sum('stock') }} livros</h2>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-book"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('books.index') }}" class="text-white">Ver detalhes <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Últimas Faturas</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Cliente</th>
                                                <th>Data</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(\App\Models\Invoice::with('customer')->latest()->take(5)->get() as $invoice)
                                            <tr>
                                                <td>{{ $invoice->id }}</td>
                                                <td>{{ $invoice->customer->name }}</td>
                                                <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                                                <td>{{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
                                                <td>
                                                    @if($invoice->status == 'paid')
                                                        <span class="badge bg-success">Pago</span>
                                                    @elseif($invoice->status == 'pending')
                                                        <span class="badge bg-warning text-dark">Pendente</span>
                                                    @elseif($invoice->status == 'draft')
                                                        <span class="badge bg-secondary">Rascunho</span>
                                                    @else
                                                        <span class="badge bg-danger">Cancelado</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <a href="{{ route('invoices.index') }}" class="btn btn-primary">Ver Todas</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Livros com Baixo Estoque</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Autor</th>
                                                <th>Estoque</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(\App\Models\Book::where('stock', '<', 10)->where('active', true)->take(5)->get() as $book)
                                            <tr>
                                                <td>{{ $book->title }}</td>
                                                <td>{{ $book->author }}</td>
                                                <td>
                                                    @if($book->stock < 5)
                                                        <span class="text-danger">{{ $book->stock }}</span>
                                                    @else
                                                        <span class="text-warning">{{ $book->stock }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('books.edit', $book->id) }}" class="btn btn-sm btn-primary">Atualizar</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <a href="{{ route('books.index') }}" class="btn btn-primary">Ver Todos</a>
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
