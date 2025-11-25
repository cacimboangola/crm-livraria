# 📦 Documentação dos Módulos

## Visão Geral

Este documento detalha cada módulo do sistema, suas funcionalidades, fluxos de trabalho e integrações.

---

## 1. Módulo de Clientes

### Descrição
Gerenciamento completo de clientes da livraria, incluindo cadastro, histórico de compras e análise de comportamento.

### Funcionalidades

#### 1.1 CRUD de Clientes
- **Criar**: Cadastro de novos clientes com dados pessoais
- **Ler**: Visualização de detalhes e histórico
- **Atualizar**: Edição de informações cadastrais
- **Deletar**: Remoção de clientes (soft delete)

#### 1.2 Histórico de Compras
- Lista todas as faturas do cliente
- Total gasto
- Produtos mais comprados
- Frequência de compras

#### 1.3 Análise de Comportamento
- Categorias preferidas
- Ticket médio
- Última compra
- Status de atividade

### Rotas

```php
Route::resource('customers', CustomerController::class);
Route::get('/customers/search', [CustomerController::class, 'search']);
```

### Service: CustomerService

```php
// Métodos principais
create(array $data): Customer
update(Customer $customer, array $data): Customer
delete(Customer $customer): bool
getPurchaseHistory(Customer $customer): Collection
getPreferredCategories(Customer $customer): Collection
getInactiveCustomers(int $days = 90): Collection
```

### Campos da Tabela

```sql
- id (PK)
- name (string)
- email (string, unique)
- phone (string, nullable)
- address (text, nullable)
- birth_date (date, nullable)
- is_active (boolean, default: true)
- notes (text, nullable)
- created_at, updated_at
```

### Relacionamentos

- `hasMany` → Invoices
- `hasOne` → LoyaltyPoints
- `belongsToMany` → Campaigns

---

## 2. Módulo de Livros

### Descrição
Gestão completa do catálogo de livros, incluindo categorias, estoque e preços.

### Funcionalidades

#### 2.1 Gestão de Livros
- Cadastro com informações completas
- Upload de capa
- Controle de estoque
- Gestão de preços e descontos

#### 2.2 Categorias
- Organização hierárquica
- Filtros por categoria
- Estatísticas por categoria

#### 2.3 Controle de Estoque
- Atualização automática em vendas
- Alertas de estoque baixo
- Histórico de movimentações

### Rotas

```php
Route::resource('books', BookController::class);
Route::resource('book-categories', BookCategoryController::class);
Route::get('/books/category/{categoryId}', [BookController::class, 'byCategory']);
Route::put('/books/{book}/stock', [BookController::class, 'updateStock']);
```

### Services

#### BookService

```php
create(array $data): Book
update(Book $book, array $data): Book
updateStock(Book $book, int $quantity, string $type): void
getLowStock(int $threshold = 10): Collection
getByCategory(int $categoryId): Collection
```

#### BookCategoryService

```php
create(array $data): BookCategory
update(BookCategory $category, array $data): BookCategory
getWithBooksCount(): Collection
```

### Campos da Tabela Books

```sql
- id (PK)
- title (string)
- author (string)
- isbn (string, unique, nullable)
- description (text, nullable)
- price (decimal)
- discount_price (decimal, nullable)
- stock_quantity (integer, default: 0)
- book_category_id (FK)
- cover_image (string, nullable)
- publisher (string, nullable)
- publication_year (integer, nullable)
- pages (integer, nullable)
- language (string, default: 'pt')
- is_active (boolean, default: true)
- created_at, updated_at
```

### Relacionamentos

- `belongsTo` → BookCategory
- `hasMany` → InvoiceItems

---

## 3. Módulo de Vendas (Faturas)

### Descrição
Sistema completo de emissão e gestão de faturas, com múltiplos métodos de pagamento.

### Funcionalidades

#### 3.1 Emissão de Faturas
- Seleção de cliente
- Adição de múltiplos itens
- Cálculo automático de totais
- Aplicação de descontos
- Resgate de pontos de fidelidade

#### 3.2 Métodos de Pagamento
- Dinheiro
- Cartão de Crédito/Débito
- Transferência Bancária
- PIX

#### 3.3 Gestão de Status
- **Pendente**: Aguardando pagamento
- **Paga**: Pagamento confirmado
- **Cancelada**: Fatura cancelada

#### 3.4 Funcionalidades Adicionais
- Geração de PDF
- Envio por email
- Impressão
- Histórico de alterações

