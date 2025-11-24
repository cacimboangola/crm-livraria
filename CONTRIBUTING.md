# 🤝 Guia de Contribuição

Obrigado por considerar contribuir com o CRM Livraria! Este documento fornece diretrizes para contribuir com o projeto.

## 📋 Índice

- [Código de Conduta](#código-de-conduta)
- [Como Posso Contribuir?](#como-posso-contribuir)
- [Processo de Desenvolvimento](#processo-de-desenvolvimento)
- [Padrões de Código](#padrões-de-código)
- [Commits e Pull Requests](#commits-e-pull-requests)
- [Testes](#testes)
- [Documentação](#documentação)

---

## Código de Conduta

Este projeto adere a um código de conduta. Ao participar, espera-se que você mantenha este código:

- **Seja respeitoso**: Trate todos com respeito e consideração
- **Seja colaborativo**: Trabalhe em conjunto para melhorar o projeto
- **Seja construtivo**: Forneça feedback construtivo e aceite críticas
- **Seja inclusivo**: Seja acolhedor com novos contribuidores

---

## Como Posso Contribuir?

### Reportar Bugs

Antes de criar um relatório de bug:

1. **Verifique a documentação** para confirmar que é um bug
2. **Pesquise issues existentes** para evitar duplicatas
3. **Colete informações** sobre o ambiente e passos para reproduzir

**Template de Bug Report:**

```markdown
**Descrição do Bug**
Uma descrição clara e concisa do bug.

**Passos para Reproduzir**
1. Vá para '...'
2. Clique em '...'
3. Role até '...'
4. Veja o erro

**Comportamento Esperado**
O que deveria acontecer.

**Comportamento Atual**
O que está acontecendo.

**Screenshots**
Se aplicável, adicione screenshots.

**Ambiente**
- OS: [e.g. Ubuntu 20.04]
- PHP: [e.g. 8.2.0]
- Laravel: [e.g. 12.0]
- Browser: [e.g. Chrome 120]

**Informações Adicionais**
Qualquer contexto adicional sobre o problema.
```

### Sugerir Melhorias

**Template de Feature Request:**

```markdown
**Descrição da Funcionalidade**
Uma descrição clara da funcionalidade desejada.

**Problema que Resolve**
Qual problema esta funcionalidade resolve?

**Solução Proposta**
Como você imagina que esta funcionalidade funcionaria?

**Alternativas Consideradas**
Quais outras soluções você considerou?

**Contexto Adicional**
Screenshots, mockups, ou qualquer contexto adicional.
```

### Contribuir com Código

1. **Fork o repositório**
2. **Crie uma branch** para sua feature/fix
3. **Faça suas alterações**
4. **Escreva/atualize testes**
5. **Atualize a documentação**
6. **Submeta um Pull Request**

---

## Processo de Desenvolvimento

### 1. Configurar Ambiente de Desenvolvimento

```bash
# Clone seu fork
git clone https://github.com/seu-usuario/crm-livraria.git
cd crm-livraria

# Adicione o repositório original como upstream
git remote add upstream https://github.com/original/crm-livraria.git

# Instale dependências
composer install
npm install

# Configure o ambiente
cp .env.example .env
php artisan key:generate

# Execute migrations
php artisan migrate:fresh --seed

# Inicie o servidor
composer run dev
```

### 2. Criar uma Branch

```bash
# Atualize seu fork
git checkout main
git pull upstream main

# Crie uma nova branch
git checkout -b feature/nome-da-feature
# ou
git checkout -b fix/nome-do-bug
```

**Convenção de Nomes de Branch:**

- `feature/` - Nova funcionalidade
- `fix/` - Correção de bug
- `docs/` - Apenas documentação
- `refactor/` - Refatoração de código
- `test/` - Adição/correção de testes
- `chore/` - Manutenção/tarefas

### 3. Fazer Alterações

- Siga os [Padrões de Código](#padrões-de-código)
- Escreva código limpo e legível
- Adicione comentários quando necessário
- Mantenha commits pequenos e focados

### 4. Testar

```bash
# Execute os testes
php artisan test

# Verifique o estilo de código
./vendor/bin/pint

# Execute análise estática (se configurado)
./vendor/bin/phpstan analyse
```

### 5. Commit

```bash
# Adicione os arquivos
git add .

# Faça o commit seguindo o padrão
git commit -m "feat: adiciona sistema de cupons de desconto"
```

### 6. Push e Pull Request

```bash
# Push para seu fork
git push origin feature/nome-da-feature

# Abra um Pull Request no GitHub
```

---

## Padrões de Código

### PHP (PSR-12)

O projeto segue o padrão **PSR-12** para código PHP.

```php
<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Collection;

class CustomerService
{
    /**
     * Criar novo cliente
     *
     * @param array $data
     * @return Customer
     */
    public function create(array $data): Customer
    {
        return Customer::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);
    }
}
```

**Regras:**

- Indentação: 4 espaços
- Chaves em nova linha para classes e métodos
- Chaves na mesma linha para estruturas de controle
- Sempre use type hints
- Sempre use return types
- DocBlocks para métodos públicos

### JavaScript

```javascript
// Use const/let, nunca var
const items = [];
let count = 0;

// Arrow functions quando apropriado
const double = (n) => n * 2;

// Nomes descritivos
function calculateTotalPrice(items) {
    return items.reduce((sum, item) => sum + item.price, 0);
}

// Comentários quando necessário
// Calcula o desconto baseado no total
function calculateDiscount(total) {
    if (total > 100) {
        return total * 0.1; // 10% de desconto
    }
    return 0;
}
```

### Blade Templates

```blade
{{-- Comentários em Blade --}}

{{-- Use @auth, @guest, etc. --}}
@auth
    <p>Bem-vindo, {{ auth()->user()->name }}</p>
@endauth

{{-- Sempre escape output ({{ }}) --}}
<p>{{ $customer->name }}</p>

{{-- Use {!! !!} apenas quando necessário --}}
<div>{!! $htmlContent !!}</div>

{{-- Components reutilizáveis --}}
<x-button type="primary">Salvar</x-button>

{{-- Diretivas personalizadas --}}
@can('edit', $post)
    <a href="{{ route('posts.edit', $post) }}">Editar</a>
@endcan
```

### CSS/Tailwind

```html
<!-- Use classes utilitárias do Tailwind -->
<div class="flex items-center justify-between p-4 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold text-gray-800">Título</h2>
    <button class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
        Ação
    </button>
</div>

<!-- Agrupe classes relacionadas -->
<div class="
    flex items-center justify-between
    p-4 m-2
    bg-white rounded-lg shadow
    hover:shadow-lg transition-shadow
">
    Conteúdo
</div>
```

### Nomenclatura

#### Classes

```php
// PascalCase
class CustomerService {}
class InvoiceController {}
```

#### Métodos

```php
// camelCase
public function createInvoice() {}
public function getCustomerById() {}
```

#### Variáveis

```php
// camelCase
$customerName = 'João';
$totalAmount = 150.00;
```

#### Constantes

```php
// UPPER_SNAKE_CASE
const MAX_ITEMS = 100;
const DEFAULT_CURRENCY = 'EUR';
```

#### Tabelas de Banco

```sql
-- snake_case, plural
customers
book_categories
invoice_items
```

#### Colunas de Banco

```sql
-- snake_case
customer_id
created_at
is_active
```

---

## Commits e Pull Requests

### Mensagens de Commit

Siga o padrão **Conventional Commits**:

```
<tipo>(<escopo>): <descrição>

[corpo opcional]

[rodapé opcional]
```

**Tipos:**

- `feat`: Nova funcionalidade
- `fix`: Correção de bug
- `docs`: Documentação
- `style`: Formatação (não afeta código)
- `refactor`: Refatoração
- `test`: Testes
- `chore`: Manutenção

**Exemplos:**

```bash
feat(loyalty): adiciona sistema de níveis de fidelidade

fix(invoice): corrige cálculo de desconto em faturas

docs(api): atualiza documentação da API de chatbot

refactor(services): simplifica lógica do CustomerService

test(invoice): adiciona testes para criação de faturas

chore(deps): atualiza dependências do composer
```

### Pull Requests

**Template de PR:**

```markdown
## Descrição

Breve descrição das mudanças.

## Tipo de Mudança

- [ ] Bug fix
- [ ] Nova funcionalidade
- [ ] Breaking change
- [ ] Documentação

## Como Testar

1. Passo 1
2. Passo 2
3. Passo 3

## Checklist

- [ ] Código segue os padrões do projeto
- [ ] Comentários adicionados em código complexo
- [ ] Documentação atualizada
- [ ] Testes adicionados/atualizados
- [ ] Todos os testes passam
- [ ] Sem warnings do linter

## Screenshots (se aplicável)

Adicione screenshots para mudanças visuais.

## Issues Relacionadas

Closes #123
Relates to #456
```

**Boas Práticas:**

- Mantenha PRs pequenos e focados
- Um PR = Uma funcionalidade/fix
- Descreva claramente as mudanças
- Adicione screenshots para mudanças visuais
- Responda aos comentários de revisão
- Mantenha o PR atualizado com a branch main

---

## Testes

### Estrutura de Testes

```
tests/
├── Feature/           # Testes de integração
│   ├── CustomerTest.php
│   ├── InvoiceTest.php
│   └── LoyaltyTest.php
└── Unit/             # Testes unitários
    ├── Services/
    │   ├── CustomerServiceTest.php
    │   └── InvoiceServiceTest.php
    └── Models/
        └── CustomerTest.php
```

### Escrever Testes

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_customer()
    {
        $data = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'phone' => '123456789',
        ];

        $response = $this->post('/customers', $data);

        $response->assertRedirect('/customers');
        $this->assertDatabaseHas('customers', [
            'email' => 'joao@example.com',
        ]);
    }

    /** @test */
    public function it_requires_name_and_email()
    {
        $response = $this->post('/customers', []);

        $response->assertSessionHasErrors(['name', 'email']);
    }
}
```

### Executar Testes

```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter CustomerTest

# Com cobertura
php artisan test --coverage

# Paralelo
php artisan test --parallel
```

---

## Documentação

### Atualizar Documentação

Sempre atualize a documentação quando:

- Adicionar nova funcionalidade
- Modificar comportamento existente
- Adicionar/modificar APIs
- Alterar configurações

### Locais de Documentação

- `README.md` - Visão geral e instalação
- `docs/ARCHITECTURE.md` - Arquitetura do sistema
- `docs/API.md` - Documentação da API
- `docs/MODULES.md` - Documentação dos módulos
- `docs/DEPLOYMENT.md` - Guia de deploy
- `CONTRIBUTING.md` - Este arquivo

### Estilo de Documentação

- Use Markdown
- Seja claro e conciso
- Adicione exemplos de código
- Use listas e tabelas quando apropriado
- Mantenha atualizado

---

## Processo de Revisão

### Para Revisores

- Seja construtivo e respeitoso
- Explique o "porquê" das sugestões
- Aprove quando estiver satisfeito
- Solicite mudanças quando necessário

### Para Contribuidores

- Responda aos comentários
- Faça as alterações solicitadas
- Marque conversas como resolvidas
- Seja receptivo ao feedback

---

## Reconhecimento

Todos os contribuidores serão reconhecidos no projeto. Obrigado por tornar o CRM Livraria melhor!

---

## Dúvidas?

Se tiver dúvidas sobre como contribuir:

- Abra uma issue com a tag `question`
- Entre em contato com os mantenedores
- Consulte a documentação do projeto

---

**Obrigado por contribuir! 🎉**
