# 📚 CRM Livraria

Sistema completo de CRM (Customer Relationship Management) para livrarias, desenvolvido com Laravel 12, Livewire 3 e Tailwind CSS.

## 🎯 Sobre o Projeto

O CRM Livraria é uma aplicação web moderna e robusta que oferece gestão completa para livrarias, incluindo:

- **Gestão de Clientes**: Cadastro completo com histórico de compras e preferências
- **Catálogo de Livros**: Gerenciamento de livros, categorias e estoque
- **Sistema de Vendas**: Emissão de faturas com múltiplos métodos de pagamento
- **Portal do Cliente**: Interface dedicada para clientes realizarem compras online
- **Programa de Fidelidade**: Sistema de pontos com ganho automático e resgate
- **Campanhas de Marketing**: Criação e gestão de campanhas com rastreamento de métricas
- **Sistema de Recomendações**: Sugestões inteligentes baseadas em histórico de compras
- **Pedidos Especiais**: Sistema completo de acompanhamento com notificações automáticas
- **Notificações**: Sistema completo de notificações em tempo real
- **Chatbot Inteligente**: Assistente virtual com IA para atendimento e consultas

### 💡 O que é CRM?

**CRM** (Customer Relationship Management) é uma estratégia de negócio focada em construir relacionamentos duradouros com clientes. Este sistema combina:

- **CRM Operacional** (55%): Automatização de vendas, marketing e atendimento
- **CRM Analítico** (30%): Análise de dados e comportamento para decisões estratégicas
- **CRM Colaborativo** (15%): Comunicação entre equipes e portal self-service

📖 **[Leia o Guia Completo sobre CRM](docs/CRM-GUIDE.md)** para entender os diferentes tipos de CRM e como este sistema se encaixa.

## 🚀 Tecnologias Utilizadas

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Livewire 3 + Blade Templates
- **Estilização**: Tailwind CSS + Bootstrap 5
- **Banco de Dados**: MySQL
- **Geração de PDF**: DomPDF
- **Autenticação**: Laravel UI
- **Filas**: Database Queue Driver
- **Cache**: Database Cache Driver

## 📋 Pré-requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18.x e NPM
- MySQL >= 8.0
- Extensões PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo

## 🔧 Instalação

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd crm-livraria
```

### 2. Instale as dependências

```bash
# Dependências PHP
composer install

# Dependências JavaScript
npm install
```

### 3. Configure o ambiente

```bash
# Copie o arquivo de exemplo
copy .env.example .env

# Gere a chave da aplicação
php artisan key:generate
```

### 4. Configure o banco de dados

Edite o arquivo `.env` com suas credenciais:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_livraria
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

### 5. Execute as migrations e seeders

```bash
# Criar tabelas e popular com dados de exemplo
php artisan migrate:fresh --seed
```

### 6. Compile os assets

```bash
# Desenvolvimento
npm run dev

# Produção
npm run build
```

### 7. Inicie o servidor

```bash
# Opção 1: Comando único (recomendado)
composer run dev

# Opção 2: Comandos separados
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

Acesse: `http://localhost:8000`

## 👥 Usuários Padrão

Após executar os seeders, você terá acesso aos seguintes usuários:

### Administrador
- **Email**: admin@livraria.com
- **Senha**: password
- **Acesso**: Dashboard administrativo completo

### Cliente de Teste
- **Email**: cliente@example.com
- **Senha**: password
- **Acesso**: Portal do cliente

## 📁 Estrutura do Projeto

```
crm-livraria/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controllers principais
│   │   │   ├── Auth/             # Autenticação
│   │   │   ├── Customer/         # Portal do cliente
│   │   │   └── Api/              # APIs
│   │   └── Middleware/           # Middlewares customizados
│   ├── Models/                   # Eloquent Models
│   ├── Services/                 # Camada de serviços (lógica de negócio)
│   │   ├── BookService.php
│   │   ├── CustomerService.php
│   │   ├── InvoiceService.php
│   │   ├── LoyaltyService.php
│   │   ├── CampaignService.php
│   │   ├── NotificationService.php
│   │   └── RecommendationService.php
│   └── Livewire/                 # Componentes Livewire
├── database/
│   ├── migrations/               # Migrations do banco
│   ├── seeders/                  # Seeders
│   └── factories/                # Factories para testes
├── resources/
│   ├── views/                    # Blade templates
│   │   ├── layouts/              # Layouts principais
│   │   ├── customers/            # Views de clientes
│   │   ├── books/                # Views de livros
│   │   ├── invoices/             # Views de faturas
│   │   ├── campaigns/            # Views de campanhas
│   │   ├── loyalty/              # Views de fidelidade
│   │   └── customer/             # Portal do cliente
│   └── js/                       # JavaScript
└── routes/
    ├── web.php                   # Rotas web
    └── api.php                   # Rotas API
```

