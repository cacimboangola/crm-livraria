# Twin Development Plan
Generated: 2025-11-25 22:03
Task: Sistema de Pedidos Especiais - Quando um livro não está em stock, os funcionários registram o pedido através do sistema, preenchendo um formulário com detalhes do livro e os dados de contato do cliente. Os funcionários recebem notificações de pedidos especiais e fazem a encomenda ao fornecedor. Quando o livro chega, o cliente é notificado para a retirada ou entrega.
Quality Level: pragmatic

## Análise Técnica

### Estado Atual
- **Models existentes**: `Book`, `Customer`, `Invoice`, `InvoiceItem`, `Notification`, `User`
- **Sistema de notificações**: Já existe `Notification` model com suporte a tipos, links, leitura
- **Sistema de pedidos**: Baseado em `Invoice` e `InvoiceItem`, focado em livros em stock
- **Não existe**: Conceito de "pedido especial" para livros fora de stock

### O que precisa ser criado
1. **Model `SpecialOrder`** - Para rastrear pedidos de livros não disponíveis
2. **Migration** - Tabela `special_orders` com campos para livro, cliente, status, datas
3. **Controller `SpecialOrderController`** - CRUD e gestão de status
4. **Views** - Formulário de criação, listagem, detalhes
5. **Integração com Notificações** - Alertar funcionários e clientes

### Fluxo do Sistema
1. Funcionário cria pedido especial (livro + cliente)
2. Notificação enviada para funcionários (admin)
3. Funcionário atualiza status: "encomendado ao fornecedor"
4. Quando livro chega: status "disponível para retirada"
5. Cliente recebe notificação por email/sistema
6. Funcionário marca como "entregue" ou "retirado"

## Plano de Implementação

### Arquivos a Criar:
- `database/migrations/2025_11_25_220000_create_special_orders_table.php` - Migration
- `app/Models/SpecialOrder.php` - Model com relacionamentos e status
- `app/Http/Controllers/SpecialOrderController.php` - Controller CRUD
- `resources/views/special-orders/index.blade.php` - Listagem
- `resources/views/special-orders/create.blade.php` - Formulário de criação
- `resources/views/special-orders/show.blade.php` - Detalhes do pedido
- `resources/views/special-orders/edit.blade.php` - Edição
- `app/Mail/SpecialOrderNotification.php` - Email para cliente
- `resources/views/emails/special-order-ready.blade.php` - Template de email
- `database/seeders/SpecialOrderSeeder.php` - Dados de exemplo

### Arquivos a Modificar:
- `routes/web.php` - Adicionar rotas para pedidos especiais
- `resources/views/layouts/app.blade.php` - Adicionar link no menu

### Ordem de Implementação:
1. **Migration** - Criar tabela `special_orders` com campos essenciais
2. **Model** - `SpecialOrder` com relacionamentos (Customer, User) e status enum
3. **Controller** - CRUD completo com lógica de notificações
4. **Views** - Interface para funcionários gerenciarem pedidos
5. **Email** - Notificação para cliente quando livro disponível
6. **Rotas e Menu** - Integrar ao sistema existente
7. **Seeder** - Dados de exemplo para testes

### Estrutura da Tabela `special_orders`:
```
- id
- customer_id (FK customers)
- user_id (FK users - funcionário que criou)
- book_title (string - título do livro solicitado)
- book_author (string, nullable)
- book_isbn (string, nullable)
- book_publisher (string, nullable)
- quantity (integer, default 1)
- notes (text, nullable - observações)
- status (enum: pending, ordered, received, notified, delivered, cancelled)
- supplier_notes (text, nullable - notas sobre fornecedor)
- ordered_at (timestamp, nullable)
- received_at (timestamp, nullable)
- notified_at (timestamp, nullable)
- delivered_at (timestamp, nullable)
- created_at, updated_at
```

### Status do Pedido Especial:
- `pending` - Aguardando encomenda ao fornecedor
- `ordered` - Encomendado ao fornecedor
- `received` - Livro recebido na loja
- `notified` - Cliente notificado
- `delivered` - Entregue ao cliente
- `cancelled` - Cancelado

### Riscos Técnicos:
- **Envio de emails**: Verificar configuração SMTP no `.env`
- **Mitigação**: Usar try/catch e log de erros no envio

## Próximo Passo
Para implementar este plano, digite: ok, continue, ou approve
Para cancelar, digite: cancel ou inicie uma nova tarefa
