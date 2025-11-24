# 🚀 Guia de Deploy

## Visão Geral

Este guia detalha o processo completo de deploy do CRM Livraria em ambiente de produção.

## Pré-requisitos de Produção

### Servidor
- **OS**: Ubuntu 20.04 LTS ou superior / CentOS 8+
- **RAM**: Mínimo 2GB (recomendado 4GB+)
- **Disco**: Mínimo 20GB
- **CPU**: 2 cores (recomendado 4+)

### Software
- **PHP**: 8.2 ou superior
- **Composer**: 2.x
- **Node.js**: 18.x ou superior
- **NPM**: 9.x ou superior
- **MySQL**: 8.0 ou superior
- **Nginx** ou **Apache**: Última versão estável
- **Redis**: 6.x ou superior (recomendado para cache e filas)
- **Supervisor**: Para gerenciar workers de fila

### Extensões PHP Necessárias
```bash
php8.2-cli
php8.2-fpm
php8.2-mysql
php8.2-mbstring
php8.2-xml
php8.2-curl
php8.2-zip
php8.2-gd
php8.2-bcmath
php8.2-redis
```

---

## Processo de Deploy

### 1. Preparação do Servidor

#### 1.1 Atualizar Sistema

```bash
sudo apt update && sudo apt upgrade -y
```

#### 1.2 Instalar PHP 8.2

```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-redis -y
```

#### 1.3 Instalar Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

#### 1.4 Instalar Node.js e NPM

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

#### 1.5 Instalar MySQL

```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

#### 1.6 Instalar Redis

```bash
sudo apt install redis-server -y
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

#### 1.7 Instalar Nginx

```bash
sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx
```

#### 1.8 Instalar Supervisor

```bash
sudo apt install supervisor -y
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

---

### 2. Configuração do Banco de Dados

```bash
# Acessar MySQL
sudo mysql -u root -p

# Criar banco de dados
CREATE DATABASE crm_livraria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Criar usuário
CREATE USER 'crm_user'@'localhost' IDENTIFIED BY 'senha_segura_aqui';

# Conceder privilégios
GRANT ALL PRIVILEGES ON crm_livraria.* TO 'crm_user'@'localhost';

# Aplicar mudanças
FLUSH PRIVILEGES;

# Sair
EXIT;
```

---

### 3. Deploy da Aplicação

#### 3.1 Clonar Repositório

```bash
cd /var/www
sudo git clone <url-do-repositorio> crm-livraria
cd crm-livraria
```

#### 3.2 Configurar Permissões

```bash
sudo chown -R www-data:www-data /var/www/crm-livraria
sudo chmod -R 755 /var/www/crm-livraria
sudo chmod -R 775 /var/www/crm-livraria/storage
sudo chmod -R 775 /var/www/crm-livraria/bootstrap/cache
```

#### 3.3 Instalar Dependências

```bash
# Dependências PHP
composer install --optimize-autoloader --no-dev

# Dependências JavaScript
npm install
```

#### 3.4 Configurar Ambiente

```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Editar configurações
nano .env
```

**Configurações importantes no .env:**

```env
APP_NAME="CRM Livraria"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_livraria
DB_USERNAME=crm_user
DB_PASSWORD=senha_segura_aqui

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.seu-provedor.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@dominio.com
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@seu-dominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### 3.5 Gerar Chave da Aplicação

```bash
php artisan key:generate
```

#### 3.6 Executar Migrations

```bash
php artisan migrate --force
```

#### 3.7 Executar Seeders (Opcional - apenas primeira vez)

```bash
php artisan db:seed --force
```

#### 3.8 Compilar Assets

```bash
npm run build
```

#### 3.9 Otimizar Aplicação

```bash
# Cache de configuração
php artisan config:cache

# Cache de rotas
php artisan route:cache

# Cache de views
php artisan view:cache

# Cache de eventos
php artisan event:cache
```

#### 3.10 Criar Link Simbólico para Storage

```bash
php artisan storage:link
```

---

### 4. Configuração do Nginx

#### 4.1 Criar Arquivo de Configuração

```bash
sudo nano /etc/nginx/sites-available/crm-livraria
```

**Conteúdo:**

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name seu-dominio.com www.seu-dominio.com;
    root /var/www/crm-livraria/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 4.2 Ativar Site

