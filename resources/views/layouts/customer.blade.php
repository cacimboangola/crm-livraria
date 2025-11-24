<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'CRM Livraria') }} - Portal do Cliente</title>
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/chatbot.js') }}" defer></script>
    
    <!-- Bootstrap CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --text-dark: #2c3e50;
            --text-light: #ecf0f1;
            --text-muted: #7f8c8d;
            --bg-light: #f8f9fa;
            --bg-white: #ffffff;
            --border-light: #dee2e6;
            --shadow-sm: 0 2px 4px rgba(44, 62, 80, 0.1);
            --shadow-md: 0 4px 12px rgba(44, 62, 80, 0.15);
            --shadow-lg: 0 8px 25px rgba(44, 62, 80, 0.2);
            --gradient-primary: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            --gradient-accent: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            --gradient-light: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
            --border-radius-sm: 8px;
            --border-radius-md: 12px;
            --border-radius-lg: 16px;
            --border-radius-xl: 20px;
            --transition-fast: 0.2s ease;
            --transition-normal: 0.3s ease;
        }

        .navbar-modern {
            background: var(--gradient-primary) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 1rem 0;
            box-shadow: var(--shadow-md);
        }

        .navbar-brand-modern {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--text-light) !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: var(--transition-normal);
        }

        .navbar-brand-modern:hover {
            color: var(--accent-color) !important;
            transform: translateY(-1px);
        }

        .nav-link-modern {
            color: var(--text-light) !important;
            font-weight: 500;
            padding: 0.75rem 1.25rem !important;
            border-radius: var(--border-radius-sm);
            transition: var(--transition-normal);
            position: relative;
            margin: 0 0.25rem;
        }

        .nav-link-modern:hover {
            color: var(--accent-color) !important;
            background: rgba(255,255,255,0.1);
            transform: translateY(-1px);
        }

        .nav-link-modern.active {
            background: var(--gradient-accent);
            color: white !important;
            box-shadow: var(--shadow-sm);
        }

        .cart-icon-modern {
            position: relative;
            background: var(--gradient-accent);
            border-radius: var(--border-radius-md);
            padding: 0.75rem 1rem !important;
            margin-left: 0.5rem;
            transition: var(--transition-normal);
        }

        .cart-icon-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
        }

        .cart-count-modern {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--bg-white);
            color: var(--accent-color);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid var(--accent-color);
        }

        .dropdown-menu-modern {
            background: var(--bg-white);
            border: none;
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-lg);
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .dropdown-item-modern {
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1rem;
            transition: var(--transition-fast);
            color: var(--text-dark);
            font-weight: 500;
        }

        .dropdown-item-modern:hover {
            background: var(--bg-light);
            color: var(--accent-color);
            transform: translateX(4px);
        }

        .navbar-toggler-modern {
            border: none;
            padding: 0.5rem;
            border-radius: var(--border-radius-sm);
            background: rgba(255,255,255,0.1);
        }

        .navbar-toggler-modern:focus {
            box-shadow: none;
        }

        .footer-modern {
            background: var(--gradient-primary);
            color: var(--text-light);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }

        .footer-modern h5 {
            color: var(--accent-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .footer-modern a {
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition-normal);
        }

        .footer-modern a:hover {
            color: var(--accent-color);
            transform: translateX(4px);
        }

        .alert-modern {
            border: none;
            border-radius: var(--border-radius-md);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success-modern {
            background: linear-gradient(135deg, rgba(39, 174, 96, 0.1) 0%, rgba(39, 174, 96, 0.05) 100%);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .alert-danger-modern {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.1) 0%, rgba(231, 76, 60, 0.05) 100%);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .alert-info-modern {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(52, 152, 219, 0.05) 100%);
            color: var(--accent-color);
            border-left: 4px solid var(--accent-color);
        }

        .btn-close-modern {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: inherit;
            opacity: 0.7;
            transition: var(--transition-fast);
        }

        .btn-close-modern:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        /* Global Button Styles */
        .btn {
            border-radius: var(--border-radius-md);
            font-weight: 500;
            transition: var(--transition-normal);
            border: none;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--gradient-accent);
            color: white;
        }

        .btn-primary:hover {
            background: var(--gradient-accent);
            box-shadow: var(--shadow-md);
            filter: brightness(1.1);
        }

        .btn-outline-primary {
            border: 2px solid var(--accent-color);
            color: var(--accent-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--accent-color);
            color: white;
            border-color: var(--accent-color);
        }

        /* Global Card Styles */
        .card {
            border: none;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            transition: var(--transition-normal);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        /* Global Form Styles */
        .form-control {
            border-radius: var(--border-radius-sm);
            border: 2px solid var(--border-light);
            transition: var(--transition-fast);
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-modern">
        <div class="container">
            <a class="navbar-brand-modern" href="{{ route('customer.catalog') }}">
                <i class="fas fa-book-reader me-2"></i>Livraria CRM
            </a>
            <button class="navbar-toggler navbar-toggler-modern" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->routeIs('customer.catalog') ? 'active' : '' }}" href="{{ route('customer.catalog') }}">
                            <i class="fas fa-books me-2"></i>Catálogo
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link nav-link-modern {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Painel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-modern {{ request()->routeIs('customer.orders*') ? 'active' : '' }}" href="{{ route('customer.orders') }}">
                                <i class="fas fa-receipt me-2"></i>Pedidos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-modern {{ request()->routeIs('customer.loyalty') ? 'active' : '' }}" href="{{ route('customer.loyalty') }}">
                                <i class="fas fa-star me-2"></i>Fidelidade
                            </a>
                        </li>
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link cart-icon-modern" href="{{ route('customer.cart') }}">
                                <i class="fas fa-shopping-bag"></i>
                                @if(session('cart') && count(session('cart')) > 0)
                                    <span class="cart-count-modern">{{ count(session('cart')) }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-link-modern dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-2"></i>{{ explode(' ', Auth::user()->name)[0] }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item dropdown-item-modern" href="{{ route('customer.profile') }}">
                                    <i class="fas fa-user-edit me-2"></i>Meu Perfil
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item dropdown-item-modern" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link nav-link-modern" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-2"></i>Entrar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-modern" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-2"></i>Cadastrar
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-modern alert-success-modern alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-modern" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-modern alert-danger-modern alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close btn-close-modern" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-modern alert-info-modern alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close btn-close-modern" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="footer-modern">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-book-reader me-2"></i>Livraria CRM</h5>
                    <p class="text-muted">Sua livraria online com os melhores títulos e preços. Descubra mundos incríveis através da leitura.</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="text-muted fs-5"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-link me-2"></i>Links Úteis</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('customer.catalog') }}"><i class="fas fa-books me-2"></i>Catálogo</a></li>
                        <li class="mb-2"><a href="#"><i class="fas fa-info-circle me-2"></i>Sobre Nós</a></li>
                        <li class="mb-2"><a href="#"><i class="fas fa-shield-alt me-2"></i>Política de Privacidade</a></li>
                        <li class="mb-2"><a href="#"><i class="fas fa-file-contract me-2"></i>Termos de Uso</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-phone me-2"></i>Contato</h5>
                    <div class="contact-info">
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <span>Rua dos Livros, 123<br>Luanda, Angola</span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <span>(+244) 923-456-789</span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <span>contato@livraria-crm.com</span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-clock me-2"></i>
                            <span>Seg-Sex: 8h-18h</span>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center">
                <p class="mb-0 text-muted">&copy; {{ date('Y') }} Livraria CRM. Todos os direitos reservados. Desenvolvido com dedicação em Angola.</p>
            </div>
        </div>
    </footer>
</body>
</html>