## 🏗️ Arquitetura

O projeto segue o padrão **Service Layer**, separando responsabilidades:

### Controllers
- Finos e focados em HTTP
- Delegam lógica de negócio para Services
- Tratam validação via Form Requests

### Services
- Centralizam regras de negócio
- Reutilizáveis entre controllers
- Gerenciam transações e integrações

### Models
- Apenas relacionamentos e scopes
- Sem lógica de negócio complexa
- Seguem convenções Eloquent

### Livewire Components
- Componentes reativos para UI dinâmica
- Gerenciam estado do frontend
- Comunicação em tempo real

## 🔐 Autenticação e Autorização

### Middleware Customizado

- **AdminMiddleware**: Restringe acesso ao painel administrativo
- **CustomerMiddleware**: Protege rotas do portal do cliente

### Roles de Usuário

- **admin**: Acesso total ao sistema
- **customer**: Acesso ao portal do cliente

## 📊 Módulos Principais

### 1. Gestão de Clientes
- CRUD completo de clientes
- Histórico de compras
- Análise de comportamento
- Segmentação para campanhas

### 2. Catálogo de Livros
- Gestão de livros e categorias
- Controle de estoque
- Preços e descontos
- Upload de capas

### 3. Sistema de Vendas
- Emissão de faturas
- Múltiplos métodos de pagamento (Dinheiro, Cartão, Transferência, PIX)
- Gestão de status (Pendente, Paga, Cancelada)
- Geração de PDF
- Envio por email

### 4. Programa de Fidelidade
- Ganho automático de pontos em compras (1 ponto = 1€)
- Resgate de pontos como desconto
- Pontos de bônus via campanhas
- Expiração automática de pontos (365 dias)
- Dashboard de pontos para clientes

### 5. Campanhas de Marketing
- Criação de campanhas segmentadas
- Seleção manual ou automática de clientes
- Distribuição de pontos de bônus
- Envio de emails em massa
- Rastreamento de métricas:
  - Taxa de abertura
  - Taxa de cliques
  - Taxa de conversão

### 6. Sistema de Recomendações
- Livros populares
- Recomendações personalizadas por cliente
- Livros similares
- Clientes potenciais para um livro

### 7. Portal do Cliente
- Catálogo de livros público
- Carrinho de compras
- Checkout simplificado
- Histórico de pedidos
- Gestão de perfil
- Dashboard de fidelidade
- Acompanhamento de pedidos especiais
- Sistema de notificações integrado

### 8. Sistema de Pedidos Especiais
- Solicitação via chatbot ou interface web
- Timeline visual de acompanhamento
- Notificações automáticas por mudança de status
- Interface dedicada para clientes e administradores
- Workflow completo: solicitação → encomenda → recebimento → entrega
- Métricas de performance e tempo de atendimento

### 9. Chatbot Inteligente
- Reconhecimento de intenções com IA
- Consulta de pedidos especiais
- Criação de pedidos via formulário integrado
- Busca de livros no catálogo
- Redirecionamento para páginas específicas
- Suporte 24/7 automatizado

## 🔄 Sistema de Filas

O projeto utiliza filas para processar tarefas assíncronas:

```bash
# Processar filas
php artisan queue:work

# Processar com retry
php artisan queue:listen --tries=3
```

### Jobs Implementados
- Envio de emails de campanhas
- Processamento de notificações
- Expiração de pontos de fidelidade

## 🧪 Testes

```bash
# Executar todos os testes
php artisan test

# Executar com cobertura
php artisan test --coverage

# Limpar cache antes dos testes
composer run test
```

## 📧 Configuração de Email

