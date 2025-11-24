# 🏗️ Arquitetura do Sistema

## Visão Geral

O CRM Livraria segue o padrão **Service Layer Architecture**, separando claramente as responsabilidades entre camadas e promovendo código limpo, testável e manutenível.

## Padrão Arquitetural

```
┌─────────────────────────────────────────────────────────┐
│                    Presentation Layer                    │
│  (Controllers, Livewire Components, Blade Views)        │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                    Service Layer                         │
│  (Business Logic, Orchestration, Transactions)          │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                    Data Layer                            │
│  (Models, Eloquent ORM, Database)                       │
└─────────────────────────────────────────────────────────┘
```

## Camadas da Aplicação

### 1. Presentation Layer (Camada de Apresentação)

**Responsabilidades:**
- Receber requisições HTTP
- Validar inputs do usuário
- Delegar lógica de negócio para Services
- Retornar respostas (views, JSON, redirects)

**Componentes:**

#### Controllers
Localizados em `app/Http/Controllers/`

```php
// Exemplo: InvoiceController.php
public function store(Request $request)
{
    // 1. Validação
    $validated = $request->validate([...]);
    
    // 2. Delegação para Service
    $invoice = $this->invoiceService->create($validated);
    
    // 3. Resposta
    return redirect()->route('invoices.show', $invoice);
}
```

**Princípios:**
- Controllers devem ser **finos** (thin controllers)
- Não contêm lógica de negócio
- Apenas orquestram o fluxo HTTP
- Usam Form Requests para validação complexa

#### Livewire Components
Localizados em `app/Livewire/`

```php
// Exemplo: CartComponent.php
class CartComponent extends Component
{
    public function addToCart($bookId)
    {
        $this->cartService->add($bookId);
        $this->emit('cartUpdated');
    }
}
```

**Características:**
- Componentes reativos para UI dinâmica
- Comunicação em tempo real com o backend
- Gerenciam estado do frontend

#### Blade Templates
Localizados em `resources/views/`

- **Layouts**: Templates base (`app.blade.php`, `customer.blade.php`)
- **Components**: Componentes reutilizáveis (botões, cards, modais)
- **Views**: Páginas específicas de cada módulo

### 2. Service Layer (Camada de Serviços)

**Responsabilidades:**
- Implementar regras de negócio
- Orquestrar operações complexas
- Gerenciar transações de banco de dados
- Integrar múltiplos models
- Disparar eventos e notificações

**Localização:** `app/Services/`

#### Estrutura de um Service

```php
namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Criar nova fatura com itens e processar pontos de fidelidade
     */
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // 1. Criar fatura
            $invoice = Invoice::create([...]);
            
            // 2. Adicionar itens
            foreach ($data['items'] as $item) {
                $invoice->items()->create($item);
            }
            
            // 3. Atualizar estoque
            $this->updateStock($invoice);
            
            // 4. Processar pontos de fidelidade
            $this->loyaltyService->addPoints($invoice);
            
            // 5. Enviar notificação
            $this->notificationService->notifyInvoiceCreated($invoice);
            
            return $invoice;
        });
    }
    
    /**
     * Atualizar status da fatura
     */
    public function updateStatus(Invoice $invoice, string $status): Invoice
    {
        $oldStatus = $invoice->status;
        
        $invoice->update(['status' => $status]);
        
        // Lógica específica por status
        if ($status === 'paid' && $oldStatus !== 'paid') {
            $this->loyaltyService->addPoints($invoice);
        }
        
        if ($status === 'cancelled' && $oldStatus === 'paid') {
            $this->loyaltyService->removePoints($invoice);
        }
        
        return $invoice;
    }
}
```

#### Services Implementados

##### BookService
- Gerenciamento de livros
- Controle de estoque
- Cálculo de preços com desconto

##### CustomerService
- CRUD de clientes
- Análise de comportamento
- Segmentação para campanhas

##### InvoiceService
- Criação e gestão de faturas
- Processamento de pagamentos
- Integração com estoque e fidelidade

##### LoyaltyService
- Gestão de pontos de fidelidade
- Ganho automático em compras
- Resgate de pontos
- Expiração automática

##### CampaignService
- Criação de campanhas
- Seleção de clientes (manual/automática)
- Envio de emails
- Rastreamento de métricas

##### NotificationService
- Criação de notificações
- Envio para usuários específicos
- Marcação de leitura

##### RecommendationService
- Algoritmos de recomendação
- Livros populares
- Recomendações personalizadas

### 3. Data Layer (Camada de Dados)

**Responsabilidades:**
- Representar entidades do banco de dados
- Definir relacionamentos
- Implementar scopes e accessors
- Validação básica de dados

**Localização:** `app/Models/`

#### Estrutura de um Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    // 1. Configuração básica
    protected $fillable = ['name', 'email', 'phone', 'address'];
    
    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];
    
    // 2. Relacionamentos
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
    
    public function loyaltyPoints(): HasOne
    {
        return $this->hasOne(LoyaltyPoint::class);
    }
    
    // 3. Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    // 4. Accessors/Mutators
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->email})";
    }
}
```

**Princípios:**
- Models devem ser **magros** (thin models)
- Apenas relacionamentos e scopes
- Sem lógica de negócio complexa
- Seguem convenções Eloquent

## Fluxo de Dados

### Exemplo: Criação de Fatura

```
1. User submits form
   ↓
