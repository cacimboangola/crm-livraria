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
  "message": "Olá, quero comprar um livro de ficção",
  "customer_id": 1
}
```

**Response:**

```json
{
  "response": "Olá! Temos ótimas opções de ficção. Aqui estão alguns livros populares...",
  "suggestions": [
    {
      "id": 1,
      "title": "1984",
      "author": "George Orwell",
      "price": 29.90
    }
  ]
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
      "title": "Nova fatura criada",
      "message": "Fatura #001 foi criada com sucesso",
      "type": "invoice",
      "created_at": "2025-01-20T10:30:00Z"
    }
  ],
  "count": 5
}
```

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

## Changelog

### v1.0.0 (2025-01-20)
- Lançamento inicial da API
- Endpoint de chatbot
- Endpoints de rastreamento de campanhas
- Endpoints de notificações