### Rotas

```php
Route::resource('invoices', InvoiceController::class);
Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'changeStatus']);
Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf']);
Route::post('/invoices/{invoice}/email', [InvoiceController::class, 'sendEmail']);
```

### Service: InvoiceService

```php
create(array $data): Invoice
update(Invoice $invoice, array $data): Invoice
changeStatus(Invoice $invoice, string $status): Invoice
generatePdf(Invoice $invoice): string
sendEmail(Invoice $invoice): bool
applyLoyaltyDiscount(Invoice $invoice, int $points): Invoice
```

### Campos da Tabela Invoices

```sql
- id (PK)
- invoice_number (string, unique)
- customer_id (FK)
- subtotal (decimal)
- discount (decimal, default: 0)
- loyalty_discount (decimal, default: 0)
- total (decimal)
- payment_method (enum: cash, card, transfer, pix)
- status (enum: pending, paid, cancelled)
- notes (text, nullable)
- paid_at (datetime, nullable)
- created_at, updated_at
```

### Campos da Tabela Invoice_Items

```sql
- id (PK)
- invoice_id (FK)
- book_id (FK)
- quantity (integer)
- unit_price (decimal)
- discount (decimal, default: 0)
- subtotal (decimal)
- created_at, updated_at
```

### Relacionamentos

- `belongsTo` → Customer
- `hasMany` → InvoiceItems
- `hasMany` → LoyaltyTransactions

### Fluxo de Criação

```
1. Selecionar cliente
2. Adicionar itens ao carrinho
3. Aplicar descontos (opcional)
4. Resgatar pontos de fidelidade (opcional)
5. Selecionar método de pagamento
6. Confirmar criação
   ↓
7. InvoiceService cria fatura
8. Atualiza estoque (BookService)
9. Adiciona pontos de fidelidade (LoyaltyService)
10. Envia notificação (NotificationService)
11. Retorna fatura criada
```

---

## 4. Módulo de Fidelidade

### Descrição
Sistema de pontos de fidelidade com ganho automático em compras e resgate como desconto.

### Funcionalidades

#### 4.1 Ganho de Pontos
- **Automático**: 1 ponto por cada 1€ gasto
- **Manual**: Adição manual por administradores
- **Campanhas**: Pontos de bônus via campanhas

#### 4.2 Resgate de Pontos
- Conversão: 100 pontos = 10€ de desconto
- Aplicação direta em faturas
- Histórico de resgates

#### 4.3 Expiração
- Pontos expiram após 365 dias
- Processamento automático via comando Artisan
- Notificação antes da expiração

#### 4.4 Dashboard
- Saldo atual
- Pontos a expirar
- Histórico de transações
- Estatísticas

### Rotas

```php
// Admin
Route::get('/loyalty/admin', [LoyaltyController::class, 'adminDashboard']);
Route::post('/loyalty/expiration', [LoyaltyController::class, 'processExpiration']);

// Cliente
Route::get('/loyalty/customers/{customer}', [LoyaltyController::class, 'customerDashboard']);
Route::post('/loyalty/customers/{customer}/add-points', [LoyaltyController::class, 'addPoints']);
Route::post('/loyalty/customers/{customer}/redeem-points', [LoyaltyController::class, 'redeemPoints']);
```

### Service: LoyaltyService

```php
addPoints(Customer $customer, int $points, string $description, ?Invoice $invoice): void
redeemPoints(Customer $customer, int $points, Invoice $invoice): float
getBalance(Customer $customer): int
getExpiringPoints(Customer $customer, int $days = 30): int
processExpiration(): int
removePoints(Invoice $invoice): void
```

### Campos da Tabela Loyalty_Points

```sql
- id (PK)
- customer_id (FK, unique)
- current_balance (integer, default: 0)
- total_earned (integer, default: 0)
- total_redeemed (integer, default: 0)
- created_at, updated_at
```

### Campos da Tabela Loyalty_Transactions

```sql
- id (PK)
- customer_id (FK)
- invoice_id (FK, nullable)
- campaign_id (FK, nullable)
- type (enum: earn, redeem, expire, bonus)
- points (integer)
- description (string)
- expires_at (date, nullable)
- created_at, updated_at
```

### Relacionamentos

- `belongsTo` → Customer
- `belongsTo` → Invoice (nullable)
- `belongsTo` → Campaign (nullable)

### Comando Artisan