2. InvoiceController@store
   - Valida dados
   ↓
3. InvoiceService@create
   - Inicia transação DB
   - Cria Invoice
   - Adiciona InvoiceItems
   - Atualiza estoque (BookService)
   - Adiciona pontos (LoyaltyService)
   - Envia notificação (NotificationService)
   - Commit transação
   ↓
4. Controller retorna resposta
   - Redirect com mensagem de sucesso
```

## Transações de Banco de Dados

Todas as operações complexas que envolvem múltiplas tabelas são envolvidas em transações:

```php
use Illuminate\Support\Facades\DB;

public function complexOperation(array $data)
{
    return DB::transaction(function () use ($data) {
        // Operação 1
        $model1 = Model1::create($data1);
        
        // Operação 2
        $model2 = Model2::create($data2);
        
        // Se qualquer operação falhar, rollback automático
        
        return $model1;
    });
}
```

## Eventos e Listeners

O sistema utiliza eventos para desacoplar funcionalidades:

```php
// Disparar evento
event(new InvoiceCreated($invoice));

// Listener
class SendInvoiceNotification
{
    public function handle(InvoiceCreated $event)
    {
        // Enviar notificação
    }
}
```

## Jobs e Filas

Tarefas demoradas são processadas em background:

```php
// Despachar job
SendCampaignEmails::dispatch($campaign);

// Job
class SendCampaignEmails implements ShouldQueue
{
    public function handle()
    {
        // Processar envio de emails
    }
}
```

## Middleware

### AdminMiddleware
Restringe acesso ao painel administrativo:

```php
public function handle($request, Closure $next)
{
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Acesso negado');
    }
    
    return $next($request);
}
```

### CustomerMiddleware
Protege rotas do portal do cliente:

```php
public function handle($request, Closure $next)
{
    if (!auth()->user()->isCustomer()) {
        abort(403, 'Acesso negado');
    }
    
    return $next($request);
}
```

## Validação

### Form Requests
Para validações complexas:

```php
namespace App\Http\Requests;

class StoreInvoiceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
    
    public function messages()
    {
        return [
            'customer_id.required' => 'Selecione um cliente',
            'items.required' => 'Adicione pelo menos um item',
        ];
    }
}
```

## Boas Práticas

### 1. Single Responsibility Principle (SRP)
Cada classe tem uma única responsabilidade:
- Controllers → HTTP
- Services → Lógica de negócio
- Models → Representação de dados

### 2. Dependency Injection
Injetar dependências via construtor:

```php
class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService,
        private CustomerService $customerService
    ) {}
}
```

### 3. Interface Segregation
Usar interfaces quando necessário:

```php
interface PaymentGateway
{
    public function charge(float $amount): bool;
}

class StripeGateway implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        // Implementação Stripe
    }
}
```

### 4. Tratamento de Erros

```php
try {
    $invoice = $this->invoiceService->create($data);
} catch (\Exception $e) {
    Log::error('Erro ao criar fatura: ' . $e->getMessage());
    return back()->withErrors('Erro ao criar fatura');
}
```

## Performance

### 1. Eager Loading
Evitar N+1 queries:

```php
// ❌ Ruim
$invoices = Invoice::all();
foreach ($invoices as $invoice) {
    echo $invoice->customer->name; // N+1 query
}

// ✅ Bom
$invoices = Invoice::with('customer')->get();
foreach ($invoices as $invoice) {
    echo $invoice->customer->name; // 1 query
}
```

### 2. Caching
Cachear dados frequentemente acessados:

```php
$popularBooks = Cache::remember('popular_books', 3600, function () {
    return Book::orderBy('sales_count', 'desc')->take(10)->get();
});
```

### 3. Paginação
Sempre paginar grandes conjuntos de dados:

```php
$customers = Customer::paginate(20);
```

## Testes

### Unit Tests
Testar Services isoladamente:

```php
public function test_invoice_creation()
{
    $service = new InvoiceService();
    $invoice = $service->create([...]);
    
    $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);
}
```

### Feature Tests
Testar fluxos completos:

```php
public function test_user_can_create_invoice()
{
    $this->actingAs($admin)
         ->post('/invoices', $data)
         ->assertRedirect('/invoices');
}
```

## Segurança

### 1. Mass Assignment Protection
Sempre definir `$fillable` ou `$guarded`:

```php
protected $fillable = ['name', 'email'];
```

### 2. SQL Injection Prevention
Usar Query Builder ou Eloquent:

```php
// ✅ Seguro
Customer::where('email', $email)->first();

// ❌ Inseguro
DB::select("SELECT * FROM customers WHERE email = '$email'");
```

### 3. CSRF Protection
Sempre incluir `@csrf` em formulários:

```blade
<form method="POST">
    @csrf
    ...
</form>
```

## Conclusão

Esta arquitetura promove:
- **Separação de responsabilidades**
- **Código testável e manutenível**
- **Escalabilidade**
- **Reutilização de código**
- **Facilidade de manutenção**

Ao adicionar novas funcionalidades, sempre siga estes padrões para manter a consistência e qualidade do código.
