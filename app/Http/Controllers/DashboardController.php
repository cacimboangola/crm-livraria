<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard com dados analíticos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Estatísticas gerais
        $totalClientes = Customer::count();
        $totalLivros = Book::count();
        $totalFaturas = Invoice::count();
        $totalVendas = Invoice::where('status', 'paid')->sum('total');
        
        // Vendas dos últimos 6 meses
        $vendasPorMes = Invoice::where('status', 'paid')
            ->where('payment_date', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw('MONTH(payment_date) as mes'),
                DB::raw('YEAR(payment_date) as ano'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('ano', 'mes')
            ->orderBy('ano')
            ->orderBy('mes')
            ->get();
        
        // Formatar dados para gráfico
        $meses = [];
        $valores = [];
        
        foreach ($vendasPorMes as $venda) {
            $data = Carbon::createFromDate($venda->ano, $venda->mes, 1);
            $meses[] = $data->format('M/Y');
            $valores[] = $venda->total;
        }
        
        // Top 5 clientes
        $topClientes = Invoice::where('status', 'paid')
            ->select('customer_id', DB::raw('SUM(total) as total'))
            ->groupBy('customer_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->with('customer')
            ->get();
        
        // Top 5 livros vendidos
        $topLivros = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('books', 'invoice_items.book_id', '=', 'books.id')
            ->select(
                'books.id',
                'books.title',
                DB::raw('SUM(invoice_items.quantity) as quantidade')
            )
            ->where('invoices.status', 'paid')
            ->groupBy('books.id', 'books.title')
            ->orderBy('quantidade', 'desc')
            ->limit(5)
            ->get();
        
        // Faturas pendentes
        $faturasPendentes = Invoice::where('status', 'pending')
            ->where('due_date', '<', Carbon::now()->addDays(7))
            ->with('customer')
            ->orderBy('due_date')
            ->limit(5)
            ->get();
        
        // Livros com estoque baixo
        $livrosEstoqueBaixo = Book::where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(5)
            ->get();
        
        return view('dashboard', compact(
            'totalClientes',
            'totalLivros',
            'totalFaturas',
            'totalVendas',
            'meses',
            'valores',
            'topClientes',
            'topLivros',
            'faturasPendentes',
            'livrosEstoqueBaixo'
        ));
    }
}
