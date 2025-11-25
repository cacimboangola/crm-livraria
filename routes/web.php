<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignTrackingController;
use App\Http\Controllers\LoyaltyController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\SpecialOrderController;

// Rota pública inicial
Route::get('/', function () {
    return redirect()->route('login');
});

// Rotas de autenticação
use Illuminate\Support\Facades\Auth;

// Aplicar middleware guest para rotas de login e registro
Route::middleware(['guest'])->group(function () {
    // Login routes
    Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
    
    // Registration routes
    Route::get('register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
    
    // Password reset routes
    Route::get('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

// Rota de logout (requer autenticação)
Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {
    // Dashboard (apenas para admins)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')->middleware(\App\Http\Middleware\AdminMiddleware::class);
    
    // Perfil de usuário
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserController::class, 'changePassword'])->name('profile.password');
    
    // Rotas para clientes
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    
    // Rotas para categorias de livros
    Route::resource('book-categories', BookCategoryController::class);
    Route::get('/book-categories/search', [BookCategoryController::class, 'search'])->name('book-categories.search');
    
    // Rotas para livros
    Route::resource('books', BookController::class);
    Route::get('/books/search', [BookController::class, 'search'])->name('books.search');
    Route::get('/books/category/{categoryId}', [BookController::class, 'byCategory'])->name('books.by-category');
    Route::put('/books/{book}/stock', [BookController::class, 'updateStock'])->name('books.update-stock');
    
    // Rotas para faturas
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/search', [InvoiceController::class, 'search'])->name('invoices.search');
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'changeStatus'])->name('invoices.update-status');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'show'])->name('invoices.print');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
    Route::post('/invoices/{invoice}/email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    
    // Rotas para notificações
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/clear/read', [NotificationController::class, 'destroyRead'])->name('clear-read');
        Route::get('/unread', [NotificationController::class, 'getUnreadNotifications'])->name('unread');
    });
    
    // Rotas para recomendações
    Route::prefix('recommendations')->name('recommendations.')->group(function () {
        Route::get('/popular', [RecommendationController::class, 'popularBooks'])->name('popular');
        Route::get('/customer/{customer}', [RecommendationController::class, 'forCustomer'])->name('customer');
        Route::get('/book/{book}/similar', [RecommendationController::class, 'similarBooks'])->name('similar');
        Route::get('/book/{book}/potential-customers', [RecommendationController::class, 'potentialCustomers'])->name('potential-customers');
    });
    
    // Rotas para gerenciamento de usuários (apenas admin)
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::resource('users', UserController::class);
        
        // Rotas para campanhas de marketing
        Route::resource('campaigns', CampaignController::class);
        Route::get('/campaigns/{campaign}/select-customers', [CampaignController::class, 'selectCustomers'])->name('campaigns.select-customers');
        Route::post('/campaigns/{campaign}/add-customers', [CampaignController::class, 'addCustomers'])->name('campaigns.add-customers');
        Route::post('/campaigns/{campaign}/remove-customers', [CampaignController::class, 'removeCustomers'])->name('campaigns.remove-customers');
        Route::post('/campaigns/{campaign}/auto-select-customers', [CampaignController::class, 'autoSelectCustomers'])->name('campaigns.auto-select-customers');
        Route::post('/campaigns/{campaign}/activate', [CampaignController::class, 'activate'])->name('campaigns.activate');
        Route::post('/campaigns/{campaign}/cancel', [CampaignController::class, 'cancel'])->name('campaigns.cancel');
        Route::post('/campaigns/{campaign}/complete', [CampaignController::class, 'complete'])->name('campaigns.complete');
        Route::post('/campaigns/{campaign}/send-emails', [CampaignController::class, 'sendEmails'])->name('campaigns.send-emails');
        Route::get('/campaigns/{campaign}/metrics', [CampaignController::class, 'metrics'])->name('campaigns.metrics');
        Route::post('/campaigns/{campaign}/distribute-points', [CampaignController::class, 'distributePoints'])->name('campaigns.distribute-points');
        
        // Rotas para administração do sistema de fidelidade
        Route::get('/loyalty/admin', [LoyaltyController::class, 'adminDashboard'])->name('loyalty.admin');
        Route::post('/loyalty/expiration', [LoyaltyController::class, 'processExpiration'])->name('loyalty.process-expiration');
        
        // Rotas para cupons de desconto
        Route::resource('coupons', CouponController::class);
        Route::patch('/coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
        Route::get('/coupons-generate-code', [CouponController::class, 'generateCode'])->name('coupons.generate-code');
        
        // Rotas para pedidos especiais
        Route::resource('special-orders', SpecialOrderController::class);
        Route::patch('/special-orders/{special_order}/advance-status', [SpecialOrderController::class, 'advanceStatus'])->name('special-orders.advance-status');
        Route::patch('/special-orders/{special_order}/cancel', [SpecialOrderController::class, 'cancel'])->name('special-orders.cancel');
    });
    
    // Rotas para o sistema de fidelidade (acessível a todos usuários autenticados)
    Route::prefix('loyalty')->name('loyalty.')->group(function () {
        Route::get('/customers/{customer}', [LoyaltyController::class, 'customerDashboard'])->name('dashboard');
        Route::get('/customers/{customer}/transactions', [LoyaltyController::class, 'transactionHistory'])->name('transactions');
        Route::get('/customers/{customer}/add-points', [LoyaltyController::class, 'showAddPointsForm'])->name('add-points.form');
        Route::post('/customers/{customer}/add-points', [LoyaltyController::class, 'addPoints'])->name('add-points');
        Route::get('/customers/{customer}/redeem-points', [LoyaltyController::class, 'showRedeemPointsForm'])->name('redeem-points.form');
        Route::post('/customers/{customer}/redeem-points', [LoyaltyController::class, 'redeemPoints'])->name('redeem-points');
    });
    
});

