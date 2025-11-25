# 📚 Documentação do CRM Livraria

Bem-vindo à documentação completa do CRM Livraria! Este índice irá guiá-lo através de toda a documentação disponível.

## 📖 Índice Geral

### 🚀 Começando

- **[README Principal](../README.md)** - Visão geral, instalação e primeiros passos
- **[📖 Manual do Usuário](USER-GUIDE.md)** - Guia completo para clientes e administradores
- **[Guia sobre CRM](CRM-GUIDE.md)** - O que é CRM, tipos e funcionalidades
- **[FAQ](FAQ.md)** - Perguntas frequentes e troubleshooting
- **[Guia de Contribuição](../CONTRIBUTING.md)** - Como contribuir com o projeto
- **[Changelog](../CHANGELOG.md)** - Histórico de mudanças e versões

### 🏗️ Arquitetura e Desenvolvimento

- **[Arquitetura do Sistema](ARCHITECTURE.md)** - Padrões arquiteturais, camadas e fluxos
  - Service Layer Pattern
  - Estrutura de Controllers, Services e Models
  - Transações e eventos
  - Boas práticas de código

### 📦 Módulos e Funcionalidades

- **[Documentação dos Módulos](MODULES.md)** - Detalhamento completo de cada módulo
  - Gestão de Clientes
  - Catálogo de Livros
  - Sistema de Vendas (Faturas)
  - Programa de Fidelidade
  - **[Campanhas de Marketing](CAMPAIGNS.md)** - Sistema completo de email marketing
  - Pedidos Especiais
  - Sistema de Recomendações
  - Notificações
  - Portal do Cliente
  - Chatbot

### 🔌 API e Integrações

- **[Documentação da API](API.md)** - Endpoints, autenticação e exemplos
  - API do Chatbot
  - Rastreamento de Campanhas
  - Notificações
  - Webhooks
  - Rate limiting

### 🚀 Deploy e Produção

- **[Guia de Deploy](DEPLOYMENT.md)** - Deploy completo em produção
  - Requisitos de servidor
  - Configuração de ambiente
  - Nginx/Apache
  - SSL/HTTPS
  - Filas e Supervisor
  - Backups
  - Monitoramento
  - Atualizações

---

## 🎯 Guias Rápidos

### Para Desenvolvedores

1. **Instalação Local**
   ```bash
   git clone <repo>
   composer install && npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate:fresh --seed
   composer run dev
   ```

2. **Estrutura do Código**
   - Controllers: `app/Http/Controllers/`
   - Services: `app/Services/`
   - Models: `app/Models/`
   - Views: `resources/views/`
   - Livewire: `app/Livewire/`

3. **Executar Testes**
   ```bash
   php artisan test
   ```

4. **Padrões de Código**
   - PSR-12 para PHP
   - Conventional Commits para mensagens
   - Service Layer para lógica de negócio

### Para Administradores

1. **Acesso Inicial**
   - URL: `http://localhost:8000`
   - Email: `admin@livraria.com`
   - Senha: `password`

2. **Tarefas Comuns**
   - Criar cliente: Dashboard > Clientes > Novo
   - Emitir fatura: Dashboard > Faturas > Nova
   - Criar campanha: Dashboard > Campanhas > Nova
   - Ver métricas: Dashboard > Campanhas > Métricas

3. **Manutenção**
   - Processar expiração de pontos: `php artisan loyalty:process-expiration`
   - Limpar cache: `php artisan cache:clear`
   - Ver logs: `storage/logs/laravel.log`

### Para Clientes

1. **Portal do Cliente**
   - URL: `http://localhost:8000/cliente/dashboard`
   - Funcionalidades:
     - Navegar catálogo
     - Adicionar ao carrinho
     - Finalizar compra
     - Ver histórico de pedidos
     - Consultar pontos de fidelidade

---

## 🔍 Busca Rápida

### Por Funcionalidade

