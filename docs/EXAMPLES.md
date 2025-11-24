# 💡 Exemplos Práticos de Código

Este documento contém exemplos práticos de como usar e estender as funcionalidades do CRM Livraria.

## Índice

- [Services](#services)
- [Controllers](#controllers)
- [Models](#models)
- [Livewire Components](#livewire-components)
- [Blade Templates](#blade-templates)
- [Jobs e Filas](#jobs-e-filas)
- [Eventos e Listeners](#eventos-e-listeners)
- [Testes](#testes)

---

## Services

### Exemplo 1: Criar um Novo Service

```php
<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Criar novo produto
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'name' => $data['name'],
                'price' => $data['price'],
                'stock' => $data['stock'] ?? 0,
            ]);
            
            // Lógica adicional aqui
            
            return $product;
        });
    }
    
    /**
     * Atualizar estoque
     */
    public function updateStock(Product $product, int $quantity, string $type = 'add'): void
    {
        if ($type === 'add') {
            $product->increment('stock', $quantity);
        } else {
            $product->decrement('stock', $quantity);
        }
    }
    
    /**
     * Buscar produtos com estoque baixo
     */
    public function getLowStock(int $threshold = 10): Collection
    {
        return Product::where('stock', '<=', $threshold)
            ->where('is_active', true)
            ->get();
    }
}
```

### Exemplo 2: Usar Service no Controller

```php
<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);
        
        try {
            $product = $this->productService->create($validated);
            
            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Produto criado com sucesso!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors('Erro ao criar produto: ' . $e->getMessage());
        }
    }
}
```

---

## Controllers

### Exemplo 1: Controller RESTful Completo

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}
    
    /**
     * Listar todas as categorias
     */
    public function index()
    {
        $categories = Category::with('books')
            ->withCount('books')
            ->paginate(20);
            
        return view('categories.index', compact('categories'));
    }
    
    /**
     * Mostrar formulário de criação
     */
    public function create()
    {
        return view('categories.create');
    }
    
    /**
     * Salvar nova categoria
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);
        
        $category = $this->categoryService->create($validated);
        
        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }
    
    /**
     * Mostrar detalhes da categoria
     */
    public function show(Category $category)
    {
        $category->load(['books' => function ($query) {
            $query->where('is_active', true);
        }]);
        
        return view('categories.show', compact('category'));
    }
    
    /**
     * Mostrar formulário de edição
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }
    
    /**
     * Atualizar categoria
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);
        
        $this->categoryService->update($category, $validated);
        
        return redirect()
            ->route('categories.show', $category)
            ->with('success', 'Categoria atualizada com sucesso!');
    }
    
    /**
     * Deletar categoria
     */
    public function destroy(Category $category)
    {
        if ($category->books()->count() > 0) {
            return back()->withErrors('Não é possível deletar categoria com livros associados.');
        }
        
        $category->delete();
        
        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoria deletada com sucesso!');
    }
}
```

### Exemplo 2: Controller com AJAX

```php
<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookSearchController extends Controller
{
    /**
     * Buscar livros via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $books = Book::where('title', 'like', "%{$query}%")
            ->orWhere('author', 'like', "%{$query}%")
            ->orWhere('isbn', 'like', "%{$query}%")
            ->with('category')
            ->limit(10)
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $books,
        ]);
    }
    
    /**
     * Verificar disponibilidade de estoque
     */
    public function checkStock(Book $book)
    {
        return response()->json([
            'available' => $book->stock_quantity > 0,
            'quantity' => $book->stock_quantity,
        ]);
    }
}
```

---

## Models

### Exemplo 1: Model com Relacionamentos

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'customer_id',
        'order_number',
        'subtotal',
        'discount',
        'total',
        'status',
        'notes',
    ];
    
    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
    ];
    
    // Relacionamentos
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    // Accessors
    public function getFormattedTotalAttribute(): string
    {
        return '€' . number_format($this->total, 2);
    }
    
    // Mutators
    public function setOrderNumberAttribute($value)
    {
        $this->attributes['order_number'] = strtoupper($value);
    }
    
    // Métodos auxiliares
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }
}
```

### Exemplo 2: Model com Eventos

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_number',
        'total',
        'status',
    ];
    
    // Boot method para eventos
    protected static function boot()
    {
        parent::boot();
        
        // Antes de criar
        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
        
        // Depois de criar
        static::created(function ($invoice) {
            // Disparar evento
            event(new \App\Events\InvoiceCreated($invoice));
        });
        
        // Antes de atualizar
        static::updating(function ($invoice) {
            if ($invoice->isDirty('status') && $invoice->status === 'paid') {
                $invoice->paid_at = now();
            }
        });
    }
    
    private static function generateInvoiceNumber(): string
    {
        $lastInvoice = static::latest('id')->first();
        $number = $lastInvoice ? $lastInvoice->id + 1 : 1;
        
        return 'INV-' . date('Y') . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
```

---

## Livewire Components

### Exemplo 1: Componente de Carrinho

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Book;

class ShoppingCart extends Component
{
    public $cart = [];
    public $total = 0;
    
    protected $listeners = ['productAdded' => 'addToCart'];
    
    public function mount()
    {
        $this->cart = session()->get('cart', []);
        $this->calculateTotal();
    }
    
    public function addToCart($bookId)
    {
        $book = Book::find($bookId);
        
        if (!$book || $book->stock_quantity < 1) {
            $this->dispatch('error', 'Produto indisponível');
            return;
        }
        
        if (isset($this->cart[$bookId])) {
            $this->cart[$bookId]['quantity']++;
        } else {
            $this->cart[$bookId] = [
                'id' => $book->id,
                'title' => $book->title,
                'price' => $book->price,
                'quantity' => 1,
            ];
        }
        
        $this->saveCart();
        $this->dispatch('success', 'Produto adicionado ao carrinho');
    }
    
    public function updateQuantity($bookId, $quantity)
    {
        if ($quantity < 1) {
            $this->removeFromCart($bookId);
            return;
        }
        
        $this->cart[$bookId]['quantity'] = $quantity;
        $this->saveCart();
    }
    
    public function removeFromCart($bookId)
    {
        unset($this->cart[$bookId]);
        $this->saveCart();
        $this->dispatch('success', 'Produto removido do carrinho');
    }
    
    public function clearCart()
    {
        $this->cart = [];
        $this->saveCart();
        $this->dispatch('success', 'Carrinho limpo');
    }
    
    private function saveCart()
    {
        session()->put('cart', $this->cart);
        $this->calculateTotal();
    }
    
    private function calculateTotal()
    {
        $this->total = collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }
    
    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
```

### Exemplo 2: Componente de Busca em Tempo Real

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;

class CustomerSearch extends Component
{
    public $search = '';
    public $selectedCustomer = null;
    
    public function updatedSearch()
    {
        // Executado automaticamente quando $search muda
    }
    
    public function selectCustomer($customerId)
    {
        $this->selectedCustomer = Customer::find($customerId);
        $this->dispatch('customerSelected', $customerId);
    }
    
    public function clearSelection()
    {
        $this->selectedCustomer = null;
        $this->search = '';
    }
    
    public function render()
    {
        $customers = [];
        
        if (strlen($this->search) >= 2) {
            $customers = Customer::where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->limit(10)
                ->get();
        }
        
        return view('livewire.customer-search', [
            'customers' => $customers,
        ]);
    }
}
```

---

## Blade Templates

### Exemplo 1: Layout Base

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CRM Livraria')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100">
    {{-- Header --}}
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold">
                    CRM Livraria
                </a>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <span>{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary">
                                Sair
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    
    {{-- Main Content --}}
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>
    
    {{-- Footer --}}
    <footer class="bg-white shadow mt-8">
        <div class="container mx-auto px-4 py-4 text-center text-gray-600">
            &copy; {{ date('Y') }} CRM Livraria. Todos os direitos reservados.
        </div>
    </footer>
    
    @livewireScripts
    @stack('scripts')
</body>
</html>
```

### Exemplo 2: Componente Blade Reutilizável

```blade
{{-- resources/views/components/button.blade.php --}}
@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
])

@php
$classes = [
    'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
    'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white',
    'success' => 'bg-green-600 hover:bg-green-700 text-white',
];

$sizes = [
    'sm' => 'px-3 py-1 text-sm',
    'md' => 'px-4 py-2',
    'lg' => 'px-6 py-3 text-lg',
];

$class = $classes[$variant] . ' ' . $sizes[$size];
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "rounded font-medium transition-colors $class"]) }}
>
    {{ $slot }}
</button>
```

**Uso:**

```blade
<x-button variant="primary" size="lg">
    Salvar
</x-button>

<x-button variant="danger" onclick="confirmDelete()">
    Deletar
</x-button>
```

---

## Jobs e Filas

### Exemplo 1: Job para Enviar Email

```php
<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 3;
    public $timeout = 120;
    
    public function __construct(
        public Campaign $campaign,
        public Customer $customer,
        public string $trackingToken
    ) {}
    
    public function handle()
    {
        Mail::to($this->customer->email)->send(
            new \App\Mail\CampaignEmail(
                $this->campaign,
                $this->customer,
                $this->trackingToken
            )
        );
        
        // Atualizar status
        $this->campaign->customers()->updateExistingPivot($this->customer->id, [
            'email_sent_at' => now(),
        ]);
    }
    
    public function failed(\Throwable $exception)
    {
        // Log do erro
        \Log::error('Falha ao enviar email de campanha', [
            'campaign_id' => $this->campaign->id,
            'customer_id' => $this->customer->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
```

**Despachar o Job:**

```php
SendCampaignEmail::dispatch($campaign, $customer, $token);

// Ou com delay
SendCampaignEmail::dispatch($campaign, $customer, $token)
    ->delay(now()->addMinutes(5));

// Ou em fila específica
SendCampaignEmail::dispatch($campaign, $customer, $token)
    ->onQueue('emails');
```

---

## Eventos e Listeners

### Exemplo 1: Evento e Listener

```php
<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoicePaid
{
    use Dispatchable, SerializesModels;
    
    public function __construct(
        public Invoice $invoice
    ) {}
}
```

```php
<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Services\LoyaltyService;
use App\Services\NotificationService;

class ProcessInvoicePayment
{
    public function __construct(
        private LoyaltyService $loyaltyService,
        private NotificationService $notificationService
    ) {}
    
    public function handle(InvoicePaid $event)
    {
        $invoice = $event->invoice;
        
        // Adicionar pontos de fidelidade
        $this->loyaltyService->addPoints(
            $invoice->customer,
            (int) $invoice->total,
            "Compra - Fatura #{$invoice->invoice_number}",
            $invoice
        );
        
        // Enviar notificação
        $this->notificationService->create(
            $invoice->customer->user,
            'Fatura Paga',
            "Sua fatura #{$invoice->invoice_number} foi paga com sucesso!",
            'invoice',
            route('invoices.show', $invoice)
        );
    }
}
```

**Registrar no EventServiceProvider:**

```php
protected $listen = [
    InvoicePaid::class => [
        ProcessInvoicePayment::class,
    ],
];
```

**Disparar o Evento:**

```php
event(new InvoicePaid($invoice));
```

---

## Testes

### Exemplo 1: Teste de Feature

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function admin_can_create_customer()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->post('/customers', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'phone' => '123456789',
        ]);
        
        $response->assertRedirect('/customers');
        $this->assertDatabaseHas('customers', [
            'email' => 'joao@example.com',
        ]);
    }
    
    /** @test */
    public function customer_creation_requires_name_and_email()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->post('/customers', []);
        
        $response->assertSessionHasErrors(['name', 'email']);
    }
    
    /** @test */
    public function non_admin_cannot_create_customer()
    {
        $user = User::factory()->create(['role' => 'customer']);
        
        $response = $this->actingAs($user)->post('/customers', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);
        
        $response->assertStatus(403);
    }
}
```

### Exemplo 2: Teste de Service

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\LoyaltyService;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoyaltyServiceTest extends TestCase
{
    use RefreshDatabase;
    
    private LoyaltyService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(LoyaltyService::class);
    }
    
    /** @test */
    public function it_can_add_points_to_customer()
    {
        $customer = Customer::factory()->create();
        
        $this->service->addPoints($customer, 100, 'Test points');
        
        $this->assertEquals(100, $this->service->getBalance($customer));
        $this->assertDatabaseHas('loyalty_transactions', [
            'customer_id' => $customer->id,
            'points' => 100,
            'type' => 'earn',
        ]);
    }
    
    /** @test */
    public function it_can_redeem_points()
    {
        $customer = Customer::factory()->create();
        $this->service->addPoints($customer, 200, 'Initial points');
        
        $invoice = \App\Models\Invoice::factory()->create([
            'customer_id' => $customer->id,
            'total' => 100,
        ]);
        
        $discount = $this->service->redeemPoints($customer, 100, $invoice);
        
        $this->assertEquals(10.0, $discount); // 100 points = 10€
        $this->assertEquals(100, $this->service->getBalance($customer));
    }
}
```

---

## Conclusão

Estes exemplos demonstram os padrões e práticas recomendadas para desenvolvimento no CRM Livraria. Use-os como referência ao adicionar novas funcionalidades ou modificar existentes.

Para mais informações, consulte:
- [Documentação de Arquitetura](ARCHITECTURE.md)
- [Documentação de Módulos](MODULES.md)
- [Guia de Contribuição](../CONTRIBUTING.md)