```bash
sudo ln -s /etc/nginx/sites-available/crm-livraria /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### 5. Configuração de SSL (HTTPS)

#### 5.1 Instalar Certbot

```bash
sudo apt install certbot python3-certbot-nginx -y
```

#### 5.2 Obter Certificado

```bash
sudo certbot --nginx -d seu-dominio.com -d www.seu-dominio.com
```

#### 5.3 Renovação Automática

```bash
sudo certbot renew --dry-run
```

O Certbot configurará automaticamente a renovação via cron.

---

### 6. Configuração de Filas (Supervisor)

#### 6.1 Criar Arquivo de Configuração

```bash
sudo nano /etc/supervisor/conf.d/crm-livraria-worker.conf
```

**Conteúdo:**

```ini
[program:crm-livraria-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/crm-livraria/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/crm-livraria/storage/logs/worker.log
stopwaitsecs=3600
```

#### 6.2 Atualizar Supervisor

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start crm-livraria-worker:*
```

#### 6.3 Verificar Status

```bash
sudo supervisorctl status
```

---

### 7. Configuração de Cron Jobs

#### 7.1 Editar Crontab

```bash
sudo crontab -e -u www-data
```

#### 7.2 Adicionar Jobs

```cron
# Laravel Scheduler
* * * * * cd /var/www/crm-livraria && php artisan schedule:run >> /dev/null 2>&1

# Expiração de pontos de fidelidade (diariamente às 2h)
0 2 * * * cd /var/www/crm-livraria && php artisan loyalty:process-expiration >> /dev/null 2>&1
```

---

### 8. Configuração de Backup

#### 8.1 Script de Backup

```bash
sudo nano /usr/local/bin/backup-crm.sh
```

**Conteúdo:**

```bash
#!/bin/bash

# Configurações
APP_DIR="/var/www/crm-livraria"
BACKUP_DIR="/backups/crm-livraria"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="crm_livraria"
DB_USER="crm_user"
DB_PASS="senha_segura_aqui"

# Criar diretório de backup
mkdir -p $BACKUP_DIR

# Backup do banco de dados
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup dos arquivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $APP_DIR storage public/uploads

# Remover backups antigos (manter últimos 7 dias)
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup concluído: $DATE"
```

#### 8.2 Tornar Executável

```bash
sudo chmod +x /usr/local/bin/backup-crm.sh
```

#### 8.3 Agendar Backup Diário

```bash
sudo crontab -e
```

Adicionar:

```cron
# Backup diário às 3h
0 3 * * * /usr/local/bin/backup-crm.sh >> /var/log/crm-backup.log 2>&1
```

---

### 9. Monitoramento

#### 9.1 Logs da Aplicação

```bash
# Logs do Laravel
tail -f /var/www/crm-livraria/storage/logs/laravel.log

# Logs do Nginx
tail -f /var/log/nginx/error.log

# Logs do PHP-FPM
tail -f /var/log/php8.2-fpm.log

# Logs do Worker
tail -f /var/www/crm-livraria/storage/logs/worker.log
```

#### 9.2 Monitoramento de Recursos

```bash
# CPU e Memória
htop

# Espaço em disco
df -h

# Status dos serviços
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
sudo systemctl status redis-server
sudo systemctl status supervisor
```

---

### 10. Atualizações

#### 10.1 Script de Deploy

```bash
sudo nano /usr/local/bin/deploy-crm.sh
```

**Conteúdo:**

```bash
#!/bin/bash

APP_DIR="/var/www/crm-livraria"

echo "Iniciando deploy..."

# Entrar no diretório
cd $APP_DIR

# Ativar modo de manutenção
php artisan down

# Atualizar código
git pull origin main

# Instalar dependências
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Executar migrations
php artisan migrate --force

# Limpar e recriar caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar workers
sudo supervisorctl restart crm-livraria-worker:*

# Desativar modo de manutenção
php artisan up

echo "Deploy concluído!"
```

#### 10.2 Tornar Executável

```bash
sudo chmod +x /usr/local/bin/deploy-crm.sh
```

#### 10.3 Executar Deploy

```bash
sudo /usr/local/bin/deploy-crm.sh
```

---

### 11. Segurança

#### 11.1 Firewall (UFW)

```bash
# Instalar UFW
sudo apt install ufw -y

# Configurar regras
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'

# Ativar firewall
sudo ufw enable

# Verificar status
sudo ufw status
```

#### 11.2 Fail2Ban

```bash
# Instalar
sudo apt install fail2ban -y

# Configurar
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo nano /etc/fail2ban/jail.local

# Reiniciar
sudo systemctl restart fail2ban
```

#### 11.3 Permissões de Arquivos

```bash
# Garantir permissões corretas
sudo chown -R www-data:www-data /var/www/crm-livraria
sudo find /var/www/crm-livraria -type f -exec chmod 644 {} \;
sudo find /var/www/crm-livraria -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/crm-livraria/storage
sudo chmod -R 775 /var/www/crm-livraria/bootstrap/cache
```

---

### 12. Otimizações de Performance

#### 12.1 PHP-FPM

```bash
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

Ajustar:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

#### 12.2 MySQL

```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Adicionar:

```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 200
query_cache_size = 64M
```

#### 12.3 Redis

```bash
sudo nano /etc/redis/redis.conf
```

Ajustar:

```conf
maxmemory 512mb
maxmemory-policy allkeys-lru
```

---

### 13. Troubleshooting

#### Erro 500

```bash
# Verificar logs
tail -f /var/www/crm-livraria/storage/logs/laravel.log

# Verificar permissões
sudo chmod -R 775 storage bootstrap/cache

# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### Filas não processam

```bash
# Verificar status do worker
sudo supervisorctl status

# Reiniciar workers
sudo supervisorctl restart crm-livraria-worker:*

# Verificar logs
tail -f /var/www/crm-livraria/storage/logs/worker.log
```

#### Site lento

```bash
# Verificar uso de recursos
htop

# Otimizar banco de dados
php artisan optimize

# Verificar queries lentas
sudo tail -f /var/log/mysql/slow-query.log
```

---

### 14. Checklist de Deploy

- [ ] Servidor configurado com requisitos mínimos
- [ ] PHP 8.2 e extensões instaladas
- [ ] Composer e Node.js instalados
- [ ] MySQL configurado e banco criado
- [ ] Redis instalado e rodando
- [ ] Código clonado e dependências instaladas
- [ ] Arquivo .env configurado corretamente
- [ ] Migrations executadas
- [ ] Assets compilados
- [ ] Caches criados
- [ ] Nginx configurado
- [ ] SSL configurado (HTTPS)
- [ ] Supervisor configurado para filas
- [ ] Cron jobs configurados
- [ ] Backups automatizados
- [ ] Firewall configurado
- [ ] Monitoramento ativo
- [ ] Testes de funcionalidade realizados

---

## Suporte

Para problemas durante o deploy:
- Consulte os logs da aplicação
- Verifique a documentação do Laravel
- Abra uma issue no repositório

---

**Última atualização**: Janeiro 2025