```bash
# Processar expiração de pontos
php artisan loyalty:process-expiration
```

---

## 5. Módulo de Campanhas

### Descrição
Sistema completo de marketing com criação de campanhas, segmentação de clientes e rastreamento de métricas.

### Funcionalidades

#### 5.1 Criação de Campanhas
- Nome e descrição
- Tipo (email, sms, notificação)
- Conteúdo personalizado
- Período de vigência

#### 5.2 Segmentação de Clientes
- **Manual**: Seleção individual
- **Automática**: Baseada em critérios:
  - Clientes inativos (X dias)
  - Clientes que compraram categoria Y
  - Clientes com gasto total > Z
  - Clientes com pontos de fidelidade > W

#### 5.3 Distribuição de Pontos
- Pontos de bônus para participantes
- Incentivo para engajamento

#### 5.4 Envio de Emails
- Templates personalizados
- Variáveis dinâmicas (nome, pontos, etc.)
- Processamento em fila
- Links de rastreamento

#### 5.5 Rastreamento de Métricas
- **Taxa de Abertura**: Quantos abriram o email
- **Taxa de Cliques**: Quantos clicaram nos links
- **Taxa de Conversão**: Quantos realizaram compra

### Rotas

```php
Route::resource('campaigns', CampaignController::class);
Route::post('/campaigns/{campaign}/activate', [CampaignController::class, 'activate']);
Route::post('/campaigns/{campaign}/send-emails', [CampaignController::class, 'sendEmails']);
Route::post('/campaigns/{campaign}/distribute-points', [CampaignController::class, 'distributePoints']);
Route::get('/campaigns/{campaign}/metrics', [CampaignController::class, 'metrics']);

// Rastreamento (público)
Route::get('/track/open/{campaignId}/{customerId}/{token}', [CampaignTrackingController::class, 'trackOpen']);
Route::get('/track/click/{campaignId}/{customerId}/{token}', [CampaignTrackingController::class, 'trackClick']);
Route::get('/track/conversion/{campaignId}/{customerId}/{token}', [CampaignTrackingController::class, 'trackConversion']);
```

### Service: CampaignService

```php
create(array $data): Campaign
update(Campaign $campaign, array $data): Campaign
activate(Campaign $campaign): Campaign
cancel(Campaign $campaign): Campaign
complete(Campaign $campaign): Campaign
addCustomers(Campaign $campaign, array $customerIds): void
autoSelectCustomers(Campaign $campaign, array $criteria): void
sendEmails(Campaign $campaign): void
distributePoints(Campaign $campaign, int $points): void
getMetrics(Campaign $campaign): array
```

### Campos da Tabela Campaigns

```sql
- id (PK)
- name (string)
- description (text, nullable)
- type (enum: email, sms, notification)
- status (enum: draft, active, completed, cancelled)
- content (text)
- start_date (date)
- end_date (date, nullable)
- target_customers_count (integer, default: 0)
- emails_sent (integer, default: 0)
- emails_opened (integer, default: 0)
- emails_clicked (integer, default: 0)
- conversions (integer, default: 0)
- created_at, updated_at
```

### Tabela Pivot: campaign_customer

```sql
- campaign_id (FK)
- customer_id (FK)
- email_sent_at (datetime, nullable)
- email_opened_at (datetime, nullable)
- email_clicked_at (datetime, nullable)
- converted_at (datetime, nullable)
- tracking_token (string, unique)
```

### Relacionamentos

- `belongsToMany` → Customers
- `hasMany` → LoyaltyTransactions

### Fluxo de Campanha

```
1. Criar campanha (draft)
2. Adicionar conteúdo
3. Selecionar clientes (manual ou automático)
4. Ativar campanha
5. Enviar emails (processado em fila)
   ↓
6. Cliente recebe email com links de rastreamento
7. Cliente abre email → trackOpen()
8. Cliente clica em link → trackClick()
9. Cliente realiza compra → trackConversion()
   ↓
10. Dashboard exibe métricas em tempo real
```

---

## 6. Módulo de Recomendações

### Descrição
Sistema inteligente de recomendações baseado em histórico de compras e comportamento.

### Funcionalidades

#### 6.1 Livros Populares
- Mais vendidos globalmente
- Filtro por período
- Filtro por categoria

#### 6.2 Recomendações Personalizadas
- Baseadas em compras anteriores
- Categorias preferidas
- Autores favoritos

#### 6.3 Livros Similares
- Mesma categoria
- Mesmo autor
- Faixa de preço similar

