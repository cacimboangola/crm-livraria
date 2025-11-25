# 🤖 Documentação Completa do Chatbot

**Sistema de Assistente Virtual Inteligente para CRM Livraria**

---

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Arquitetura](#arquitetura)
- [Tipos de Mensagens](#tipos-de-mensagens)
- [Fluxos de Conversa](#fluxos-de-conversa)
- [Respostas por Categoria](#respostas-por-categoria)
- [Palavras-chave e Sinônimos](#palavras-chave-e-sinônimos)
- [Estados e Contextos](#estados-e-contextos)
- [Integração com Sistema](#integração-com-sistema)
- [Configuração e Personalização](#configuração-e-personalização)

---

## 🎯 Visão Geral

O chatbot é um assistente virtual inteligente que ajuda clientes da livraria com:

- **Busca de livros** por título, autor ou categoria
- **Consulta de pedidos** e histórico de compras
- **Informações sobre fidelidade** e pontos acumulados
- **Direcionamento para atendimento** humano quando necessário
- **Navegação** e ajuda geral no site

### Características Principais

- ✅ **Processamento de linguagem natural** em português
- ✅ **Reconhecimento de intenções** com sistema de prioridades
- ✅ **Mapeamento de sinônimos** automático
- ✅ **Integração com banco de dados** para busca em tempo real
- ✅ **Interface responsiva** e moderna
- ✅ **Rastreamento de conversas** para melhorias

---

## 🏗️ Arquitetura

### Backend (Laravel)
```
ChatbotController.php
├── processMessage()      # Endpoint principal da API
├── generateResponse()    # Lógica de processamento
├── isGreeting()         # Detecta saudações
├── containsAny()        # Verifica palavras-chave
├── handleBookSearch()   # Processa busca de livros
├── handleOrderQuery()   # Consulta pedidos
├── handleLoyaltyQuery() # Consulta fidelidade
└── extractSearchTerms() # Extrai termos de busca
```

### Frontend (JavaScript)
```
chatbot.js
├── Chatbot Class
├── init()              # Inicialização
├── createChatbotUI()   # Interface do usuário
├── bindEvents()        # Eventos e interações
├── sendMessage()       # Envio de mensagens
├── handleUserInput()   # Processamento de entrada
├── addMessage()        # Adiciona mensagens ao chat
└── addTypingIndicator() # Indicador de digitação
```

### Integração
- **Rota API**: `POST /api/chatbot`
- **Autenticação**: CSRF Token
- **Banco de dados**: Consultas em Books, Customers, Invoices
- **Serviços**: LoyaltyService para pontos de fidelidade

---

## 📝 Tipos de Mensagens

### 1. Saudações

#### Palavras-chave Reconhecidas:
```
'oi', 'olá', 'ola', 'oie', 'opa'
'bom dia', 'boa tarde', 'boa noite', 'bom tarde'
'ei', 'hey', 'hi', 'hello', 'hola'
'tchau', 'até logo', 'até mais', 'bye', 'adeus'
'obrigado', 'obrigada', 'valeu', 'thanks'
```

#### Exemplos de Entrada:
- "Olá"
- "Oi, tudo bem?"
- "Bom dia!"
- "Hey"
- "Obrigado"

#### Resposta:
```
Mensagem: "Olá! Como posso ajudar você hoje?"
Opções:
- Buscar livros
- Meus pedidos  
- Pontos de fidelidade
- Falar com atendente
```

### 2. Busca de Livros

#### 2.1 Busca Específica (Prioridade Alta)

##### Palavras-chave:
```
'buscar livro', 'procurar livro', 'encontrar livro'
'quero um livro', 'livro de', 'livros de'
```

##### Exemplos de Entrada:
- "Buscar livro de ficção"
- "Quero um livro de romance"
- "Procurar livro do Isaac Asimov"
- "Livros de fantasia"

##### Resposta com Resultados:
```
Mensagem: "Encontrei estes livros para você:
- [Título] por [Autor] - Kz [Preço]
- [Título] por [Autor] - Kz [Preço]
- [Título] por [Autor] - Kz [Preço]
Gostaria de mais informações sobre algum deles?"

Opções:
- Ver mais livros
- Buscar outro livro
- Ver categorias
```

##### Resposta sem Resultados:
```
Mensagem: "Não encontrei livros correspondentes à sua busca. Tente outros termos ou categorias."
Opções:
- Ver categorias
- Buscar por autor
- Falar com atendente
```

#### 2.2 Busca Geral (Prioridade Média)

##### Palavras-chave:
```
'livro', 'livros', 'autor', 'categoria'
'ficção', 'romance', 'fantasia', 'biografia'
'história', 'infantil', 'negócios', 'autoajuda'
```

##### Exemplos de Entrada:
- "ficção"
- "romance"
- "Isaac Asimov"
- "livros infantis"

#### 2.3 Busca Sem Termos Específicos

##### Resposta:
```
Mensagem: "O que você gostaria de buscar? Você pode digitar o título, autor ou categoria do livro."
Opções:
- Livros mais vendidos
- Novos lançamentos
- Promoções
```

### 3. Consulta de Pedidos

#### 3.1 Pedidos Específicos (Prioridade Alta)

##### Palavras-chave:
```
'meus pedidos', 'meu pedido', 'minhas compras', 'minha compra'
'histórico de pedidos', 'status do pedido'
```

##### Exemplos de Entrada:
- "Meus pedidos"
- "Quero ver meu pedido"
- "Histórico de compras"
- "Status do meu pedido"

#### 3.2 Pedidos Gerais (Prioridade Média)

##### Palavras-chave:
```
'pedido', 'compra', 'encomenda', 'fatura', 'ordem'
```

##### Exemplos de Entrada:
- "pedido"
- "compra"
- "fatura"

#### 3.3 Respostas para Usuário Não Logado:

```
Mensagem: "Para verificar seus pedidos, você precisa estar logado. Por favor, faça login na sua conta."
Opções:
- Como fazer login?
- Voltar ao menu
- Falar com atendente
```

#### 3.4 Respostas para Usuário sem Perfil:

```
Mensagem: "Não encontrei um perfil de cliente associado à sua conta. Por favor, complete seu perfil para acessar seus pedidos."
Opções:
- Como completar meu perfil?
- Voltar ao menu
- Falar com atendente
```

#### 3.5 Respostas para Usuário sem Pedidos:

```
Mensagem: "Você ainda não possui pedidos registrados em nosso sistema."
Opções:
- Ver livros disponíveis
- Como fazer um pedido?
- Voltar ao menu
```

#### 3.6 Respostas com Pedidos Encontrados:

```
Mensagem: "Aqui estão seus pedidos mais recentes:
- Pedido #[ID] ([Data]) - Kz [Valor] - Status: [Status]
- Pedido #[ID] ([Data]) - Kz [Valor] - Status: [Status]
- Pedido #[ID] ([Data]) - Kz [Valor] - Status: [Status]

Gostaria de ver mais detalhes de algum pedido específico?"

Opções:
- Ver todos os pedidos
- Status de entrega
- Voltar ao menu
```

### 4. Pontos de Fidelidade

#### 4.1 Fidelidade Específica (Prioridade Alta)

##### Palavras-chave:
```
'meus pontos', 'pontos de fidelidade', 'programa de fidelidade'
'saldo de pontos', 'quantos pontos'
```

##### Exemplos de Entrada:
- "Meus pontos"
- "Pontos de fidelidade"
- "Quantos pontos eu tenho?"
- "Saldo de pontos"

#### 4.2 Fidelidade Geral (Prioridade Média)

##### Palavras-chave:
```
'ponto', 'pontos', 'fidelidade', 'recompensa', 'desconto'
```

##### Exemplos de Entrada:
- "pontos"
- "fidelidade"
- "recompensa"

#### 4.3 Respostas para Usuário Não Logado:

```
Mensagem: "Para verificar seus pontos de fidelidade, você precisa estar logado. Por favor, faça login na sua conta."
Opções:
- Como fazer login?
- Voltar ao menu
- Falar com atendente
```

#### 4.4 Respostas para Usuário sem Perfil:

```
Mensagem: "Não encontrei um perfil de cliente associado à sua conta. Por favor, complete seu perfil para acessar o programa de fidelidade."
Opções:
- Como completar meu perfil?
- Voltar ao menu
- Falar com atendente
```

#### 4.5 Respostas com Pontos Encontrados:

```
Mensagem: "Você possui [X] pontos de fidelidade disponíveis.
Seu nível atual é: [Nível].

Continue comprando para acumular mais pontos e subir de nível!"

Opções:
- Como ganhar mais pontos?
- Benefícios do programa
- Ver histórico de pontos
```

### 5. Atendimento Humano

#### 5.1 Atendimento Específico (Prioridade Alta)

##### Palavras-chave:
```
'falar com atendente', 'atendente humano', 'pessoa real'
'suporte técnico', 'preciso de ajuda'
```

##### Exemplos de Entrada:
- "Falar com atendente"
- "Quero falar com uma pessoa"
- "Preciso de ajuda humana"
- "Suporte técnico"

#### 5.2 Resposta:

```
Mensagem: "Entendo que você prefere falar com um atendente humano. Por favor, entre em contato pelo telefone (244) 923-456-789 ou pelo email atendimento@livraria-angola.com durante nosso horário comercial (8h às 18h)."
Opções:
- Voltar ao menu
- Buscar livros
- Meus pedidos
```

### 6. Ajuda Geral

#### 6.1 Palavras-chave (Prioridade Baixa):
```
'ajuda', 'como', 'o que', 'onde', 'quando'
```

#### 6.2 Exemplos de Entrada:
- "Como posso fazer um pedido?"
- "O que vocês vendem?"
- "Onde fica a loja?"
- "Preciso de ajuda"

#### 6.3 Resposta:

```
Mensagem: "Posso ajudar você com várias coisas! Escolha uma das opções abaixo:"
Opções:
- Buscar livros
- Consultar pedidos
- Ver pontos de fidelidade
- Falar com atendente
```

### 7. Mensagens Não Reconhecidas

#### 7.1 Exemplos:
- "xyz123"
- "blablabla"
- "asdfgh"
- Qualquer texto que não corresponda aos padrões

#### 7.2 Resposta:

```
Mensagem: "Desculpe, não entendi sua pergunta. Como posso ajudar você?"
Opções:
- Buscar livros
- Meus pedidos
- Pontos de fidelidade
- Falar com atendente
```

---

## 🔄 Fluxos de Conversa

### Fluxo 1: Busca de Livros

```mermaid
graph TD
    A[Usuário: "ficção"] --> B[Sistema: Mapeia para "ficção científica"]
    B --> C[Busca no banco de dados]
    C --> D{Encontrou livros?}
    D -->|Sim| E[Lista livros encontrados]
    D -->|Não| F[Mensagem "não encontrado"]
    E --> G[Opções: Ver mais, Buscar outro, Categorias]
    F --> H[Opções: Categorias, Por autor, Atendente]
```

### Fluxo 2: Consulta de Pedidos

```mermaid
graph TD
    A[Usuário: "meus pedidos"] --> B{Usuário logado?}
    B -->|Não| C[Solicita login]
    B -->|Sim| D{Tem perfil cliente?}
    D -->|Não| E[Solicita completar perfil]
    D -->|Sim| F[Busca pedidos no BD]
    F --> G{Tem pedidos?}
    G -->|Não| H[Mensagem "sem pedidos"]
    G -->|Sim| I[Lista pedidos recentes]
```

### Fluxo 3: Pontos de Fidelidade

```mermaid
graph TD
    A[Usuário: "meus pontos"] --> B{Usuário logado?}
    B -->|Não| C[Solicita login]
    B -->|Sim| D{Tem perfil cliente?}
    D -->|Não| E[Solicita completar perfil]
    D -->|Sim| F[Consulta LoyaltyService]
    F --> G[Mostra saldo e nível]
    G --> H[Opções: Ganhar mais, Benefícios, Histórico]
```

---

## 🗝️ Palavras-chave e Sinônimos

### Mapeamento de Sinônimos para Categorias

| Entrada do Usuário | Mapeado Para | Categoria BD |
|-------------------|--------------|--------------|
| ficção | ficção científica | Ficção Científica |
| sci-fi | ficção científica | Ficção Científica |
| scifi | ficção científica | Ficção Científica |
| romance | romance | Romance |
| romântico | romance | Romance |
| romântica | romance | Romance |
| biografia | biografia | Biografia |
| biográfico | biografia | Biografia |
| história | história | História |
| histórico | história | História |
| infantil | infantil | Infantil |
| criança | infantil | Infantil |
| crianças | infantil | Infantil |
| negócios | negócios | Negócios |
| business | negócios | Negócios |
| empresarial | negócios | Negócios |
| autoajuda | autoajuda | Autoajuda |
| auto-ajuda | autoajuda | Autoajuda |
| desenvolvimento | autoajuda | Autoajuda |

### Palavras Ignoradas na Busca

```php
$ignoreWords = [
    'livro', 'livros', 'buscar', 'procurar', 'encontrar', 'sobre', 'como', 
    'quero', 'gostaria', 'pode', 'por', 'favor', 'me', 'ajudar', 'busca',
    'um', 'uma', 'de', 'do', 'da', 'dos', 'das', 'para', 'com', 'em',
    'que', 'qual', 'onde', 'quando', 'porque', 'ver', 'mostrar', 'listar'
];
```

### Sistema de Prioridades

#### Prioridade Alta (Processadas Primeiro)
1. Pedidos específicos: "meus pedidos", "meu pedido"
2. Fidelidade específica: "meus pontos", "pontos de fidelidade"
3. Busca específica: "buscar livro", "quero um livro"
4. Atendimento específico: "falar com atendente"

#### Prioridade Média
1. Busca geral: "livro", "ficção", "romance"
2. Pedidos gerais: "pedido", "compra"
3. Fidelidade geral: "pontos", "fidelidade"

#### Prioridade Baixa
1. Ajuda geral: "ajuda", "como", "o que"

---

## 🔧 Estados e Contextos

### Estados do Usuário

#### 1. Usuário Anônimo (Não Logado)
- **Pode**: Buscar livros, ver categorias, falar com atendente
- **Não pode**: Ver pedidos, consultar pontos de fidelidade
- **Mensagens**: Solicita login para funcionalidades restritas

#### 2. Usuário Logado sem Perfil Cliente
- **Pode**: Buscar livros, ver categorias, falar com atendente
- **Não pode**: Ver pedidos, consultar pontos (sem perfil completo)
- **Mensagens**: Solicita completar perfil de cliente

#### 3. Usuário Logado com Perfil Cliente
- **Pode**: Todas as funcionalidades
- **Acesso**: Pedidos, pontos de fidelidade, histórico completo
- **Mensagens**: Respostas personalizadas com dados reais

### Contextos de Conversa

#### Contexto de Busca
- **Ativo quando**: Usuário está buscando livros
- **Mantém**: Termos de busca anteriores
- **Opções**: Refinar busca, ver mais resultados, mudar categoria

#### Contexto de Pedidos
- **Ativo quando**: Usuário consulta pedidos
- **Mantém**: Lista de pedidos carregada
- **Opções**: Ver detalhes, status, fazer novo pedido

#### Contexto de Fidelidade
- **Ativo quando**: Usuário consulta pontos
- **Mantém**: Saldo atual e nível
- **Opções**: Ver histórico, benefícios, como ganhar mais

---

## 🔗 Integração com Sistema

### Modelos Utilizados

#### Book Model
```php
// Campos consultados
- title (título)
- author (autor)  
- price (preço)
- category_id (categoria)

// Relacionamentos
- category (BookCategory)

// Consultas
Book::where('title', 'like', "%{$term}%")
    ->orWhere('author', 'like', "%{$term}%")
    ->orWhereHas('category', function($q) use ($term) {
        $q->where('name', 'like', "%{$term}%");
    })
```

#### Customer Model
```php
// Campos consultados
- email (para associar com usuário)
- name (nome do cliente)

// Relacionamentos
- invoices (pedidos/faturas)

// Consultas
Customer::where('email', Auth::user()->email)->first()
```

#### Invoice Model
```php
// Campos consultados
- customer_id (cliente)
- invoice_date (data do pedido)
- total_amount (valor total)
- status (status do pedido)

// Consultas
Invoice::where('customer_id', $customer->id)
       ->orderBy('invoice_date', 'desc')
       ->limit(3)
```

#### LoyaltyService
```php
// Métodos utilizados
getCustomerPoints($customerId)

// Retorna
- current_balance (saldo atual)
- level (nível do cliente)
```

### Status de Pedidos

| Status BD | Exibição | Descrição |
|-----------|----------|-----------|
| pending | Pendente | Aguardando pagamento |
| paid | Pago | Pagamento confirmado |
| delivered | Entregue | Pedido entregue |
| cancelled | Cancelado | Pedido cancelado |

---

## ⚙️ Configuração e Personalização

### Configurações do Chatbot

#### Interface (chatbot.js)
```javascript
// Configurações visuais
const config = {
    buttonColor: '#3490dc',
    containerWidth: '350px',
    containerHeight: '500px',
    animationSpeed: '0.2s',
    maxMessages: 50
};
```

#### Backend (ChatbotController.php)
```php
// Configurações de busca
private $searchLimit = 3;        // Máximo de livros retornados
private $recentOrdersLimit = 3;  // Máximo de pedidos recentes
private $minWordLength = 2;      // Tamanho mínimo de palavra para busca
```

### Personalização de Mensagens

#### Saudações Personalizadas
```php
// Em generateResponse()
if ($this->isGreeting($messageLower)) {
    $hour = date('H');
    $greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');
    
    return [
        'message' => "{$greeting}! Como posso ajudar você hoje?",
        // ...
    ];
}
```

#### Mensagens por Horário
```php
// Horário comercial
$isBusinessHours = date('H') >= 8 && date('H') <= 18;
$contactMessage = $isBusinessHours 
    ? "Nossa equipe está disponível agora!"
    : "Nossa equipe retorna às 8h. Deixe sua mensagem!";
```

### Adicionando Novas Intenções

#### 1. Adicionar Palavras-chave
```php
// Em generateResponse()
if ($this->containsAny($messageLower, ['nova_intencao', 'palavra_chave'])) {
    return $this->handleNovaIntencao($messageLower);
}
```

#### 2. Criar Handler
```php
private function handleNovaIntencao($message)
{
    // Lógica específica
    return [
        'message' => 'Resposta personalizada',
        'options' => ['Opção 1', 'Opção 2']
    ];
}
```

#### 3. Adicionar Sinônimos
```php
// Em extractSearchTerms()
$categoryMappings = [
    'novo_sinonimo' => 'categoria_existente',
    // ...
];
```

---

## 📊 Métricas e Monitoramento

### Logs Automáticos

#### Mensagens Processadas
```php
Log::info('Chatbot message processed', [
    'message' => $message,
    'response_type' => $responseType,
    'user_id' => Auth::id(),
    'timestamp' => now()
]);
```

#### Erros de Busca
```php
Log::warning('Chatbot search returned no results', [
    'search_terms' => $searchTerms,
    'message' => $message
]);
```

### Métricas Sugeridas

- **Taxa de resolução**: Mensagens resolvidas vs. direcionadas para atendente
- **Intenções mais comuns**: Quais tipos de pergunta são mais frequentes
- **Termos não reconhecidos**: Para melhorar o vocabulário
- **Tempo de resposta**: Performance da API

---

## 🚀 Melhorias Futuras

### Funcionalidades Planejadas

#### 1. Contexto de Conversa
- Lembrar mensagens anteriores na sessão
- Continuar conversas onde pararam
- Referências contextuais ("esse livro", "meu último pedido")

#### 2. Aprendizado de Máquina
- Análise de sentimento das mensagens
- Sugestões automáticas baseadas no histórico
- Melhoria contínua do reconhecimento

#### 3. Integrações Avançadas
- Notificações push quando pedidos chegarem
- Integração com WhatsApp/Telegram
- Suporte a imagens (capas de livros)

#### 4. Personalização Avançada
- Preferências de categoria por cliente
- Recomendações baseadas em compras anteriores
- Lembretes de livros em promoção

### Melhorias Técnicas

#### 1. Performance
- Cache de respostas frequentes
- Otimização de consultas ao banco
- Compressão de mensagens

#### 2. Escalabilidade
- Suporte a múltiplos idiomas
- API rate limiting
- Balanceamento de carga

#### 3. Segurança
- Validação rigorosa de entrada
- Prevenção de ataques de injeção
- Logs de segurança

---

## 🔍 Troubleshooting

### Problemas Comuns

#### Chatbot não responde
1. Verificar se JavaScript está habilitado
2. Conferir token CSRF no cabeçalho
3. Verificar logs do Laravel (`storage/logs/laravel.log`)

#### Respostas incorretas
1. Verificar ordem de prioridade das intenções
2. Adicionar palavras-chave específicas
3. Melhorar mapeamento de sinônimos

#### Busca não encontra livros
1. Verificar se livros estão ativos no BD
2. Conferir relacionamentos entre Book e Category
3. Validar termos extraídos da mensagem

### Comandos de Debug

```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Testar API diretamente
curl -X POST http://localhost:8000/api/chatbot \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: token" \
  -d '{"message":"teste"}'

# Limpar cache
php artisan cache:clear
php artisan config:clear
```

---

## 📚 Exemplos de Uso Completos

### Exemplo 1: Busca de Livro Específico

**Entrada**: "Quero livros de Isaac Asimov"

**Processamento**:
1. Detecta intenção: Busca específica (prioridade alta)
2. Extrai termos: ["isaac", "asimov"]
3. Consulta BD: `Book::where('author', 'like', '%isaac%')->orWhere('author', 'like', '%asimov%')`
4. Encontra: "Fundação"

**Saída**:
```
Mensagem: "Encontrei estes livros para você:
- Fundação por Isaac Asimov - Kz 54.90
Gostaria de mais informações sobre algum deles?"

Opções:
- Ver mais livros
- Buscar outro livro  
- Ver categorias
```

### Exemplo 2: Consulta de Pedidos (Usuário Logado)

**Entrada**: "Meus pedidos"

**Processamento**:
1. Detecta intenção: Pedidos específicos (prioridade alta)
2. Verifica autenticação: ✅ Logado
3. Busca cliente: ✅ Encontrado
4. Consulta pedidos: 2 pedidos encontrados

**Saída**:
```
Mensagem: "Aqui estão seus pedidos mais recentes:
- Pedido #123 (15/11/2025) - Kz 89.90 - Status: Entregue
- Pedido #124 (20/11/2025) - Kz 154.80 - Status: Pago

Gostaria de ver mais detalhes de algum pedido específico?"

Opções:
- Ver todos os pedidos
- Status de entrega
- Voltar ao menu
```

### Exemplo 3: Consulta de Pontos (Usuário Não Logado)

**Entrada**: "Quantos pontos eu tenho?"

**Processamento**:
1. Detecta intenção: Fidelidade específica (prioridade alta)
2. Verifica autenticação: ❌ Não logado
3. Retorna mensagem de login

**Saída**:
```
Mensagem: "Para verificar seus pontos de fidelidade, você precisa estar logado. Por favor, faça login na sua conta."

Opções:
- Como fazer login?
- Voltar ao menu
- Falar com atendente
```

---

**Documentação criada em**: Novembro 2025  
**Versão**: 2.0.0  
**Autor**: Equipe CRM Livraria  
**Última atualização**: 25/11/2025
