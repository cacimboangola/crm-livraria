<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura #{{ $invoice->id }} - Livraria CRM</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-subtitle {
            font-size: 16px;
            color: #666;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info-block {
            width: 48%;
        }
        .invoice-info-title {
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th, .invoice-table td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .invoice-table th {
            background-color: #f5f5f5;
            text-align: left;
        }
        .invoice-table tfoot td {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .invoice-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .invoice-notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }
        .status-draft {
            color: #6c757d;
            font-weight: bold;
        }
        .print-button {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        @media print {
            .print-button {
                display: none;
            }
            body {
                padding: 0;
                margin: 15mm;
            }
            @page {
                size: A4;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">Imprimir</button>
    
    <div class="invoice-header">
        <div class="invoice-title">LIVRARIA CRM</div>
        <div class="invoice-subtitle">Sistema de Gestão de Livraria</div>
        <div>Rua Exemplo, 123 - Luanda - Angola</div>
        <div>NIF: 000000000 - Telefone: (+244) 000 000 000</div>
    </div>
    
    <div class="invoice-title">FATURA #{{ $invoice->id }}</div>
    <div class="invoice-subtitle">
        Status: 
        <span class="status-{{ $invoice->status }}">
            {{ 
                $invoice->status == 'paid' ? 'PAGO' : 
                ($invoice->status == 'pending' ? 'PENDENTE' : 
                ($invoice->status == 'draft' ? 'RASCUNHO' : 'CANCELADO')) 
            }}
        </span>
    </div>
    
    <div class="invoice-info">
        <div class="invoice-info-block">
            <div class="invoice-info-title">Informações do Cliente</div>
            <div><strong>Nome:</strong> {{ $invoice->customer->name }}</div>
            <div><strong>Email:</strong> {{ $invoice->customer->email }}</div>
            <div><strong>Telefone:</strong> {{ $invoice->customer->phone }}</div>
            <div><strong>Documento:</strong> {{ $invoice->customer->document }}</div>
            <div><strong>Endereço:</strong> {{ $invoice->customer->address }}</div>
        </div>
        
        <div class="invoice-info-block">
            <div class="invoice-info-title">Informações da Fatura</div>
            <div><strong>Número:</strong> {{ $invoice->id }}</div>
            <div><strong>Data:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</div>
            <div><strong>Vencimento:</strong> {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}</div>
            <div><strong>Pagamento:</strong> {{ $invoice->payment_date ? $invoice->payment_date->format('d/m/Y') : '-' }}</div>
            <div><strong>Método de Pagamento:</strong> {{ $invoice->payment_method ?? '-' }}</div>
        </div>
    </div>
    
    <table class="invoice-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">Livro</th>
                <th width="15%">Autor</th>
                <th width="10%" class="text-center">Qtd</th>
                <th width="10%" class="text-right">Preço Unit.</th>
                <th width="15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->book->title }}</td>
                    <td>{{ $item->book->author }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Kz {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="text-right">Kz {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">Subtotal:</td>
                <td class="text-right">Kz {{ number_format($invoice->subtotal, 2, ',', '.') }}</td>
            </tr>
            @if($invoice->discount > 0)
                <tr>
                    <td colspan="5" class="text-right">Desconto:</td>
                    <td class="text-right">Kz {{ number_format($invoice->discount, 2, ',', '.') }}</td>
                </tr>
            @endif
            @if($invoice->tax > 0)
                <tr>
                    <td colspan="5" class="text-right">Impostos:</td>
                    <td class="text-right">Kz {{ number_format($invoice->tax, 2, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="5" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>Kz {{ number_format($invoice->total, 2, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
    
    @if($invoice->notes)
        <div class="invoice-notes">
            <strong>Observações:</strong><br>
            {{ $invoice->notes }}
        </div>
    @endif
    
    <div class="invoice-footer">
        <p>Esta fatura foi gerada automaticamente pelo sistema Livraria CRM em {{ now()->format('d/m/Y H:i:s') }}.</p>
        <p>Em caso de dúvidas, entre em contato pelo telefone (00) 0000-0000 ou pelo email contato@livraria-crm.com</p>
    </div>
</body>
</html>
