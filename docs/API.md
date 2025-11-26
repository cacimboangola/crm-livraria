# 📡 Documentação da API

## Visão Geral

O CRM Livraria expõe algumas APIs para integração externa e funcionalidades específicas.

## Base URL

```
http://localhost:8000/api
```

## Autenticação

A maioria das rotas requer autenticação via Laravel Sanctum ou sessão.

```http
Authorization: Bearer {token}
```

## Endpoints

### Chatbot API

#### POST /api/chatbot

Processa mensagens do chatbot e retorna respostas inteligentes.

**Request:**

```json
{
  "message": "Meus pedidos especiais"
}
```

**Response:**

```json
{
  "message": "📚 **Seus Pedidos Especiais**\n\n📊 **Resumo:**\n• Total: 2 pedidos\n• Pendentes: 1 pedidos\n• Em andamento: 1 pedidos\n\n📋 **Últimos pedidos:**\n- ⏳ **Pedido #6**: O Código Da Vinci - Dan Brown (pending)\n- 📦 **Pedido #5**: Dom Quixote - Miguel de Cervantes (ordered)",
  "options": [
    "Ver detalhes completos",
    "Fazer novo pedido especial",
    "Voltar ao menu"
  ]
}
```

**Funcionalidades do Chatbot:**
- ✅ **Consulta de Pedidos Especiais**: "meus pedidos especiais", "status pedido especial"
- ✅ **Criação de Pedidos**: "pedido especial", "livro em falta"
- ✅ **Busca de Livros**: "buscar livro", "procurar livro"
- ✅ **Suporte**: "falar com atendente", "ajuda"

#### POST /api/chatbot/special-order

Cria pedido especial via chatbot (requer autenticação).

**Request:**

```json
{
  "book_title": "Dom Quixote",
  "book_author": "Miguel de Cervantes",
  "book_isbn": "978-85-359-0277-8",
  "book_publisher": "Editora Moderna",
  "quantity": 1,
  "delivery_preference": "pickup",
  "customer_notes": "Preciso urgente para trabalho acadêmico"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Pedido especial criado com sucesso!",
  "special_order": {
    "id": 7,
    "book_title": "Dom Quixote",
    "status": "pending",
    "created_at": "2025-11-26T08:00:00Z"
  }
}
```

**Status Codes:**
- `200 OK`: Sucesso
- `400 Bad Request`: Mensagem inválida
- `500 Internal Server Error`: Erro no processamento

---

### Rastreamento de Campanhas

#### GET /track/open/{campaignId}/{customerId}/{token}

Rastreia abertura de email de campanha.

**Parâmetros:**
- `campaignId` (integer): ID da campanha
- `customerId` (integer): ID do cliente
- `token` (string): Token de segurança

**Response:**
Retorna um pixel transparente 1x1 (GIF)

**Status Codes:**
- `200 OK`: Abertura registrada
- `404 Not Found`: Campanha ou cliente não encontrado

---

#### GET /track/click/{campaignId}/{customerId}/{token}

Rastreia clique em link de campanha.

**Parâmetros:**
- `campaignId` (integer): ID da campanha
- `customerId` (integer): ID do cliente
- `token` (string): Token de segurança

**Response:**
Redireciona para a URL de destino

**Status Codes:**
- `302 Found`: Clique registrado e redirecionado
- `404 Not Found`: Campanha ou cliente não encontrado

---

#### GET /track/conversion/{campaignId}/{customerId}/{token}

Rastreia conversão (compra) originada de campanha.

**Parâmetros:**
- `campaignId` (integer): ID da campanha
- `customerId` (integer): ID do cliente
- `token` (string): Token de segurança

**Response:**

```json
{
  "success": true,
  "message": "Conversão registrada com sucesso"
}
```

**Status Codes:**
- `200 OK`: Conversão registrada
- `404 Not Found`: Campanha ou cliente não encontrado

---

### Notificações (Requer Autenticação)

#### GET /notifications/unread