#### 6.4 Clientes Potenciais
- Clientes que compraram livros similares
- Clientes da mesma categoria
- Segmentação para campanhas

### Rotas

```php
Route::get('/recommendations/popular', [RecommendationController::class, 'popularBooks']);
Route::get('/recommendations/customer/{customer}', [RecommendationController::class, 'forCustomer']);
Route::get('/recommendations/book/{book}/similar', [RecommendationController::class, 'similarBooks']);
Route::get('/recommendations/book/{book}/potential-customers', [RecommendationController::class, 'potentialCustomers']);
```

### Service: RecommendationService

```php
getPopularBooks(int $limit = 10, ?int $categoryId = null): Collection
getRecommendationsForCustomer(Customer $customer, int $limit = 10): Collection
getSimilarBooks(Book $book, int $limit = 10): Collection
getPotentialCustomers(Book $book, int $limit = 50): Collection
```

### Algoritmos

#### Popular Books
```sql
SELECT books.*, COUNT(invoice_items.id) as sales_count
FROM books
JOIN invoice_items ON books.id = invoice_items.book_id
JOIN invoices ON invoice_items.invoice_id = invoices.id
WHERE invoices.status = 'paid'
GROUP BY books.id
ORDER BY sales_count DESC
LIMIT 10
```

#### Customer Recommendations
```php
1. Buscar categorias mais compradas pelo cliente
2. Buscar livros dessas categorias que o cliente ainda não comprou
3. Ordenar por popularidade
4. Retornar top 10
```

---

## 7. Módulo de Notificações

### Descrição
Sistema centralizado de notificações para usuários e administradores.

### Funcionalidades

#### 7.1 Tipos de Notificação
- Fatura criada
- Fatura paga
- Pontos de fidelidade ganhos
- Pontos próximos da expiração
- Campanha iniciada
- Estoque baixo (admin)

#### 7.2 Gerenciamento
- Marcar como lida
- Marcar todas como lidas
- Deletar notificação
- Limpar lidas

#### 7.3 Exibição
- Badge com contador
- Dropdown com últimas notificações
- Página completa de histórico

### Rotas

```php
Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
Route::get('/notifications/unread', [NotificationController::class, 'getUnreadNotifications']);
```

### Service: NotificationService

```php
create(User $user, string $title, string $message, string $type, ?string $url = null): void
markAsRead(int $notificationId): void
markAllAsRead(User $user): void
getUnread(User $user): Collection
deleteRead(User $user): int
```

### Campos da Tabela Notifications

```sql
- id (PK)
- user_id (FK)
- title (string)
- message (text)
- type (string)
- url (string, nullable)
- read_at (datetime, nullable)
- created_at, updated_at
```

---

## 8. Portal do Cliente

### Descrição
Interface dedicada para clientes realizarem compras online de forma autônoma.

### Funcionalidades

#### 8.1 Catálogo Público
- Navegação por categorias
- Busca de livros
- Detalhes do livro
- Visualização de capa

#### 8.2 Carrinho de Compras
- Adicionar/remover itens
- Atualizar quantidades
- Visualizar total
- Persistência em sessão

#### 8.3 Checkout
- Revisão do pedido
- Seleção de método de pagamento
- Aplicação de pontos de fidelidade
- Confirmação

#### 8.4 Minha Conta
- Dashboard com resumo
- Histórico de pedidos
- Pontos de fidelidade
- Edição de perfil

### Rotas

```php
// Público
Route::get('/catalogo', [CustomerPortalController::class, 'catalog']);
Route::get('/livro/{book}', [CustomerPortalController::class, 'bookDetails']);

// Autenticado
Route::get('/cliente/dashboard', [CustomerPortalController::class, 'dashboard']);
Route::get('/cliente/carrinho', [CartController::class, 'show']);
Route::post('/cliente/carrinho/adicionar', [CartController::class, 'add']);
Route::post('/cliente/checkout', [CheckoutController::class, 'process']);
Route::get('/cliente/pedidos', [CustomerPortalController::class, 'orders']);
Route::get('/cliente/fidelidade', [CustomerPortalController::class, 'loyalty']);
```

---

## 9. Chatbot

### Descrição
Assistente virtual para atendimento ao cliente e suporte.

### Funcionalidades

- Responder perguntas sobre livros
- Buscar livros por título/autor
- Informar sobre promoções
- Consultar status de pedidos
- Informar saldo de pontos