Para desenvolvimento, o sistema usa `MAIL_MAILER=log`. Para produção, configure SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_username
MAIL_PASSWORD=sua_senha
MAIL_FROM_ADDRESS="noreply@livraria.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 🎨 Personalização

### Tailwind CSS

O projeto usa Tailwind CSS. Para customizar:

```bash
# Edite tailwind.config.js
# Recompile os assets
npm run dev
```

### Cores do Sistema

Definidas em `tailwind.config.js`:
- Primary: Blue
- Success: Green
- Warning: Yellow
- Danger: Red

## 🚀 Deploy em Produção

### 1. Otimize a aplicação

```bash
# Cache de configuração
php artisan config:cache

# Cache de rotas
php artisan route:cache

# Cache de views
php artisan view:cache

# Otimize autoload
composer install --optimize-autoloader --no-dev
```

### 2. Compile assets para produção

```bash
npm run build
```

### 3. Configure o ambiente

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

# Use Redis para melhor performance
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### 4. Configure o servidor web

Aponte o document root para `/public`

### 5. Configure permissões

```bash
chmod -R 755 storage bootstrap/cache
```

## 🐛 Troubleshooting

### Erro de permissão em storage/

```bash
chmod -R 775 storage bootstrap/cache
```

### Erro "Class not found"

```bash
composer dump-autoload
```

### Assets não carregam

```bash
npm run build
php artisan storage:link
```

### Filas não processam

```bash
php artisan queue:restart
php artisan queue:listen
```

## 📝 Convenções de Código

- **PSR-12**: Padrão de código PHP
- **Nomenclatura**: 
  - Controllers: `PascalCase` + `Controller`
  - Services: `PascalCase` + `Service`
  - Models: `PascalCase` (singular)
  - Migrations: `snake_case`
  - Views: `kebab-case`

## 🤝 Contribuindo

1. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
2. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
3. Push para a branch (`git push origin feature/MinhaFeature`)
4. Abra um Pull Request

### Padrões de Commit

- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `docs:` Documentação
- `style:` Formatação
- `refactor:` Refatoração
- `test:` Testes
- `chore:` Manutenção

## 📄 Licença

Este projeto é licenciado sob a [MIT License](https://opensource.org/licenses/MIT).

## 👨‍💻 Autor

Desenvolvido com ❤️ para gestão moderna de livrarias.

## 📚 Documentação Adicional

- 📖 **[Guia Completo sobre CRM](docs/CRM-GUIDE.md)** - Entenda os tipos de CRM e como este sistema se encaixa
- 🏗️ **[Arquitetura do Sistema](docs/ARCHITECTURE.md)** - Padrões arquiteturais e estrutura do código
- 📡 **[Documentação da API](docs/API.md)** - Endpoints, exemplos e integrações
- 📚 **[Sistema de Pedidos Especiais](docs/SPECIAL-ORDERS-TRACKING.md)** - Funcionalidade completa de acompanhamento
- 🚀 **[Guia de Deploy](docs/DEPLOYMENT.md)** - Como colocar em produção

## 📞 Suporte

Para dúvidas ou problemas:
- Abra uma issue no repositório
- Entre em contato via email
- Consulte a documentação completa

---

## 🎉 Changelog

### v2.1.0 (2025-11-26) - Sistema de Acompanhamento de Pedidos Especiais
- ✅ **Interface completa para clientes** acompanharem pedidos especiais
- ✅ **Timeline visual** com status em tempo real
- ✅ **Notificações automáticas** por mudança de status
- ✅ **Chatbot inteligente** expandido com consulta de pedidos
- ✅ **Sistema de notificações** avançado com links diretos
- ✅ **Documentação completa** atualizada

### v2.0.0 (2025-11-25) - Campanhas e Pedidos Especiais
- ✅ Sistema completo de campanhas de marketing
- ✅ Rastreamento avançado (abertura, cliques, conversões)
- ✅ Gestão administrativa de pedidos especiais
- ✅ Webhooks para integrações externas

### v1.0.0 (2025-01-20) - Lançamento Inicial
- ✅ CRM completo para livrarias
- ✅ Portal do cliente
- ✅ Sistema de fidelidade
- ✅ Chatbot básico

---

**Nota**: Este é um projeto educacional/comercial. Sinta-se livre para adaptá-lo às suas necessidades.