Retorna notificações não lidas do usuário autenticado.

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "title": "Pedido Especial Encomendado! 📦",
      "message": "Seu pedido especial \"O Código Da Vinci\" foi encomendado ao fornecedor. Você será notificado quando chegar!",
      "type": "special_order_status",
      "link": "/cliente/pedidos-especiais/6",
      "created_at": "2025-11-26T08:00:00Z"
    },
    {
      "id": 2,
      "title": "Livro Chegou na Loja! ✅",
      "message": "O livro \"Dom Quixote\" chegou em nossa loja e está sendo preparado para você.",
      "type": "special_order_status",
      "link": "/cliente/pedidos-especiais/5",
      "created_at": "2025-11-26T09:00:00Z"
    }
  ],
  "count": 2
}
```

**Tipos de Notificação:**
- ✅ **special_order_status**: Mudanças de status em pedidos especiais
- ✅ **invoice**: Notificações de faturas
- ✅ **loyalty**: Programa de fidelidade
- ✅ **campaign**: Campanhas de marketing

**Status Codes:**
- `200 OK`: Sucesso
- `401 Unauthorized`: Não autenticado

---

#### POST /notifications/{id}/read

Marca notificação como lida.

**Response:**

```json
{
  "success": true,
  "message": "Notificação marcada como lida"
}
```

**Status Codes:**
- `200 OK`: Sucesso
- `404 Not Found`: Notificação não encontrada
- `401 Unauthorized`: Não autenticado

---

#### POST /notifications/read-all

Marca todas as notificações como lidas.

**Response:**

```json
{
  "success": true,
  "message": "Todas as notificações foram marcadas como lidas",
  "count": 5
}
```

**Status Codes:**
- `200 OK`: Sucesso
- `401 Unauthorized`: Não autenticado

---

## Estrutura de Resposta Padrão

### Sucesso

```json
{
  "success": true,
  "data": { ... },
  "message": "Operação realizada com sucesso"
}
```

### Erro

```json
{
  "success": false,
  "error": "Mensagem de erro",
  "code": "ERROR_CODE"
}
```

### Validação

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "O campo é obrigatório"
    ]
  }
}
```

## Códigos de Status HTTP

- `200 OK`: Requisição bem-sucedida
- `201 Created`: Recurso criado com sucesso
- `204 No Content`: Requisição bem-sucedida sem conteúdo de retorno
- `400 Bad Request`: Dados inválidos
- `401 Unauthorized`: Não autenticado
- `403 Forbidden`: Sem permissão
- `404 Not Found`: Recurso não encontrado
- `422 Unprocessable Entity`: Erro de validação
- `500 Internal Server Error`: Erro no servidor

## Rate Limiting

As APIs possuem rate limiting para prevenir abuso:

- **Geral**: 60 requisições por minuto
- **Chatbot**: 20 requisições por minuto
- **Rastreamento**: Sem limite (público)

**Headers de Rate Limit:**

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1642684800
```

## Paginação

Endpoints que retornam listas usam paginação:

**Request:**

```
GET /api/customers?page=2&per_page=20
```

**Response:**

```json
{
  "data": [...],
  "current_page": 2,
  "last_page": 10,
  "per_page": 20,
  "total": 200,
  "links": {
    "first": "http://localhost:8000/api/customers?page=1",
    "last": "http://localhost:8000/api/customers?page=10",
    "prev": "http://localhost:8000/api/customers?page=1",
    "next": "http://localhost:8000/api/customers?page=3"
  }
}
```

## Filtros e Ordenação

Muitos endpoints suportam filtros e ordenação:

**Filtros:**

```
GET /api/books?category_id=1&min_price=10&max_price=50
```

**Ordenação:**

```
GET /api/books?sort_by=price&sort_order=desc
```

**Busca:**

```
GET /api/books?search=harry+potter
```

## Webhooks

O sistema pode enviar webhooks para URLs configuradas quando eventos importantes ocorrem.

### Eventos Disponíveis

- `invoice.created`: Nova fatura criada
- `invoice.paid`: Fatura paga
- `invoice.cancelled`: Fatura cancelada
- `campaign.completed`: Campanha concluída
- `loyalty.points_earned`: Pontos de fidelidade ganhos
- `loyalty.points_redeemed`: Pontos de fidelidade resgatados

### Estrutura do Webhook

**Headers:**

```
Content-Type: application/json
X-Webhook-Signature: sha256_hash
```

**Payload:**

```json
{
  "event": "invoice.created",
  "timestamp": "2025-01-20T10:30:00Z",
  "data": {
    "id": 1,
    "customer_id": 5,
    "total": 150.00,
    "status": "pending"
  }
}
```

### Verificação de Assinatura

```php
$signature = hash_hmac('sha256', $payload, $webhookSecret);
$isValid = hash_equals($signature, $receivedSignature);
```

## Exemplos de Uso

### cURL

```bash
# Chatbot
curl -X POST http://localhost:8000/api/chatbot \
  -H "Content-Type: application/json" \
  -d '{"message": "Olá", "customer_id": 1}'

