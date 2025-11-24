# ❓ FAQ - Perguntas Frequentes

## Índice

- [Instalação e Configuração](#instalação-e-configuração)
- [Funcionalidades](#funcionalidades)
- [Problemas Comuns](#problemas-comuns)
- [Performance](#performance)
- [Segurança](#segurança)
- [Deploy](#deploy)

---

## Instalação e Configuração

### Como instalar o projeto?

Siga o guia completo no [README.md](../README.md). Resumidamente:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run dev
composer run dev
```

### Quais são os requisitos mínimos?

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- MySQL >= 8.0
- Extensões PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo

### Como configurar o banco de dados?

Edite o arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_livraria
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

Depois execute:

```bash
php artisan migrate:fresh --seed
```

### Como configurar email?

Para desenvolvimento, use `MAIL_MAILER=log` (emails salvos em `storage/logs/laravel.log`).

Para produção, configure SMTP no `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_username
MAIL_PASSWORD=sua_senha
MAIL_FROM_ADDRESS="noreply@livraria.com"
```

### Como acessar o sistema após instalação?

**Admin:**
- Email: admin@livraria.com
- Senha: password

**Cliente:**
- Email: cliente@example.com
- Senha: password

---

## Funcionalidades

### Como funciona o sistema de fidelidade?

1. **Ganho de Pontos**: Clientes ganham 1 ponto para cada 1€ gasto em compras
2. **Resgate**: 100 pontos = 10€ de desconto
3. **Expiração**: Pontos expiram após 365 dias
4. **Bônus**: Administradores podem dar pontos extras via campanhas

### Como criar uma campanha de marketing?

1. Acesse **Campanhas** no menu
2. Clique em **Nova Campanha**
3. Preencha nome, descrição e conteúdo
4. Selecione clientes (manual ou automático)
5. Ative a campanha
6. Envie os emails
7. Acompanhe as métricas

### Como rastrear métricas de campanhas?

As métricas são rastreadas automaticamente:

- **Abertura**: Pixel transparente no email
- **Clique**: Links com parâmetros de rastreamento
- **Conversão**: Marcada quando cliente faz compra

Acesse **Campanhas > Ver Métricas** para visualizar.

### Como funciona o sistema de recomendações?

O sistema analisa:

- Histórico de compras do cliente
- Categorias preferidas
- Livros populares
- Livros similares (mesma categoria/autor)

Recomendações aparecem no dashboard do cliente e podem ser usadas em campanhas.

### Como emitir uma fatura?

1. Acesse **Faturas > Nova Fatura**
2. Selecione o cliente
3. Adicione livros ao carrinho
4. Aplique descontos (opcional)
5. Resgate pontos de fidelidade (opcional)
6. Selecione método de pagamento
7. Confirme a criação

A fatura pode ser enviada por email ou baixada em PDF.

### Como o estoque é atualizado?

O estoque é atualizado **automaticamente** quando:

- Uma fatura é criada (decrementa)
- Uma fatura é cancelada (incrementa)

Você também pode atualizar manualmente em **Livros > Editar > Atualizar Estoque**.

### Como funciona o Portal do Cliente?

Clientes podem:

- Navegar pelo catálogo de livros
- Adicionar livros ao carrinho
- Finalizar compra
- Ver histórico de pedidos
- Consultar pontos de fidelidade
- Baixar faturas em PDF
- Editar perfil

Acesso: `http://localhost:8000/cliente/dashboard`

---

## Problemas Comuns

### Erro: "Class not found"

**Solução:**

```bash
composer dump-autoload
php artisan clear-compiled
php artisan optimize
```

### Erro: "Permission denied" em storage/

**Solução:**

```bash
# Windows (PowerShell como Admin)
icacls storage /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls bootstrap\cache /grant "IIS_IUSRS:(OI)(CI)F" /T

# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Erro: "SQLSTATE[HY000] [2002] Connection refused"

**Causa**: MySQL não está rodando ou configuração incorreta.

**Solução:**

```bash
# Verificar se MySQL está rodando
# Windows
net start mysql

# Linux
sudo systemctl start mysql

# Verificar configurações no .env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_livraria
```

### Erro: "419 Page Expired" ao submeter formulários

**Causa**: Token CSRF expirado.

**Solução:**

1. Verifique se o formulário tem `@csrf`
2. Limpe o cache: `php artisan cache:clear`
3. Aumente o tempo de sessão no `.env`:

```env
SESSION_LIFETIME=120
```

### Assets (CSS/JS) não carregam

**Solução:**

```bash
# Recompilar assets
npm run build

# Criar link simbólico para storage
php artisan storage:link

# Limpar cache de views
php artisan view:clear
```

### Filas não processam

**Solução:**

```bash
# Verificar se o worker está rodando
# Se não estiver, inicie:
php artisan queue:listen --tries=3

# Ou use o comando dev que já inicia tudo:
composer run dev

# Reiniciar workers
php artisan queue:restart
```

### Emails não são enviados

**Solução:**

1. Verifique configuração no `.env`
2. Para desenvolvimento, use `MAIL_MAILER=log`
3. Verifique logs: `storage/logs/laravel.log`
4. Teste conexão SMTP:

```bash
php artisan tinker
Mail::raw('Teste', function($msg) {
    $msg->to('teste@example.com')->subject('Teste');
});
```

### Erro 500 sem mensagem

**Solução:**

```bash
# Ativar debug no .env
APP_DEBUG=true

# Verificar logs
tail -f storage/logs/laravel.log

# Limpar todos os caches
php artisan optimize:clear
```

### Pontos de fidelidade não são adicionados

**Causa**: Fatura não está com status "paid".

**Solução:**

Pontos só são adicionados quando a fatura é marcada como **Paga**. Verifique:

1. Status da fatura está "paid"
2. Logs em `storage/logs/laravel.log`
3. Tabela `loyalty_transactions` no banco

### Campanhas não rastreiam métricas

**Causa**: Links de rastreamento não estão corretos.

**Solução:**

Certifique-se de usar os helpers corretos no conteúdo da campanha:

```php
// Abertura (pixel transparente)
<img src="{{ route('campaigns.track-open', [$campaign->id, $customer->id, $token]) }}" width="1" height="1">

// Clique
<a href="{{ route('campaigns.track-click', [$campaign->id, $customer->id, $token]) }}">Clique aqui</a>
```

---

## Performance

### O site está lento, o que fazer?

**Otimizações:**

```bash
# 1. Cache de configuração
php artisan config:cache

# 2. Cache de rotas
php artisan route:cache

# 3. Cache de views
php artisan view:cache

# 4. Otimizar autoload
composer install --optimize-autoloader --no-dev

# 5. Usar Redis para cache e filas
# No .env:
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### Como otimizar queries do banco?

**Boas práticas:**

```php
// ❌ Ruim (N+1 query)
$invoices = Invoice::all();
foreach ($invoices as $invoice) {
    echo $invoice->customer->name;
}

// ✅ Bom (Eager loading)
$invoices = Invoice::with('customer')->get();
foreach ($invoices as $invoice) {
    echo $invoice->customer->name;
}

// ✅ Paginação
$customers = Customer::paginate(20);

// ✅ Select específico
$customers = Customer::select('id', 'name', 'email')->get();
```

### Como reduzir uso de memória?

```php
// Use chunk para grandes datasets
Customer::chunk(100, function ($customers) {
    foreach ($customers as $customer) {
        // Processar
    }
});

// Use cursor para iteração eficiente
foreach (Customer::cursor() as $customer) {
    // Processar
}
```

---

## Segurança

### Como proteger contra SQL Injection?

**Sempre use Query Builder ou Eloquent:**

```php
// ✅ Seguro
Customer::where('email', $email)->first();

// ❌ Inseguro
DB::select("SELECT * FROM customers WHERE email = '$email'");
```

### Como proteger contra XSS?

**Sempre escape output no Blade:**

```blade
{{-- ✅ Seguro (escapado automaticamente) --}}
<p>{{ $customer->name }}</p>

{{-- ❌ Inseguro (não escapado) --}}
<p>{!! $customer->name !!}</p>
```

### Como proteger rotas administrativas?

Use o middleware `AdminMiddleware`:

```php
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::resource('users', UserController::class);
});
```

### Como proteger dados sensíveis?

```env
# Nunca commite o .env
# Use .env.example como template

# Dados sensíveis devem estar no .env
DB_PASSWORD=senha_segura
MAIL_PASSWORD=senha_segura
```

---

## Deploy

### Como fazer deploy em produção?

Siga o guia completo em [docs/DEPLOYMENT.md](DEPLOYMENT.md).

**Resumo:**

```bash
# 1. Configurar servidor (PHP, MySQL, Nginx, Redis)
# 2. Clonar repositório
# 3. Instalar dependências
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 4. Configurar .env
APP_ENV=production
APP_DEBUG=false

# 5. Executar migrations
php artisan migrate --force

# 6. Otimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Configurar Supervisor para filas
# 8. Configurar SSL (Let's Encrypt)
```

### Como fazer backup?

**Banco de dados:**

```bash
mysqldump -u usuario -p crm_livraria > backup.sql
```

**Arquivos:**

```bash
tar -czf backup-files.tar.gz storage public/uploads
```

**Automatizar com cron:**

```cron
0 3 * * * /usr/local/bin/backup-crm.sh
```

### Como atualizar a aplicação?

```bash
# 1. Modo de manutenção
php artisan down

# 2. Atualizar código
git pull origin main

# 3. Atualizar dependências
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 4. Executar migrations
php artisan migrate --force

# 5. Limpar e recriar caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Reiniciar workers
sudo supervisorctl restart crm-livraria-worker:*

# 7. Sair do modo de manutenção
php artisan up
```

---

## Dúvidas Adicionais

### Onde encontrar mais ajuda?

- **Documentação**: Consulte os arquivos em `/docs`
- **Issues**: Abra uma issue no GitHub
- **Laravel Docs**: https://laravel.com/docs
- **Livewire Docs**: https://livewire.laravel.com/docs

### Como reportar um bug?

Abra uma issue no GitHub com:

1. Descrição clara do problema
2. Passos para reproduzir
3. Comportamento esperado vs atual
4. Screenshots (se aplicável)
5. Informações do ambiente (PHP, Laravel, OS)

### Como sugerir uma funcionalidade?

Abra uma issue com a tag `enhancement` incluindo:

1. Descrição da funcionalidade
2. Problema que resolve
3. Solução proposta
4. Mockups (se aplicável)

---

**Última atualização**: Janeiro 2025
