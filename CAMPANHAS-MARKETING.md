# 📢 Sistema de Campanhas de Marketing

## 📋 Visão Geral

O sistema de **Campanhas de Marketing** do CRM Livraria permite criar, gerenciar e monitorar campanhas direcionadas para engajar clientes, aumentar vendas e fortalecer o relacionamento com o público.

---

## 🎯 Funcionalidades Principais

### 1. **Tipos de Campanhas**

O sistema suporta diversos tipos de campanhas:

- **📧 Email** - Envio de emails marketing para clientes
- **📱 SMS** - Mensagens de texto (preparado para integração futura)
- **💰 Desconto** - Campanhas promocionais com descontos especiais
- **🎉 Evento** - Divulgação de eventos e lançamentos

### 2. **Status das Campanhas**

- **📝 Rascunho (draft)** - Campanha em criação, ainda não ativa
- **✅ Ativa (active)** - Campanha em execução
- **🏁 Concluída (completed)** - Campanha finalizada com sucesso
- **❌ Cancelada (cancelled)** - Campanha interrompida

---

## 🚀 Como Criar uma Campanha

### Passo 1: Criar Nova Campanha

1. Acesse **Campanhas de Marketing** no menu admin
2. Clique em **"Nova Campanha"**
3. Preencha os dados:
   - **Nome**: Título identificador da campanha
   - **Tipo**: Escolha entre Email, SMS, Desconto ou Evento
   - **Descrição**: Resumo dos objetivos da campanha
   - **Conteúdo**: Corpo da mensagem (HTML suportado para emails)
   - **Data de Início**: Quando a campanha começa
   - **Data de Término**: Quando a campanha termina (opcional)
   - **Critérios de Segmentação**: Filtros para selecionar público-alvo

### Passo 2: Selecionar Clientes

Após criar a campanha, você pode adicionar clientes de duas formas:

#### **Seleção Manual**
- Clique em **"Adicionar Clientes"**
- Escolha clientes individualmente da lista
- Confirme a seleção

#### **Seleção Automática** 🪄
- Clique em **"Seleção Automática"**
- O sistema seleciona clientes com base nos **critérios de segmentação** definidos
- Exemplos de critérios:
  - Clientes que compraram nos últimos 30 dias
  - Clientes de um nível de fidelidade específico
  - Clientes que não compraram há mais de 60 dias
  - Clientes de uma categoria específica

### Passo 3: Ativar e Enviar

1. **Ativar Campanha**: Muda o status de "Rascunho" para "Ativa"
2. **Enviar Emails**: Para campanhas de email, clique em "Enviar Emails" para disparar as mensagens
3. **Acompanhar Métricas**: Monitore o desempenho em tempo real

---

## 📊 Métricas e Rastreamento

O sistema rastreia automaticamente:

### **Métricas Principais**
- **👥 Clientes Alvo**: Total de clientes na campanha
- **📤 Emails Enviados**: Quantidade e taxa de envio
- **👀 Emails Abertos**: Taxa de abertura (open rate)
- **🖱️ Cliques**: Interações com links na campanha
- **💰 Conversões**: Clientes que realizaram compra após a campanha

### **Rastreamento Individual**
Para cada cliente, você pode ver:
- ✅ Email enviado (data/hora)
- 👁️ Email aberto (data/hora)
- 🔗 Links clicados (data/hora)
- 🛒 Conversão realizada (data/hora)

---

## 🎁 Integração com Programa de Fidelidade

Uma funcionalidade poderosa é a **distribuição de pontos de fidelidade** através de campanhas:

### Como Distribuir Pontos

1. Acesse a campanha ativa
2. Na seção **"Programa de Fidelidade"**, defina:
   - **Quantidade de pontos** a distribuir por cliente
   - **Descrição** da bonificação
3. Clique em **"Distribuir Pontos de Fidelidade"**
4. Todos os clientes da campanha receberão os pontos automaticamente

### Casos de Uso
- **Recompensa por Engajamento**: Dar pontos para quem abriu o email
- **Incentivo de Compra**: Pontos extras em campanhas promocionais
- **Fidelização**: Bonificar clientes inativos para reengajamento
- **Eventos**: Pontos para participantes de eventos

---

## 🔧 Funcionalidades Técnicas

### **Rastreamento Automático**
O sistema utiliza URLs especiais com tokens únicos para rastrear:
- Abertura de emails (pixel invisível)
- Cliques em links
- Conversões (compras realizadas)

