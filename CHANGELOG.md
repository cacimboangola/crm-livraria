# 📝 Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [Unreleased]

### Planejado
- Sistema de cupons de desconto
- Integração com gateway de pagamento
- App mobile para clientes
- Dashboard de analytics avançado
- Exportação de relatórios em Excel
- Sistema de avaliações de livros
- Wishlist de clientes
- Notificações push

---

## [1.0.0] - 2025-01-20

### 🎉 Lançamento Inicial

Primeira versão estável do CRM Livraria com funcionalidades completas.

### ✨ Adicionado

#### Módulo de Clientes
- CRUD completo de clientes
- Histórico de compras
- Análise de comportamento
- Segmentação para campanhas
- Busca e filtros avançados

#### Módulo de Livros
- Gestão de livros e categorias
- Controle de estoque
- Upload de capas
- Preços e descontos
- Filtros por categoria e busca

#### Sistema de Vendas
- Emissão de faturas
- Múltiplos métodos de pagamento (Dinheiro, Cartão, Transferência, PIX)
- Gestão de status (Pendente, Paga, Cancelada)
- Geração de PDF
- Envio de faturas por email
- Aplicação de descontos
- Resgate de pontos de fidelidade

#### Programa de Fidelidade
- Ganho automático de pontos (1 ponto = 1€)
- Resgate de pontos como desconto (100 pontos = 10€)
- Pontos de bônus via campanhas
- Expiração automática (365 dias)
- Dashboard de pontos para clientes
- Histórico de transações
- Comando Artisan para processar expiração

#### Campanhas de Marketing
- Criação e gestão de campanhas
- Seleção manual de clientes
- Seleção automática por critérios
- Distribuição de pontos de bônus
- Envio de emails em massa (via fila)
- Rastreamento de métricas:
  - Taxa de abertura
  - Taxa de cliques
  - Taxa de conversão
- Dashboard de métricas

#### Sistema de Recomendações
- Livros populares
- Recomendações personalizadas por cliente
- Livros similares
- Clientes potenciais para um livro
- Algoritmos baseados em histórico de compras

#### Sistema de Notificações
- Notificações em tempo real
- Tipos: Fatura, Fidelidade, Campanha, Estoque
- Badge com contador
- Marcar como lida
- Histórico completo

#### Portal do Cliente
- Catálogo público de livros
- Carrinho de compras
- Checkout simplificado
- Histórico de pedidos
- Dashboard de fidelidade
- Gestão de perfil
- Download de faturas em PDF

#### Chatbot
- API de chatbot
- Respostas inteligentes
- Busca de livros
- Consulta de pedidos
- Informações de fidelidade

#### Autenticação e Autorização
- Sistema de login/registro
- Recuperação de senha
- Middleware de admin
- Middleware de cliente
- Roles: admin, customer

#### Interface e UX
- Design moderno com Tailwind CSS
- Componentes Livewire reativos
- Responsivo (desktop, tablet, mobile)
- Dark mode ready
- Feedback visual em todas as ações
- Validação em tempo real

### 🔧 Técnico

#### Arquitetura
- Padrão Service Layer
- Controllers finos
- Services para lógica de negócio
- Models magros
- Transações de banco de dados
- Eventos e Listeners

#### Performance
- Eager loading para evitar N+1
- Cache de configurações
- Cache de rotas
- Cache de views
- Paginação em todas as listagens
- Filas para tarefas pesadas

#### Segurança
- Proteção CSRF
- Validação de inputs
- Mass assignment protection
- SQL injection prevention
- XSS prevention
- Autenticação segura

#### Testes
- Testes unitários
- Testes de integração
- Cobertura de código
- CI/CD ready

#### Documentação
- README completo
- Documentação de arquitetura
- Documentação de API
- Documentação de módulos
- Guia de deploy
- Guia de contribuição

### 🐛 Correções

#### Bug no LoyaltyService (CRÍTICO)
- **Problema**: InvoiceService passava parâmetros incorretos para addPoints()
- **Localização**: `app/Services/InvoiceService.php` linhas 322-328 e 348-354
- **Correção**: Removidos parâmetros extras, passando apenas o objeto Invoice como 4º parâmetro

#### Filtros de Faturas Não Funcionando
- **Problema**: Método index() não processava filtros da URL
- **Localização**: `app/Http/Controllers/InvoiceController.php`
- **Correção**: Modificado index() para aceitar Request e processar filtros
- **Correção**: Modificado getAllPaginated() no InvoiceService para aplicar filtros

#### Paginação Perdendo Filtros
- **Problema**: Links de paginação não preservavam parâmetros de filtro
- **Localização**: `resources/views/invoices/index.blade.php`
- **Correção**: Adicionado appends(request()->query()) aos links de paginação

#### Query Incorreta em removeLoyaltyPoints
- **Problema**: Usando campos inexistentes (source_type, source_id)
- **Localização**: `app/Services/InvoiceService.php`
- **Correção**: Alterado para usar invoice_id e type='earn'

### 📦 Dependências

#### Backend
- Laravel 12.0
- PHP 8.2+
- Laravel UI 4.6
- Livewire 3.0
- DomPDF 3.1

#### Frontend
- Tailwind CSS 3.x
- Bootstrap 5.x
- Alpine.js (via Livewire)
- Vite

#### Desenvolvimento
- Laravel Pint (code style)
- Pest (testing)
- Laravel Sail (Docker)

---

## [0.9.0] - 2025-01-15

### Beta Release

#### Adicionado
- Estrutura base do projeto
- Migrations iniciais
- Seeders de exemplo
- Controllers principais
- Services principais
- Views básicas

#### Em Desenvolvimento
- Sistema de fidelidade
- Campanhas de marketing
- Portal do cliente

---

## [0.5.0] - 2025-01-10

### Alpha Release

#### Adicionado
- Configuração inicial do Laravel
- Autenticação básica
- CRUD de clientes
- CRUD de livros
- Sistema de faturas básico

---

## Tipos de Mudanças

- `Added` - Novas funcionalidades
- `Changed` - Mudanças em funcionalidades existentes
- `Deprecated` - Funcionalidades que serão removidas
- `Removed` - Funcionalidades removidas
- `Fixed` - Correções de bugs
- `Security` - Correções de segurança

---

## Links

- [Unreleased]: https://github.com/seu-usuario/crm-livraria/compare/v1.0.0...HEAD
- [1.0.0]: https://github.com/seu-usuario/crm-livraria/releases/tag/v1.0.0
- [0.9.0]: https://github.com/seu-usuario/crm-livraria/releases/tag/v0.9.0
- [0.5.0]: https://github.com/seu-usuario/crm-livraria/releases/tag/v0.5.0