# Notificações não lidas
curl -X GET http://localhost:8000/notifications/unread \
  -H "Authorization: Bearer {token}"
```

### JavaScript (Fetch)

```javascript
// Chatbot
fetch('http://localhost:8000/api/chatbot', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    message: 'Olá',
    customer_id: 1
  })
})
.then(response => response.json())
.then(data => console.log(data));

// Notificações
fetch('http://localhost:8000/notifications/unread', {
  headers: {
    'Authorization': 'Bearer ' + token
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

### PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8000']);

// Chatbot
$response = $client->post('/api/chatbot', [
    'json' => [
        'message' => 'Olá',
        'customer_id' => 1
    ]
]);

$data = json_decode($response->getBody(), true);
```

## Erros Comuns

### 401 Unauthorized

```json
{
  "message": "Unauthenticated."
}
```

**Solução**: Incluir token de autenticação válido

### 422 Validation Error

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "message": ["O campo message é obrigatório."]
  }
}
```

**Solução**: Corrigir dados enviados conforme mensagens de erro

### 429 Too Many Requests

```json
{
  "message": "Too Many Attempts."
}
```

**Solução**: Aguardar antes de fazer nova requisição

## Versionamento

Atualmente a API está na versão 1.0. Futuras versões serão versionadas via URL:

```
/api/v1/...
/api/v2/...
```

## Suporte

Para dúvidas sobre a API:
- Consulte esta documentação
- Abra uma issue no repositório
- Entre em contato com o suporte técnico

### Rastreamento de Campanhas

#### GET /track/open/{campaign_id}/{customer_id}/{token}

Rastreia abertura de email de campanha.

**Parâmetros:**
- `campaign_id`: ID da campanha
- `customer_id`: ID do cliente
- `token`: Token de segurança

**Response:** Pixel transparente 1x1 (GIF)

#### GET /track/click/{campaign_id}/{customer_id}/{token}

Rastreia clique em link de campanha.

**Parâmetros:**
- `campaign_id`: ID da campanha
- `customer_id`: ID do cliente  
- `token`: Token de segurança
- `url`: URL de destino (query parameter)

**Response:** Redirecionamento para URL original

#### POST /track/conversion/{campaign_id}/{customer_id}/{token}

Registra conversão de campanha.

**Request:**
```json
{
  "revenue": 150.00,
  "order_id": "12345"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Conversão registrada com sucesso"
}
```

### Pedidos Especiais (Admin)

#### GET /special-orders

Lista pedidos especiais (requer autenticação admin).

**Parâmetros de Query:**
- `status`: Filtrar por status (pending, ordered, received, notified, delivered, cancelled)
- `customer_id`: Filtrar por cliente
- `search`: Buscar por título ou autor
- `page`: Página (paginação)
- `per_page`: Itens por página (padrão: 10)

**Response:**
```json
{
  "data": [
    {
      "id": 6,
      "book_title": "O Código Da Vinci",
      "book_author": "Dan Brown",
      "book_isbn": "978-85-359-0277-8",
      "book_publisher": "Sextante",
      "quantity": 2,
      "delivery_preference": "pickup",
      "customer_notes": "Preciso urgente para um trabalho acadêmico",
      "status": "pending",
      "status_formatted": "Aguardando Encomenda",
      "customer": {
        "id": 24,
        "name": "João Silva",
        "email": "joao@teste.com"
      },
      "created_at": "2025-11-26T07:22:00Z",
      "ordered_at": null,
      "received_at": null,
      "notified_at": null,
      "delivered_at": null
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

### Pedidos Especiais (Cliente)

#### GET /cliente/pedidos-especiais

Lista pedidos especiais do cliente autenticado.

**Parâmetros de Query:**
- `status`: Filtrar por status
- `page`: Página (paginação)

**Response:**
```json
{
  "data": [
    {
      "id": 6,
      "book_title": "O Código Da Vinci",
      "book_author": "Dan Brown",
      "quantity": 2,
      "delivery_preference": "pickup",
      "status": "pending",
      "status_formatted": "Aguardando Encomenda",
      "created_at": "2025-11-26T07:22:00Z"
    }
  ],
  "stats": {
    "total": 1,
    "pending": 1,
    "active": 1,
    "delivered": 0
  }
}
```

#### GET /cliente/pedidos-especiais/{id}

Detalhes de um pedido especial específico do cliente.

**Response:**
```json
{
  "id": 6,
  "book_title": "O Código Da Vinci",
  "book_author": "Dan Brown",
  "book_isbn": "978-85-359-0277-8",
  "book_publisher": "Sextante",
  "quantity": 2,
  "delivery_preference": "pickup",
  "customer_notes": "Preciso urgente para um trabalho acadêmico",
  "status": "pending",
  "status_formatted": "Aguardando Encomenda",
  "timeline": [
    {
      "status": "pending",
      "label": "Pedido Criado",
      "completed": true,
      "date": "2025-11-26T07:22:00Z"
    },
    {
      "status": "ordered",
      "label": "Encomendado ao Fornecedor",
      "completed": false,
      "date": null
    },
    {
      "status": "received",
      "label": "Recebido na Loja",
      "completed": false,
      "date": null
    },
    {
      "status": "notified",
      "label": "Pronto para Retirada",
      "completed": false,
      "date": null
    },
    {
      "status": "delivered",
      "label": "Retirado",
      "completed": false,
      "date": null
    }
  ],
  "can_cancel": true,
  "created_at": "2025-11-26T07:22:00Z"
}
```

#### POST /special-orders

Cria novo pedido especial (requer autenticação admin).

**Request:**
```json
{
  "customer_id": 1,
  "book_title": "Dom Quixote",
  "book_author": "Miguel de Cervantes",
  "book_isbn": "978-85-359-0277-8",
  "quantity": 1,
  "delivery_preference": "pickup",
  "customer_notes": "Edição especial se possível"
}
```

**Response:**
```json
{
  "id": 1,
  "book_title": "Dom Quixote",
  "status": "pending",
  "created_at": "2025-11-25T10:00:00Z"
}
```

#### PATCH /special-orders/{id}/advance-status

Avança status do pedido especial.

**Response:**
```json
{
  "id": 1,
  "status": "ordered",
  "status_formatted": "Encomendado ao Fornecedor",
  "updated_at": "2025-11-25T10:30:00Z"
}
```

## Webhooks

### Campaign Conversion Webhook

Configure um webhook para receber notificações de conversões:

```http
POST https://seu-site.com/webhook/campaign-conversion
Content-Type: application/json

{
  "campaign_id": 1,
  "customer_id": 1,
  "revenue": 150.00,
  "order_id": "12345",
  "timestamp": "2025-11-25T10:00:00Z"
}
```

### Special Order Status Webhook

Receba notificações quando status de pedido especial mudar:

```http
POST https://seu-site.com/webhook/special-order-status
Content-Type: application/json

{
  "special_order_id": 1,
  "old_status": "pending",
  "new_status": "ordered",
  "customer_id": 1,
  "timestamp": "2025-11-25T10:00:00Z"
}
```

## Changelog

### v2.1.0 (2025-11-26)
- ✅ **Sistema Completo de Acompanhamento de Pedidos Especiais**
  - Interface web para clientes acompanharem pedidos
  - Timeline visual com status em tempo real
  - Notificações automáticas por mudança de status
  - Integração completa com chatbot
- ✅ **Chatbot Inteligente Expandido**
  - Consulta de pedidos especiais via chat
  - Criação de pedidos via formulário integrado
  - Reconhecimento de intenções melhorado
  - Redirecionamento para páginas específicas
- ✅ **Sistema de Notificações Avançado**
  - Notificações específicas para pedidos especiais
  - Links diretos para páginas relevantes
  - Diferentes tipos de notificação por contexto
- ✅ **Endpoints do Cliente**
  - GET /cliente/pedidos-especiais (lista)
  - GET /cliente/pedidos-especiais/{id} (detalhes)
  - PATCH /cliente/pedidos-especiais/{id}/cancelar (cancelar)

### v2.0.0 (2025-11-25)
- ✅ Adicionados endpoints de rastreamento de campanhas
- ✅ Adicionados endpoints de pedidos especiais (admin)
- ✅ Implementados webhooks para conversões
- ✅ Melhorada segurança com tokens

### v1.0.0 (2025-01-20)
- Lançamento inicial da API
- Endpoint de chatbot básico
- Endpoints básicos de notificações