### **Rotas de Rastreamento**
```
/track/open/{campaignId}/{customerId}/{token}
/track/click/{campaignId}/{customerId}/{token}
/track/conversion/{campaignId}/{customerId}/{token}
```

### **Segurança**
- Tokens únicos por cliente/campanha
- Validação de autenticidade
- Proteção contra rastreamento não autorizado

---

## 📈 Melhores Práticas

### **Criação de Campanhas**
1. ✅ Defina objetivos claros (venda, engajamento, retenção)
2. ✅ Segmente adequadamente o público-alvo
3. ✅ Crie conteúdo relevante e personalizado
4. ✅ Teste o conteúdo antes de enviar
5. ✅ Escolha horários estratégicos para envio

### **Segmentação Eficaz**
- **Novos Clientes**: Campanhas de boas-vindas
- **Clientes Ativos**: Ofertas exclusivas e lançamentos
- **Clientes Inativos**: Campanhas de reativação com incentivos
- **VIPs (Platinum)**: Eventos exclusivos e benefícios premium

### **Otimização de Resultados**
1. 📊 Monitore métricas regularmente
2. 🧪 Teste diferentes abordagens (A/B testing)
3. 🎯 Ajuste segmentação com base em resultados
4. 💬 Colete feedback dos clientes
5. 🔄 Itere e melhore continuamente

---

## 🎯 Exemplos de Campanhas

### **Campanha de Lançamento**
- **Tipo**: Email
- **Público**: Todos os clientes ativos
- **Conteúdo**: Novo livro best-seller com desconto de lançamento
- **Ação**: Distribuir 50 pontos de fidelidade para quem comprar

### **Campanha de Reativação**
- **Tipo**: Email + Desconto
- **Público**: Clientes inativos há mais de 60 dias
- **Conteúdo**: "Sentimos sua falta! Volte com 20% de desconto"
- **Ação**: Distribuir 100 pontos de bônus ao retornar

### **Campanha de Fidelidade**
- **Tipo**: Email
- **Público**: Clientes nível Gold e Platinum
- **Conteúdo**: Acesso antecipado a lançamentos + evento exclusivo
- **Ação**: Distribuir 200 pontos de fidelidade

### **Campanha Sazonal**
- **Tipo**: Email + Evento
- **Público**: Todos os clientes
- **Conteúdo**: "Feira do Livro - Descontos de até 40%"
- **Ação**: Pontos em dobro durante o evento

---

## 🛠️ Arquivos do Sistema

### **Controllers**
- `app/Http/Controllers/CampaignController.php` - Gestão de campanhas
- `app/Http/Controllers/CampaignTrackingController.php` - Rastreamento

### **Services**
- `app/Services/CampaignService.php` - Lógica de negócio

### **Models**
- `app/Models/Campaign.php` - Modelo de campanha
- Relacionamento many-to-many com `Customer`

### **Views**
- `resources/views/campaigns/index.blade.php` - Listagem
- `resources/views/campaigns/create.blade.php` - Criação
- `resources/views/campaigns/show.blade.php` - Detalhes e métricas
- `resources/views/campaigns/edit.blade.php` - Edição
- `resources/views/campaigns/metrics.blade.php` - Métricas detalhadas
- `resources/views/campaigns/select-customers.blade.php` - Seleção de clientes

### **Rotas**
```php
Route::resource('campaigns', CampaignController::class);
Route::post('/campaigns/{campaign}/activate', [CampaignController::class, 'activate']);
Route::post('/campaigns/{campaign}/send-emails', [CampaignController::class, 'sendEmails']);
Route::post('/campaigns/{campaign}/distribute-points', [CampaignController::class, 'distributePoints']);
```

---

## 💡 Dicas Avançadas

### **Personalização de Emails**
Use variáveis dinâmicas no conteúdo:
- `{nome}` - Nome do cliente
- `{nivel}` - Nível de fidelidade
- `{pontos}` - Pontos acumulados

### **Automação Futura**
O sistema está preparado para:
- Campanhas automáticas baseadas em gatilhos
- Sequências de emails (drip campaigns)
- Integração com WhatsApp Business
- Notificações push

---

## 📞 Suporte

Para dúvidas ou sugestões sobre o sistema de campanhas:
- 📧 Email: suporte@livraria-crm.com
- 📱 Telefone: (+244) 923-456-789

---

**Desenvolvido com ❤️ para Livraria CRM Angola**
