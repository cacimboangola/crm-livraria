# 📚 Sistema de Acompanhamento de Pedidos Especiais

## Visão Geral

O Sistema de Acompanhamento de Pedidos Especiais é uma funcionalidade completa que permite aos clientes solicitarem livros que não estão em estoque e acompanharem o progresso de seus pedidos em tempo real, com notificações automáticas a cada mudança de status.

## Objetivos

- ✅ **Transparência Total**: Cliente sabe exatamente onde está seu pedido
- ✅ **Comunicação Automática**: Notificações sem necessidade de contato manual
- ✅ **Experiência Superior**: Interface intuitiva e informativa
- ✅ **Eficiência Operacional**: Redução de contatos de suporte
- ✅ **Satisfação do Cliente**: Confiança através da transparência

---

## Fluxo Completo do Sistema

### 1. Solicitação do Pedido

#### **Via Chatbot (Recomendado)**
```
Cliente: "Preciso do livro O Código Da Vinci"
Chatbot: Detecta que não está em estoque
Chatbot: Oferece formulário de pedido especial
Cliente: Preenche dados (título, autor, quantidade, etc.)
Sistema: Cria pedido com status "pending"
```

#### **Via Interface Admin**
```
Admin: Acessa painel de pedidos especiais
Admin: Clica em "Novo Pedido"
Admin: Preenche dados do cliente e livro
Sistema: Cria pedido com status "pending"
```

### 2. Acompanhamento pelo Cliente

#### **Interface Web**
- **Dashboard**: Estatísticas visuais dos pedidos
- **Lista**: Cards com informações resumidas
- **Detalhes**: Timeline completa com status
- **Filtros**: Por status, data, etc.

#### **Via Chatbot**
```
Cliente: "Meus pedidos especiais"
Chatbot: Mostra resumo com últimos 5 pedidos
Chatbot: Oferece "Ver detalhes completos"
Cliente: Clica e é redirecionado para a página web
```

### 3. Gestão pelo Admin

#### **Painel Administrativo**
- **Lista Completa**: Todos os pedidos com filtros
- **Detalhes**: Informações completas do pedido
- **Ações**: Avançar status, cancelar, editar
- **Métricas**: Tempo médio, taxa de conversão

### 4. Notificações Automáticas

#### **Quando Status Muda**
```
Admin: Avança status no painel
Sistema: Detecta mudança automaticamente
Sistema: Cria notificação na tabela notifications
Sistema: Envia email (opcional, para status específicos)
Cliente: Recebe notificação com link direto
```

---

## Estados do Pedido

### 1. 📋 **Pending** (Aguardando Encomenda)
- **Descrição**: Pedido criado, aguardando ação do admin
- **Ações Disponíveis**: Avançar para "ordered", cancelar
- **Notificação**: Não (status inicial)

### 2. 📦 **Ordered** (Encomendado ao Fornecedor)
- **Descrição**: Livro foi encomendado ao fornecedor
- **Timestamp**: `ordered_at`
- **Ações Disponíveis**: Avançar para "received", cancelar
- **Notificação**: ✅ "Pedido Especial Encomendado! 📦"

### 3. ✅ **Received** (Recebido na Loja)
- **Descrição**: Livro chegou na loja, sendo preparado
- **Timestamp**: `received_at`
- **Ações Disponíveis**: Avançar para "notified"
- **Notificação**: ✅ "Livro Chegou na Loja! ✅"

### 4. 🔔 **Notified** (Pronto para Retirada/Entrega)
- **Descrição**: Cliente foi notificado que pode retirar/receber
- **Timestamp**: `notified_at`
- **Ações Disponíveis**: Avançar para "delivered"
- **Notificação**: ✅ "Seu Livro Está Pronto! 🎉"
- **Email**: ✅ Enviado automaticamente

### 5. 🎉 **Delivered** (Entregue/Retirado)
- **Descrição**: Pedido concluído com sucesso
- **Timestamp**: `delivered_at`
- **Ações Disponíveis**: Nenhuma (status final)
- **Notificação**: ✅ "Pedido Especial Concluído! 🎊"

### 6. ❌ **Cancelled** (Cancelado)
- **Descrição**: Pedido cancelado (por admin ou cliente)
- **Timestamp**: `cancelled_at`
- **Ações Disponíveis**: Nenhuma (status final)
- **Notificação**: ✅ "Pedido Especial Cancelado"

---

## Arquitetura Técnica

### Modelos de Dados

#### **SpecialOrder Model**
```php
class SpecialOrder extends Model
{
    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ORDERED = 'ordered';
    const STATUS_RECEIVED = 'received';
    const STATUS_NOTIFIED = 'notified';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    
    // Timestamps específicos
    protected $dates = [
        'ordered_at',
        'received_at', 
        'notified_at',
        'delivered_at',
        'cancelled_at'
    ];
    
    // Relacionamentos
    public function customer(): BelongsTo;
    
    // Accessors
    public function getStatusFormattedAttribute(): string;
    public function getCanCancelAttribute(): bool;
}
```

