<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Processa as mensagens do chatbot
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processMessage(Request $request)
    {
        $message = $request->input('message');
        $response = $this->generateResponse($message);
        
        return response()->json($response);
    }

    /**
     * Gera uma resposta com base na mensagem do usuário
     *
     * @param  string  $message
     * @return array
     */
    private function generateResponse($message)
    {
        // Converter para minúsculas para facilitar a comparação
        $messageLower = mb_strtolower($message, 'UTF-8');
        
        // Verificar se é uma saudação
        if ($this->isGreeting($messageLower)) {
            return [
                'message' => 'Olá! Como posso ajudar você hoje?',
                'options' => [
                    'Buscar livros',
                    'Meus pedidos',
                    'Pontos de fidelidade',
                    'Falar com atendente'
                ]
            ];
        }
        
        // Verificar se é sobre busca de livros
        if ($this->containsAny($messageLower, ['livro', 'livros', 'buscar', 'procurar', 'encontrar', 'busca'])) {
            return $this->handleBookSearch($messageLower);
        }
        
        // Verificar se é sobre pedidos
        if ($this->containsAny($messageLower, ['pedido', 'pedidos', 'compra', 'compras', 'encomenda', 'fatura'])) {
            return $this->handleOrderQuery();
        }
        
        // Verificar se é sobre pontos de fidelidade
        if ($this->containsAny($messageLower, ['ponto', 'pontos', 'fidelidade', 'programa', 'recompensa'])) {
            return $this->handleLoyaltyQuery();
        }
        
        // Verificar se quer falar com atendente
        if ($this->containsAny($messageLower, ['atendente', 'pessoa', 'humano', 'ajuda', 'suporte', 'falar'])) {
            return [
                'message' => 'Entendo que você prefere falar com um atendente humano. Por favor, entre em contato pelo telefone (244) 923-456-789 ou pelo email atendimento@livraria-angola.com durante nosso horário comercial (8h às 18h).',
                'options' => [
                    'Voltar ao menu',
                    'Buscar livros',
                    'Meus pedidos'
                ]
            ];
        }
        
        // Resposta padrão para mensagens não reconhecidas
        return [
            'message' => 'Desculpe, não entendi sua pergunta. Como posso ajudar você?',
            'options' => [
                'Buscar livros',
                'Meus pedidos',
                'Pontos de fidelidade',
                'Falar com atendente'
            ]
        ];
    }

    /**
     * Verifica se a mensagem é uma saudação
     *
     * @param  string  $message
     * @return bool
     */
    private function isGreeting($message)
    {
        $greetings = ['oi', 'olá', 'ola', 'bom dia', 'boa tarde', 'boa noite', 'ei', 'hey', 'hi', 'hello'];
        
        foreach ($greetings as $greeting) {
            if (strpos($message, $greeting) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Verifica se a mensagem contém alguma das palavras-chave
     *
     * @param  string  $message
     * @param  array  $keywords
     * @return bool
     */
    private function containsAny($message, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strpos($message, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Processa consultas sobre livros
     *
     * @param  string  $message
     * @return array
     */
    private function handleBookSearch($message)
    {
        // Extrair termos de busca (remover palavras comuns)
        $searchTerms = $this->extractSearchTerms($message);
        
        if (empty($searchTerms)) {
            return [
                'message' => 'O que você gostaria de buscar? Você pode digitar o título, autor ou categoria do livro.',
                'options' => [
                    'Livros mais vendidos',
                    'Novos lançamentos',
                    'Promoções'
                ]
            ];
        }
        
        // Buscar livros com base nos termos
        $books = Book::where(function ($query) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $query->orWhere('title', 'like', "%{$term}%")
                      ->orWhere('author', 'like', "%{$term}%")
                      ->orWhereHas('category', function ($q) use ($term) {
                          $q->where('name', 'like', "%{$term}%");
                      });
            }
        })
        ->limit(3)
        ->get();
        
        if ($books->isEmpty()) {
            return [
                'message' => "Não encontrei livros correspondentes à sua busca. Tente outros termos ou categorias.",
                'options' => [
                    'Ver categorias',
                    'Buscar por autor',
                    'Falar com atendente'
                ]
            ];
        }
        
        $bookList = $books->map(function ($book) {
            return "- {$book->title} por {$book->author} - Kz {$book->price}";
        })->join("\n");
        
        return [
            'message' => "Encontrei estes livros para você:\n{$bookList}\n\nGostaria de mais informações sobre algum deles?",
            'options' => [
                'Ver mais livros',
                'Buscar outro livro',
                'Ver categorias'
            ]
        ];
    }

    /**
     * Extrai termos de busca da mensagem
     *
     * @param  string  $message
     * @return array
     */
    private function extractSearchTerms($message)
    {
        // Palavras a ignorar
        $ignoreWords = [
            'livro', 'livros', 'buscar', 'procurar', 'encontrar', 'sobre', 'como', 
            'quero', 'gostaria', 'pode', 'por', 'favor', 'me', 'ajudar', 'busca'
        ];
        
        // Dividir a mensagem em palavras
        $words = explode(' ', $message);
        
        // Filtrar palavras ignoradas e palavras muito curtas
        $terms = array_filter($words, function ($word) use ($ignoreWords) {
            return !in_array($word, $ignoreWords) && strlen($word) > 2;
        });
        
        return array_values($terms); // Reindexar o array
    }

    /**
     * Processa consultas sobre pedidos
     *
     * @return array
     */
    private function handleOrderQuery()
    {
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            return [
                'message' => 'Para verificar seus pedidos, você precisa estar logado. Por favor, faça login na sua conta.',
                'options' => [
                    'Como fazer login?',
                    'Voltar ao menu',
                    'Falar com atendente'
                ]
            ];
        }
        
        // Obter o cliente associado ao usuário
        $customer = Customer::where('email', Auth::user()->email)->first();
        
        if (!$customer) {
            return [
                'message' => 'Não encontrei um perfil de cliente associado à sua conta. Por favor, complete seu perfil para acessar seus pedidos.',
                'options' => [
                    'Como completar meu perfil?',
                    'Voltar ao menu',
                    'Falar com atendente'
                ]
            ];
        }
        
        // Buscar os pedidos recentes
        $recentOrders = Invoice::where('customer_id', $customer->id)
            ->orderBy('invoice_date', 'desc')
            ->limit(3)
            ->get();
        
        if ($recentOrders->isEmpty()) {
            return [
                'message' => 'Você ainda não possui pedidos registrados em nosso sistema.',
                'options' => [
                    'Ver livros disponíveis',
                    'Como fazer um pedido?',
                    'Voltar ao menu'
                ]
            ];
        }
        
        $orderList = $recentOrders->map(function ($order) {
            $status = $order->status;
            $statusText = '';
            
            switch ($status) {
                case 'paid':
                    $statusText = 'Pago';
                    break;
                case 'pending':
                    $statusText = 'Pendente';
                    break;
                case 'cancelled':
                    $statusText = 'Cancelado';
                    break;
                case 'delivered':
                    $statusText = 'Entregue';
                    break;
                default:
                    $statusText = $status;
            }
            
            return "- Pedido #{$order->id} ({$order->invoice_date->format('d/m/Y')}) - Kz {$order->total_amount} - Status: {$statusText}";
        })->join("\n");
        
        return [
            'message' => "Aqui estão seus pedidos mais recentes:\n{$orderList}\n\nGostaria de ver mais detalhes de algum pedido específico?",
            'options' => [
                'Ver todos os pedidos',
                'Status de entrega',
                'Voltar ao menu'
            ]
        ];
    }

    /**
     * Processa consultas sobre pontos de fidelidade
     *
     * @return array
     */
    private function handleLoyaltyQuery()
    {
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            return [
                'message' => 'Para verificar seus pontos de fidelidade, você precisa estar logado. Por favor, faça login na sua conta.',
                'options' => [
                    'Como fazer login?',
                    'Voltar ao menu',
                    'Falar com atendente'
                ]
            ];
        }
        
        // Obter o cliente associado ao usuário
        $customer = Customer::where('email', Auth::user()->email)->first();
        
        if (!$customer) {
            return [
                'message' => 'Não encontrei um perfil de cliente associado à sua conta. Por favor, complete seu perfil para acessar o programa de fidelidade.',
                'options' => [
                    'Como completar meu perfil?',
                    'Voltar ao menu',
                    'Falar com atendente'
                ]
            ];
        }
        
        // Buscar os pontos de fidelidade
        $loyaltyPoints = $this->loyaltyService->getCustomerPoints($customer->id);
        
        return [
            'message' => "Você possui {$loyaltyPoints->current_balance} pontos de fidelidade disponíveis.\nSeu nível atual é: " . ucfirst($loyaltyPoints->level) . ".\n\nContinue comprando para acumular mais pontos e subir de nível!",
            'options' => [
                'Como ganhar mais pontos?',
                'Benefícios do programa',
                'Ver histórico de pontos'
            ]
        ];
    }
}