- **Autenticação**: [ARCHITECTURE.md](ARCHITECTURE.md#autenticação-e-autorização)
- **Fidelidade**: [MODULES.md](MODULES.md#4-módulo-de-fidelidade)
- **Campanhas**: [CAMPAIGNS.md](CAMPAIGNS.md) - Documentação completa
- **Pedidos Especiais**: [MODULES.md](MODULES.md#pedidos-especiais)
- **Faturas**: [MODULES.md](MODULES.md#3-módulo-de-vendas-faturas)
- **API**: [API.md](API.md)
- **Deploy**: [DEPLOYMENT.md](DEPLOYMENT.md)

### Por Problema

- **Erro 500**: [FAQ.md](FAQ.md#erro-500-sem-mensagem)
- **Permissões**: [FAQ.md](FAQ.md#erro-permission-denied-em-storage)
- **Filas não processam**: [FAQ.md](FAQ.md#filas-não-processam)
- **Emails não enviam**: [FAQ.md](FAQ.md#emails-não-são-enviados)
- **Performance lenta**: [FAQ.md](FAQ.md#o-site-está-lento-o-que-fazer)

### Por Tecnologia

- **Laravel**: [ARCHITECTURE.md](ARCHITECTURE.md)
- **Livewire**: [ARCHITECTURE.md](ARCHITECTURE.md#livewire-components)
- **Tailwind CSS**: [README.md](../README.md#personalização)
- **MySQL**: [DEPLOYMENT.md](DEPLOYMENT.md#configuração-do-banco-de-dados)
- **Redis**: [DEPLOYMENT.md](DEPLOYMENT.md#instalar-redis)
- **Nginx**: [DEPLOYMENT.md](DEPLOYMENT.md#configuração-do-nginx)

---

## 📊 Diagramas

### Arquitetura Geral

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

### Fluxo de Criação de Fatura

```
User → Controller → InvoiceService
                         ↓
                    [Transaction]
                         ↓
                    Create Invoice
                         ↓
                    Add Items
                         ↓
                    Update Stock (BookService)
                         ↓
                    Add Loyalty Points (LoyaltyService)
                         ↓
                    Send Notification (NotificationService)
                         ↓
                    [Commit]
                         ↓
                    Return Invoice
```

---

## 🛠️ Ferramentas e Comandos

### Artisan Commands

```bash
# Desenvolvimento
php artisan serve
php artisan queue:listen
php artisan tinker

# Migrations
php artisan migrate
php artisan migrate:fresh --seed
php artisan migrate:rollback

# Cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Filas
php artisan queue:work
php artisan queue:restart

# Customizados
php artisan loyalty:process-expiration
```

### Composer Scripts

```bash
# Desenvolvimento (inicia tudo)
composer run dev

# Testes
composer run test
```

### NPM Scripts

```bash
# Desenvolvimento
npm run dev

# Produção
npm run build

# Watch
npm run watch
```

---

## 📚 Recursos Externos

### Laravel
- [Documentação Oficial](https://laravel.com/docs)
- [Laracasts](https://laracasts.com)
- [Laravel News](https://laravel-news.com)

### Livewire
- [Documentação Oficial](https://livewire.laravel.com/docs)
- [Screencasts](https://livewire.laravel.com/screencasts)

### Tailwind CSS
- [Documentação Oficial](https://tailwindcss.com/docs)
- [Tailwind UI](https://tailwindui.com)

### PHP
- [PHP Manual](https://www.php.net/manual/pt_BR/)
- [PSR-12](https://www.php-fig.org/psr/psr-12/)

---

## 🤝 Contribuindo

Quer contribuir? Leia nosso [Guia de Contribuição](../CONTRIBUTING.md)!

### Processo Rápido

1. Fork o repositório
2. Crie uma branch (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'feat: adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

---

## 📞 Suporte

### Precisa de Ajuda?

1. **Consulte a documentação** - Provavelmente sua dúvida já está respondida
2. **Verifique o FAQ** - [FAQ.md](FAQ.md)
3. **Pesquise issues existentes** - Talvez alguém já teve o mesmo problema
4. **Abra uma issue** - Descreva seu problema detalhadamente
5. **Entre em contato** - Use os canais oficiais de suporte

### Reportar Bugs

Use o template de bug report ao abrir uma issue:

- Descrição clara do problema
- Passos para reproduzir
- Comportamento esperado vs atual
- Screenshots (se aplicável)
- Informações do ambiente

---

## 📝 Licença

Este projeto é licenciado sob a [MIT License](https://opensource.org/licenses/MIT).

---

## 🎉 Agradecimentos

Obrigado a todos os contribuidores que ajudaram a tornar este projeto melhor!

---

**Última atualização**: Novembro 2025

**Versão da Documentação**: 2.0.0