#### **Notification Model**
```php
class Notification extends Model
{
    // Tipos de notificação
    const TYPE_SPECIAL_ORDER_STATUS = 'special_order_status';
    
    // Campos
    protected $fillable = [
        'user_id',
        'sender_id', 
        'type',
        'title',
        'message',
        'link',
        'read'
    ];
}
```

### Controllers

#### **Customer\SpecialOrderController**
```php
class SpecialOrderController extends Controller
{
    public function index(): View;           // Lista pedidos do cliente
    public function show(int $id): View;     // Detalhes do pedido
    public function cancel(int $id): Response; // Cancelar pedido
}
```

#### **SpecialOrderController (Admin)**
```php
class SpecialOrderController extends Controller
{
    public function index(): View;                    // Lista todos os pedidos
    public function show(SpecialOrder $order): View;  // Detalhes do pedido
    public function advanceStatus(SpecialOrder $order): Response; // Avançar status
    public function cancel(SpecialOrder $order): Response;        // Cancelar
    
    // Método principal de notificação
    protected function notifyCustomerStatusChange(
        SpecialOrder $specialOrder, 
        string $newStatus
    ): void;
}
```

#### **Api\ChatbotController**
```php
class ChatbotController extends Controller
{
    // Consultar pedidos especiais
    private function handleSpecialOrderQuery(): array;
    
    // Criar pedido especial via chatbot
    public function createSpecialOrder(Request $request): JsonResponse;
}
```

### Views (Blade Templates)

#### **Interface do Cliente**
```
resources/views/customer/special-orders/
├── index.blade.php      # Lista de pedidos
└── show.blade.php       # Detalhes do pedido
```

#### **Interface Admin**
```
resources/views/special-orders/
├── index.blade.php      # Lista administrativa
├── show.blade.php       # Detalhes administrativos
└── create.blade.php     # Criar novo pedido
```

### JavaScript

#### **Chatbot Integration**
```javascript
// public/js/chatbot.js
class Chatbot {
    // Tratar consulta de pedidos especiais
    handleSpecialOrderQuery();
    
    // Redirecionar para página de detalhes
    redirectToSpecialOrders();
    
    // Formulário de criação via chat
    showSpecialOrderForm();
}
```

---

## Rotas

### **Cliente (Autenticado)**
```php
Route::middleware(['auth', 'customer'])->prefix('cliente')->group(function () {
    Route::get('/pedidos-especiais', [SpecialOrderController::class, 'index'])
         ->name('customer.special-orders.index');
         
    Route::get('/pedidos-especiais/{id}', [SpecialOrderController::class, 'show'])
         ->name('customer.special-orders.show');
         
    Route::patch('/pedidos-especiais/{id}/cancelar', [SpecialOrderController::class, 'cancel'])
         ->name('customer.special-orders.cancel');
});
```

### **Admin (Autenticado)**
```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('special-orders', SpecialOrderController::class);
    
    Route::patch('/special-orders/{specialOrder}/advance-status', 
                [SpecialOrderController::class, 'advanceStatus'])
         ->name('special-orders.advance-status');
});
```

### **API (Chatbot)**
```php
Route::prefix('api')->group(function () {
    Route::post('/chatbot', [ChatbotController::class, 'handleMessage']);
    Route::post('/chatbot/special-order', [ChatbotController::class, 'createSpecialOrder'])
         ->middleware('auth');
});
```

---

## Funcionalidades Detalhadas

### Dashboard do Cliente

#### **Estatísticas Visuais**
```php
$stats = [
    'total' => $specialOrders->count(),
    'pending' => $specialOrders->where('status', 'pending')->count(),
    'active' => $specialOrders->whereNotIn('status', ['delivered', 'cancelled'])->count(),
    'delivered' => $specialOrders->where('status', 'delivered')->count()
];
```

#### **Cards de Pedidos**
- **Informações Resumidas**: Título, autor, quantidade, status, data
- **Badges de Status**: Cores diferentes para cada status
- **Ações Rápidas**: Ver detalhes, cancelar (se permitido)

### Timeline Visual

#### **Estrutura da Timeline**
```php
$timeline = [
    [
        'status' => 'pending',
        'label' => 'Pedido Criado',
        'completed' => true,
        'date' => $order->created_at,
        'icon' => 'fas fa-plus-circle'
    ],
    [
        'status' => 'ordered', 
        'label' => 'Encomendado ao Fornecedor',
        'completed' => $order->ordered_at !== null,
        'date' => $order->ordered_at,
        'icon' => 'fas fa-shipping-fast'
    ],
    // ... outros status
];
```

#### **Indicadores Visuais**
- ✅ **Concluído**: Ícone verde, data preenchida
- ⏳ **Aguardando**: Ícone cinza, "Aguardando..."
- 🔄 **Em Progresso**: Ícone azul, animação (opcional)

### Sistema de Notificações