### Endpoint

```php
POST /api/chatbot
{
  "message": "Olá, quero comprar um livro de ficção",
  "customer_id": 1
}
```

### Implementação

Utiliza processamento de linguagem natural simples com palavras-chave e respostas pré-definidas.

---

## 7. Módulo de Pedidos Especiais

### Descrição
Sistema para gerenciar pedidos de livros que não estão em estoque, permitindo que funcionários registrem solicitações de clientes e acompanhem todo o processo até a entrega.

### Funcionalidades

#### 7.1 Gestão de Pedidos Especiais
- **Criar**: Registro de novos pedidos especiais
- **Acompanhar**: Timeline de status do pedido
- **Notificar**: Alertas automáticos para funcionários e clientes
- **Finalizar**: Controle de entrega e conclusão

#### 7.2 Status do Pedido
- **Pending**: Aguardando encomenda ao fornecedor
- **Ordered**: Encomendado ao fornecedor
- **Received**: Recebido na loja
- **Notified**: Cliente notificado
- **Delivered**: Entregue ao cliente
- **Cancelled**: Cancelado

#### 7.3 Notificações Automáticas
- Email para cliente quando livro chegar
- Notificações internas para funcionários
- Timeline visual do progresso

### Rotas

```php
Route::resource('special-orders', SpecialOrderController::class);
Route::patch('/special-orders/{special_order}/advance-status', [SpecialOrderController::class, 'advanceStatus']);
Route::patch('/special-orders/{special_order}/cancel', [SpecialOrderController::class, 'cancel']);
```

### Model: SpecialOrder

```php
// Relacionamentos
belongsTo(Customer::class)
belongsTo(User::class) // Funcionário que criou

// Scopes
scopePending($query)
scopeActive($query)
scopeNeedsAction($query)

// Métodos
canBeCancelled(): bool
canAdvanceStatus(): bool
getStatusFormattedAttribute(): string
getNextStatusAttribute(): string
```

### Service: SpecialOrderService

```php
// Métodos principais
create(array $data): SpecialOrder
advanceStatus(SpecialOrder $order): SpecialOrder
notifyCustomer(SpecialOrder $order): void
notifyAdmins(SpecialOrder $order, string $type): void
getMetrics(): array
```

### Fluxo de Trabalho

```
Cliente solicita livro fora de estoque
         ↓
Funcionário cria pedido especial
         ↓
Sistema notifica administradores
         ↓
Funcionário encomenda ao fornecedor
         ↓
Status atualizado para "Ordered"
         ↓
Livro chega na loja
         ↓
Status atualizado para "Received"
         ↓
Sistema notifica cliente por email
         ↓
Cliente retira/recebe o livro
         ↓
Status atualizado para "Delivered"
```

### Campos do Formulário

```php
// Dados do livro
'book_title' => 'required|string|max:255'
'book_author' => 'nullable|string|max:255'
'book_isbn' => 'nullable|string|max:20'
'book_publisher' => 'nullable|string|max:255'
'quantity' => 'required|integer|min:1'
'estimated_price' => 'nullable|numeric|min:0'

// Dados do pedido
'customer_id' => 'required|exists:customers,id'
'delivery_preference' => 'required|in:pickup,delivery'
'customer_notes' => 'nullable|string'
'supplier_notes' => 'nullable|string'
```

### Métricas e Relatórios

- Total de pedidos especiais por período
- Tempo médio de atendimento
- Taxa de conversão (pedidos concluídos)
- Livros mais solicitados
- Fornecedores mais utilizados

---

## Integrações Entre Módulos

### Fatura → Fidelidade
Ao criar/pagar fatura, pontos são automaticamente adicionados.

### Fatura → Estoque
Ao criar fatura, estoque é automaticamente decrementado.

### Campanha → Fidelidade
Ao distribuir pontos de campanha, transações são criadas.

### Pedidos Especiais → Notificações
Ao avançar status do pedido, notificações são enviadas automaticamente.

### Pedidos Especiais → Clientes
Pedidos especiais são vinculados a clientes específicos.

### Recomendações → Vendas
Recomendações baseadas em histórico de faturas.

### Notificações → Todos
Todos os módulos podem disparar notificações.

---

## Conclusão

Cada módulo foi projetado para ser independente mas integrado, permitindo manutenção e evolução facilitadas. A comunicação entre módulos é feita através dos Services, mantendo o código organizado e testável.
