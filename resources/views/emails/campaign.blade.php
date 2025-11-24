<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->name }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            padding: 20px 0;
            border-top: 1px solid #eee;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 15px 0;
        }
        .customer-name {
            font-weight: bold;
        }
        .tracking-pixel {
            width: 1px;
            height: 1px;
            position: absolute;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo da Livraria" class="logo">
        <h1>{{ $campaign->name }}</h1>
    </div>
    
    <div class="content">
        <p>Olá <span class="customer-name">{{ $customer->name }}</span>,</p>
        
        {!! $content !!}
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Livraria CRM. Todos os direitos reservados.</p>
        <p>
            Você está recebendo este e-mail porque é cliente da nossa livraria.
            Se não deseja mais receber nossos e-mails, <a href="#">clique aqui para cancelar sua inscrição</a>.
        </p>
    </div>
    
    <!-- Pixel de rastreamento para saber quando o email foi aberto -->
    <img src="{{ $trackingPixel }}" alt="" class="tracking-pixel">
</body>
</html>