// Rotas públicas para rastreamento de campanhas (não requerem autenticação)
Route::get('/track/open/{campaignId}/{customerId}/{token}', [CampaignTrackingController::class, 'trackOpen'])->name('campaigns.track-open');
Route::get('/track/click/{campaignId}/{customerId}/{token}', [CampaignTrackingController::class, 'trackClick'])->name('campaigns.track-click');
Route::get('/track/conversion/{campaignId}/{customerId}/{token}', [CampaignTrackingController::class, 'trackConversion'])->name('campaigns.track-conversion');

// Rotas públicas do catálogo de livros
Route::get('/catalogo', [\App\Http\Controllers\Customer\CustomerPortalController::class, 'catalog'])->name('customer.catalog');
Route::get('/livro/{book}', [\App\Http\Controllers\Customer\CustomerPortalController::class, 'bookDetails'])->name('customer.book.details');

// Rotas do portal do cliente (requerem autenticação como cliente)
Route::middleware(['auth', \App\Http\Middleware\CustomerMiddleware::class])->prefix('cliente')->group(function () {
    // Rotas para dashboard e perfil
    Route::get('/dashboard', [\App\Http\Controllers\Customer\CustomerPortalController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/perfil', [\App\Http\Controllers\Customer\CustomerPortalController::class, 'viewProfile'])->name('customer.profile');
    Route::get('/perfil/editar', [\App\Http\Controllers\Customer\CustomerPortalController::class, 'editProfile'])->name('customer.profile.edit');
    Route::put('/perfil/atualizar', [\App\Http\Controllers\Customer\CustomerPortalController::class, 'updateProfile'])->name('customer.profile.update');
    
    // Rotas do carrinho de compras
    Route::post('/carrinho/adicionar', [\App\Http\Controllers\Customer\CartController::class, 'add'])->name('customer.cart.add');
    Route::get('/carrinho', [\App\Http\Controllers\Customer\CartController::class, 'show'])->name('customer.cart');
    Route::post('/carrinho/atualizar', [\App\Http\Controllers\Customer\CartController::class, 'update'])->name('customer.cart.update');
    Route::post('/carrinho/remover', [\App\Http\Controllers\Customer\CartController::class, 'remove'])->name('customer.cart.remove');
    Route::post('/carrinho/cupom', [\App\Http\Controllers\Customer\CartController::class, 'applyCoupon'])->name('customer.cart.apply-coupon');
    Route::delete('/carrinho/cupom', [\App\Http\Controllers\Customer\CartController::class, 'removeCoupon'])->name('customer.cart.remove-coupon');
    
    // Rotas de checkout
    Route::post('/checkout', [\App\Http\Controllers\Customer\CheckoutController::class, 'process'])->name('customer.checkout');
    Route::post('/pedido/{id}/pagar', [\App\Http\Controllers\Customer\CheckoutController::class, 'markAsPaid'])->name('customer.order.pay');
    Route::post('/pedido/{id}/cancelar', [\App\Http\Controllers\Customer\CheckoutController::class, 'cancel'])->name('customer.order.cancel');
    
    // Rotas de pedidos/faturas
    Route::get('/pedidos', [\App\Http\Controllers\Customer\CustomerPortalController::class, 'orders'])->name('customer.orders');
    Route::get('/pedido/{id}', [\App\Http\Controllers\Customer\CustomerPortalController::class, 'orderDetails'])->name('customer.order.details');
    Route::get('/pedido/{id}/pdf', [\App\Http\Controllers\Customer\CustomerPortalController::class, 'orderPdf'])->name('customer.order.pdf');
    
    // Rotas de pontos de fidelidade
    Route::get('/fidelidade', [\App\Http\Controllers\Customer\CustomerPortalController::class, 'loyalty'])->name('customer.loyalty');
});

// Rota da API do Chatbot
Route::post('/api/chatbot', [\App\Http\Controllers\Api\ChatbotController::class, 'processMessage']);
