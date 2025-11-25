<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .invoice-info {
            margin-bottom: 20px;
        }
        .invoice-details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
            color: #777;
            border-top: 1px solid #f0f0f0;
            padding-top: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #3490dc;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-draft {
            background-color: #e2e3e5;
            color: #383d41;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">LIVRARIA CRM</div>
        <p>Sistema de Gestão de Livraria</p>
    </div>
    
    <div class="invoice-info">
        <p>Prezado(a) <strong>{{ $customer->name }}</strong>,</p>
        <p>Segue em anexo a fatura <strong>#{{ $invoice->id }}</strong> referente à sua compra em nossa livraria.</p>
    </div>
    
    <div class="invoice-details">
        <h3>Detalhes da Fatura</h3>
        <p><strong>Número:</strong> #{{ $invoice->id }}</p>
        <p><strong>Data:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</p>
        <p><strong>Vencimento:</strong> {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</p>
        <p>
            <strong>Status:</strong> 
            <span class="status status-{{ $invoice->status }}">
                {{ 
                    $invoice->status == 'paid' ? 'PAGO' : 
                    ($invoice->status == 'pending' ? 'PENDENTE' : 
                    ($invoice->status == 'draft' ? 'RASCUNHO' : 'CANCELADO')) 
                }}
            </span>
        </p>
        <p><strong>Valor Total:</strong> Kz {{ number_format($invoice->total, 2, ',', '.') }}</p>
    </div>
    
    <h3>Itens da Fatura</h3>
    <table>
        <thead>
            <tr>
                <th>Livro</th>
                <th>Qtd</th>
                <th>Preço</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->book->title }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Kz {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td>Kz {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <p>Para mais detalhes, consulte o PDF anexo a este email.</p>
    
    <p>Caso tenha alguma dúvida, entre em contato conosco respondendo a este email ou pelo telefone (00) 0000-0000.</p>
    
    <p>Agradecemos pela preferência!</p>
    
    <div class="footer">
        <p>Este é um email automático. Por favor, não responda diretamente.</p>
        <p>&copy; {{ date('Y') }} Livraria CRM - Todos os direitos reservados</p>
    </div>
</body>
</html>
