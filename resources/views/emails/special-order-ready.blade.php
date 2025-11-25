<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Livro Chegou!</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #1e3a5f;
            margin: 0;
            font-size: 24px;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 20px 0;
        }
        .book-info {
            background-color: #f8f9fa;
            border-left: 4px solid #1e3a5f;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .book-info h3 {
            margin: 0 0 10px 0;
            color: #1e3a5f;
        }
        .book-info p {
            margin: 5px 0;
            color: #666;
        }
        .delivery-info {
            background-color: #e8f5e9;
            border: 1px solid #4caf50;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .delivery-info h4 {
            color: #2e7d32;
            margin: 0 0 10px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #1e3a5f;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">📚</div>
            <h1>Seu Livro Chegou!</h1>
        </div>
        
        <div class="content">
            <p>Olá <strong>{{ $specialOrder->customer->name }}</strong>,</p>
            
            <p>Temos uma ótima notícia! O livro que você encomendou já está disponível.</p>
            
            <div class="book-info">
                <h3>{{ $specialOrder->book_title }}</h3>
                @if($specialOrder->book_author)
                    <p><strong>Autor:</strong> {{ $specialOrder->book_author }}</p>
                @endif
                @if($specialOrder->book_publisher)
                    <p><strong>Editora:</strong> {{ $specialOrder->book_publisher }}</p>
                @endif
                <p><strong>Quantidade:</strong> {{ $specialOrder->quantity }} {{ $specialOrder->quantity > 1 ? 'exemplares' : 'exemplar' }}</p>
                @if($specialOrder->estimated_price)
                    <p><strong>Preço estimado:</strong> Kz {{ number_format($specialOrder->estimated_price, 2, ',', '.') }}</p>
                @endif
            </div>
            
            <div class="delivery-info">
                @if($specialOrder->delivery_preference === 'pickup')
                    <h4>🏪 Retirada na Loja</h4>
                    <p>Seu livro está aguardando retirada em nossa loja.</p>
                    <p><strong>Endereço:</strong> Rua dos Livros, 123 - Luanda, Angola</p>
                    <p><strong>Horário:</strong> Segunda a Sexta, 8h às 18h</p>
                @else
                    <h4>🚚 Entrega em Domicílio</h4>
                    <p>Entraremos em contato para agendar a entrega do seu livro.</p>
                @endif
            </div>
            
            @if($specialOrder->customer_notes)
                <p><strong>Suas observações:</strong> {{ $specialOrder->customer_notes }}</p>
            @endif
            
            <p>Se tiver alguma dúvida, não hesite em nos contactar.</p>
            
            <p style="text-align: center;">
                <a href="{{ url('/cliente/pedidos') }}" class="btn">Ver Meus Pedidos</a>
            </p>
        </div>
        
        <div class="footer">
            <p>Obrigado por escolher a <strong>Livraria CRM</strong>!</p>
            <p>📞 (+244) 923-456-789 | ✉️ contato@livraria-crm.com</p>
            <p style="font-size: 12px; color: #999;">
                Este é um email automático. Por favor, não responda diretamente.
            </p>
        </div>
    </div>
</body>
</html>