#### **Criação Automática**
```php
protected function notifyCustomerStatusChange(SpecialOrder $specialOrder, string $newStatus): void
{
    $customer = $specialOrder->customer;
    $user = User::where('email', $customer->email)->first();
    
    if ($user) {
        $statusMessages = [
            'ordered' => [
                'title' => 'Pedido Especial Encomendado! 📦',
                'message' => "Seu pedido especial \"{$specialOrder->book_title}\" foi encomendado ao fornecedor."
            ],
            // ... outros status
        ];
        
        if (isset($statusMessages[$newStatus])) {
            Notification::create([
                'user_id' => $user->id,
                'sender_id' => Auth::id(),
                'type' => 'special_order_status',
                'title' => $statusMessages[$newStatus]['title'],
                'message' => $statusMessages[$newStatus]['message'],
                'link' => route('customer.special-orders.show', $specialOrder->id),
                'read' => false,
            ]);
        }
    }
}
```

#### **Envio de Email**
```php
// Para status específicos (ex: notified)
if ($newStatus === SpecialOrder::STATUS_NOTIFIED) {
    Mail::to($customer->email)->send(new SpecialOrderReady($specialOrder));
}
```

### Integração com Chatbot

#### **Reconhecimento de Intenções**
```php
// Detectar consulta sobre pedidos especiais
if ($this->containsAny($messageLower, [
    'meus pedidos especiais', 
    'pedidos especiais', 
    'status pedido especial',
    'acompanhar pedido especial'
])) {
    return $this->handleSpecialOrderQuery();
}
```

#### **Resposta Inteligente**
```php
private function handleSpecialOrderQuery(): array
{
    // Verificar autenticação
    if (!Auth::check()) {
        return ['message' => 'Para consultar seus pedidos especiais, você precisa estar logado.'];
    }
    
    // Buscar pedidos do cliente
    $specialOrders = SpecialOrder::where('customer_id', $customer->id)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    
    // Gerar resposta com resumo e lista
    return [
        'message' => "📚 **Seus Pedidos Especiais**\n\n📊 **Resumo:**\n• Total: {$total} pedidos\n• Pendentes: {$pending} pedidos\n\n📋 **Últimos pedidos:**\n{$orderList}",
        'options' => [
            'Ver detalhes completos',
            'Fazer novo pedido especial',
            'Voltar ao menu'
        ]
    ];
}
```

---

## Benefícios do Sistema

### Para o Cliente
- ✅ **Transparência**: Sabe exatamente onde está seu pedido
- ✅ **Conveniência**: Não precisa ligar para perguntar sobre status
- ✅ **Confiança**: Sistema profissional gera credibilidade
- ✅ **Acessibilidade**: Disponível 24/7 via web e chatbot

### Para a Empresa
- ✅ **Redução de Contatos**: Menos ligações perguntando sobre status
- ✅ **Eficiência**: Processo automatizado de comunicação
- ✅ **Satisfação**: Clientes mais satisfeitos com transparência
- ✅ **Profissionalismo**: Imagem de empresa moderna e organizada

### Para o Admin
- ✅ **Controle**: Visão completa de todos os pedidos
- ✅ **Automação**: Notificações enviadas automaticamente
- ✅ **Métricas**: Dados para otimizar o processo
- ✅ **Simplicidade**: Interface intuitiva para gestão

---

## Métricas e Analytics

### KPIs Principais
- **Tempo Médio por Status**: Quanto tempo cada etapa demora
- **Taxa de Conversão**: % de pedidos que chegam ao final
- **Taxa de Cancelamento**: % de pedidos cancelados
- **Satisfação do Cliente**: Feedback sobre o processo

### Relatórios Disponíveis
- **Pedidos por Período**: Gráfico temporal
- **Status Distribution**: Distribuição por status atual
- **Performance por Fornecedor**: Se aplicável
- **Livros Mais Solicitados**: Top 10 títulos

---

## Futuras Melhorias

### Curto Prazo
- [ ] **Push Notifications**: Via service worker
- [ ] **SMS**: Integração com gateway de SMS
- [ ] **WhatsApp**: Notificações via WhatsApp Business API

### Médio Prazo
- [ ] **Previsão de Chegada**: IA para estimar datas
- [ ] **Integração com Fornecedores**: API para status automático
- [ ] **Avaliação do Processo**: Cliente pode avaliar experiência

### Longo Prazo
- [ ] **App Mobile**: Aplicativo nativo
- [ ] **Realidade Aumentada**: Visualizar livro em 3D
- [ ] **Blockchain**: Rastreabilidade completa da cadeia

---

## Conclusão

O Sistema de Acompanhamento de Pedidos Especiais representa um salto qualitativo na experiência do cliente, transformando um processo tradicionalmente opaco em uma jornada transparente e profissional. 

A combinação de interface web intuitiva, chatbot inteligente e notificações automáticas cria uma experiência superior que beneficia tanto clientes quanto a operação da livraria.

**Status**: ✅ **Implementado e Funcional**  
**Versão**: 2.1.0  
**Última Atualização**: 26 de Novembro de 2025
