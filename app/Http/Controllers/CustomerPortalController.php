<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookService;
use App\Services\BookCategoryService;
use App\Services\CustomerService;
use App\Services\InvoiceService;
use App\Services\LoyaltyService;
use App\Models\Book;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CustomerPortalController extends Controller
{
    protected $bookService;
    protected $bookCategoryService;
    protected $customerService;
    protected $invoiceService;
    protected $loyaltyService;

    /**
     * Construtor do controlador.
     *
     * @param BookService $bookService
     * @param BookCategoryService $bookCategoryService
     * @param CustomerService $customerService
     * @param InvoiceService $invoiceService
     * @param LoyaltyService $loyaltyService
     */
    public function __construct(
        BookService $bookService,
        BookCategoryService $bookCategoryService,
        CustomerService $customerService,
        InvoiceService $invoiceService,
        LoyaltyService $loyaltyService
    ) {
        $this->bookService = $bookService;
        $this->bookCategoryService = $bookCategoryService;
        $this->customerService = $customerService;
        $this->invoiceService = $invoiceService;
        $this->loyaltyService = $loyaltyService;
        $this->middleware('customer');
    }

    /**
     * Exibe o dashboard do cliente.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        
        if (!$customer) {
            return redirect()->route('customer.profile.create')->with('info', 'Por favor, complete seu perfil para continuar.');
        }
        
        $invoices = Invoice::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $loyaltyPoints = $this->loyaltyService->getCustomerPoints($customer->id);
        
        return view('customer.dashboard', compact('customer', 'invoices', 'loyaltyPoints'));
    }

    /**
     * Exibe o catálogo de livros para o cliente.
     *
     * @return \Illuminate\Http\Response
     */
    public function catalog(Request $request)
    {
        $categories = $this->bookCategoryService->getAllCategories();
        $category_id = $request->input('category_id');
        
        $booksQuery = Book::with('category');
        
        if ($category_id) {
            $booksQuery->where('category_id', $category_id);
        }
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $booksQuery->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }
        
        $books = $booksQuery->where('active', true)->paginate(12);
        
        return view('customer.catalog', compact('books', 'categories', 'category_id'));
    }

    /**
     * Exibe os detalhes de um livro.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function bookDetails(Book $book)
    {
        $relatedBooks = $this->bookService->getRelatedBooks($book->id, 4);
        return view('customer.book-details', compact('book', 'relatedBooks'));
    }

    /**
     * Adiciona um livro ao carrinho.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request)
    {
        $bookId = $request->input('book_id');
        $quantity = $request->input('quantity', 1);
        
        $book = Book::findOrFail($bookId);
        
        $cart = Session::get('cart', []);
        
        if (isset($cart[$bookId])) {
            $cart[$bookId]['quantity'] += $quantity;
        } else {
            $cart[$bookId] = [
                'id' => $book->id,
                'title' => $book->title,
                'price' => $book->price,
                'quantity' => $quantity,
                'cover' => $book->cover_image
            ];
        }
        
        Session::put('cart', $cart);
        
        return redirect()->back()->with('success', 'Livro adicionado ao carrinho!');
    }

    /**
     * Exibe o carrinho de compras.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewCart()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return view('customer.cart', compact('cart', 'total'));
    }

    /**
     * Atualiza a quantidade de um item no carrinho.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCart(Request $request)
    {
        $cart = Session::get('cart', []);
        $bookId = $request->input('book_id');
        $quantity = $request->input('quantity');
        
        if ($quantity <= 0) {
            unset($cart[$bookId]);
        } else {
            $cart[$bookId]['quantity'] = $quantity;
        }
        
        Session::put('cart', $cart);
        
        return redirect()->route('customer.cart')->with('success', 'Carrinho atualizado!');
    }

    /**
     * Remove um item do carrinho.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeFromCart(Request $request)
    {
        $cart = Session::get('cart', []);
        $bookId = $request->input('book_id');
        
        if (isset($cart[$bookId])) {
            unset($cart[$bookId]);
            Session::put('cart', $cart);
        }
        
        return redirect()->route('customer.cart')->with('success', 'Item removido do carrinho!');
    }

    /**
     * Finaliza o pedido e cria uma fatura.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkout(Request $request)
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('customer.catalog')->with('error', 'Seu carrinho está vazio!');
        }
        
        $customer = Customer::where('user_id', Auth::id())->first();
        
        if (!$customer) {
            return redirect()->route('customer.profile.create')->with('info', 'Por favor, complete seu perfil para continuar.');
        }
        
        // Criar a fatura
        $invoiceData = [
            'customer_id' => $customer->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'pending',
            'notes' => 'Pedido realizado pelo portal do cliente',
            'payment_method' => $request->input('payment_method', 'pending'),
            'user_id' => Auth::id()
        ];
        
        $invoice = $this->invoiceService->createInvoice($invoiceData);
        
        // Adicionar itens à fatura
        foreach ($cart as $item) {
            $itemData = [
                'invoice_id' => $invoice->id,
                'book_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'description' => $item['title']
            ];
            
            $this->invoiceService->addInvoiceItem($itemData);
        }
        
        // Limpar o carrinho
        Session::forget('cart');
        
        return redirect()->route('customer.orders')->with('success', 'Pedido realizado com sucesso! Sua fatura foi gerada.');
    }

    /**
     * Exibe os pedidos/faturas do cliente.
     *
     * @return \Illuminate\Http\Response
     */
    public function orders()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        
        if (!$customer) {
            return redirect()->route('customer.profile.create')->with('info', 'Por favor, complete seu perfil para continuar.');
        }
        
        $invoices = Invoice::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('customer.orders', compact('invoices'));
    }

    /**
     * Exibe os detalhes de um pedido/fatura.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function orderDetails(Invoice $invoice)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        
        if ($invoice->customer_id != $customer->id) {
            return redirect()->route('customer.orders')->with('error', 'Você não tem permissão para visualizar esta fatura.');
        }
        
        return view('customer.order-details', compact('invoice'));
    }

    /**
     * Exibe o perfil do cliente.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        
        if (!$customer) {
            return redirect()->route('customer.profile.create')->with('info', 'Por favor, complete seu perfil para continuar.');
        }
        
        return view('customer.profile', compact('customer'));
    }

    /**
     * Exibe o formulário para criar o perfil do cliente.
     *
     * @return \Illuminate\Http\Response
     */
    public function createProfile()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        
        if ($customer) {
            return redirect()->route('customer.profile')->with('info', 'Você já possui um perfil.');
        }
        
        return view('customer.create-profile');
    }

    /**
     * Armazena o perfil do cliente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'tax_id' => 'required|string|max:20',
        ]);
        
        $customerData = $request->all();
        $customerData['user_id'] = Auth::id();
        
        $customer = $this->customerService->createCustomer($customerData);
        
        return redirect()->route('customer.dashboard')->with('success', 'Perfil criado com sucesso!');
    }

    /**
     * Exibe o formulário para editar o perfil do cliente.
     *
     * @return \Illuminate\Http\Response
     */
    public function editProfile()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        
        if (!$customer) {
            return redirect()->route('customer.profile.create')->with('info', 'Por favor, complete seu perfil para continuar.');
        }
        
        return view('customer.edit-profile', compact('customer'));
    }

    /**
     * Atualiza o perfil do cliente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        
        if (!$customer) {
            return redirect()->route('customer.profile.create')->with('info', 'Por favor, complete seu perfil para continuar.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'tax_id' => 'required|string|max:20',
        ]);
        
        $customerData = $request->all();
        
        $this->customerService->updateCustomer($customer->id, $customerData);
        
        return redirect()->route('customer.profile')->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Exibe os pontos de fidelidade do cliente.
     *
     * @return \Illuminate\Http\Response
     */
    public function loyaltyPoints()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        
        if (!$customer) {
            return redirect()->route('customer.profile.create')->with('info', 'Por favor, complete seu perfil para continuar.');
        }
        
        $loyaltyPoints = $this->loyaltyService->getCustomerPoints($customer->id);
        $transactions = $this->loyaltyService->getCustomerTransactions($customer->id);
        
        return view('customer.loyalty-points', compact('loyaltyPoints', 'transactions'));
    }
}
