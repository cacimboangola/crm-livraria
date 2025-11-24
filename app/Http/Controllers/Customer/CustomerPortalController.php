<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\BookService;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerPortalController extends Controller
{
    protected $bookService;
    protected $loyaltyService;

    public function __construct(BookService $bookService, LoyaltyService $loyaltyService)
    {
        $this->bookService = $bookService;
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Exibe o perfil do cliente
     */
    public function viewProfile()
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        if (!$customer) {
            return redirect()->route('customer.catalog')
                ->with('error', 'Perfil de cliente não encontrado.');
        }
        
        // Obter pontos de fidelidade
        $loyaltyPoints = $this->loyaltyService->getCustomerPoints($customer->id);
        
        return view('customer.profile', compact('customer', 'loyaltyPoints'));
    }
    
    /**
     * Exibe o dashboard do cliente
     */
    public function dashboard()
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        if (!$customer) {
            return redirect()->route('customer.catalog')
                ->with('error', 'Perfil de cliente não encontrado.');
        }
        
        // Obter faturas do cliente
        $invoices = Invoice::where('customer_id', $customer->id)
                          ->orderBy('invoice_date', 'desc')
                          ->take(5)
                          ->get();
        
        // Obter pontos de fidelidade
        $loyaltyPoints = $this->loyaltyService->getCustomerPoints($customer->id);
        
        return view('customer.dashboard', compact('customer', 'invoices', 'loyaltyPoints'));
    }

    /**
     * Exibe o catálogo de livros
     */
    public function catalog(Request $request)
    {
        $query = Book::with('category');
        
        // Filtrar por categoria
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Buscar por termo
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Ordenação
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'title_asc':
                    $query->orderBy('title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('title', 'asc');
            }
        } else {
            $query->orderBy('title', 'asc');
        }
        
        $books = $query->where('active', true)->paginate(12);
        $categories = BookCategory::where('active', true)->orderBy('name')->get();
        
        return view('customer.catalog', compact('books', 'categories'));
    }

    /**
     * Exibe os detalhes de um livro
     */
    public function bookDetails(Book $book)
    {
        // Verificar se o livro está ativo
        if (!$book->active) {
            return redirect()->route('customer.catalog')
                ->with('error', 'Livro não disponível.');
        }
        
        // Obter livros relacionados (mesma categoria)
        $relatedBooks = Book::where('category_id', $book->category_id)
                           ->where('id', '!=', $book->id)
                           ->where('active', true)
                           ->take(4)
                           ->get();
        
        return view('customer.book-details', compact('book', 'relatedBooks'));
    }

    /**
     * Exibe os pedidos do cliente
     */
    public function orders()
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        if (!$customer) {
            return redirect()->route('customer.catalog')
                ->with('error', 'Perfil de cliente não encontrado.');
        }
        
        $invoices = Invoice::where('customer_id', $customer->id)
                          ->orderBy('invoice_date', 'desc')
                          ->paginate(10);
        
        return view('customer.orders', compact('invoices'));
    }

    /**
     * Exibe os detalhes de um pedido
     */
    public function orderDetails($id)
    {
        $invoice = Invoice::with(['invoiceItems.book', 'customer'])->findOrFail($id);
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        // Verificar se a fatura pertence ao cliente autenticado
        if ($invoice->customer_id !== $customer->id) {
            return redirect()->route('customer.orders')
                ->with('error', 'Você não tem permissão para acessar esta fatura.');
        }
        
        // Calcular pontos de fidelidade (se a fatura estiver paga)
        $loyaltyPoints = 0;
        if ($invoice->status === 'paid') {
            $loyaltyPoints = $this->loyaltyService->calculatePointsForPurchase($invoice->total, $invoice->customer->loyaltyPoints->level ?? 'bronze');
        }
        
        return view('customer.order-details', compact('invoice', 'loyaltyPoints'));
    }

    /**
     * Gera o PDF de um pedido
     */
    public function orderPdf($id)
    {
        $invoice = Invoice::with(['invoiceItems.book', 'customer'])->findOrFail($id);
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        // Verificar se a fatura pertence ao cliente autenticado
        if ($invoice->customer_id !== $customer->id) {
            return redirect()->route('customer.orders')
                ->with('error', 'Você não tem permissão para acessar esta fatura.');
        }
        
        $pdf = PDF::loadView('pdf.invoice', compact('invoice'));
        return $pdf->download('pedido-' . $invoice->id . '.pdf');
    }

    /**
     * Exibe o formulário de edição do perfil
     */
    public function editProfile()
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        if (!$customer) {
            return redirect()->route('customer.dashboard')
                ->with('error', 'Perfil de cliente não encontrado.');
        }
        
        return view('customer.profile-edit', compact('customer'));
    }

    /**
     * Atualiza o perfil do cliente
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        if (!$customer) {
            return redirect()->route('customer.dashboard')
                ->with('error', 'Perfil de cliente não encontrado.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'postal_code' => 'nullable|string|max:10',
            'birth_date' => 'nullable|date',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);
        
        // Atualizar dados do cliente
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->postal_code = $request->postal_code;
        $customer->birth_date = $request->birth_date;
        $customer->save();
        
        // Atualizar email do usuário também
        $user->email = $request->email;
        
        // Atualizar senha se fornecida
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        
        $user->save();
        
        return redirect()->route('customer.profile.edit')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Exibe o painel de fidelidade do cliente
     */
    public function loyalty()
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        if (!$customer) {
            return redirect()->route('customer.dashboard')
                ->with('error', 'Perfil de cliente não encontrado.');
        }
        
        $points = $this->loyaltyService->getCustomerPoints($customer->id);
        $transactions = $this->loyaltyService->getCustomerTransactions($customer->id);
        
        return view('customer.loyalty', compact('customer', 'points', 'transactions'));
    }
}
