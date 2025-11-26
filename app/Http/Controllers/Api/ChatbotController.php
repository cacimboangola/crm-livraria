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

        // Verificar intenções específicas primeiro (mais precisas)

        // 1. Pedidos específicos (prioridade alta)
        if ($this->containsAny($messageLower, ['meus pedidos', 'meu pedido', 'minhas compras', 'minha compra', 'histórico de pedidos', 'status do pedido'])) {
            return $this->handleOrderQuery();
        }

        // 2. Pontos de fidelidade específicos (prioridade alta)
        if ($this->containsAny($messageLower, ['meus pontos', 'pontos de fidelidade', 'programa de fidelidade', 'saldo de pontos', 'quantos pontos'])) {
            return $this->handleLoyaltyQuery();
        }

        // 3. Busca de livros específica (prioridade alta)
        if ($this->containsAny($messageLower, ['buscar livro', 'procurar livro', 'encontrar livro', 'quero um livro', 'livro de', 'livros de'])) {
            return $this->handleBookSearch($messageLower);
        }

        // 4. Atendimento humano específico (prioridade alta)
        if ($this->containsAny($messageLower, ['falar com atendente', 'atendente humano', 'pessoa real', 'suporte técnico', 'preciso de ajuda'])) {
            return [
                'message' => 'Entendo que você prefere falar com um atendente humano. Escolha uma das opções de contato:

📞 Telefone: ' . config('contact.phone.display') . '
📧 Email: ' . config('contact.email.general') . '  
💬 WhatsApp: ' . config('contact.whatsapp.display') . '

Horário de atendimento: ' . config('contact.business_hours.display'),
                'options' => [
                    'Abrir WhatsApp',
                    'Voltar ao menu',
                    'Buscar livros'
                ]
            ];
        }

        // Verificações mais amplas (prioridade média)

        // 5. Busca geral de livros
        if ($this->containsAny($messageLower, ['livro', 'livros', 'autor', 'categoria', 'ficção', 'romance', 'fantasia', 'biografia', 'história', 'infantil', 'negócios', 'autoajuda'])) {
            return $this->handleBookSearch($messageLower);
        }

        // 6. Pedidos gerais
        if ($this->containsAny($messageLower, ['pedido', 'compra', 'encomenda', 'fatura', 'ordem'])) {
            return $this->handleOrderQuery();
        }

        // 7. Fidelidade geral
        if ($this->containsAny($messageLower, ['ponto', 'pontos', 'fidelidade', 'recompensa', 'desconto'])) {
            return $this->handleLoyaltyQuery();
        }

        // 8. Ajuda geral (prioridade baixa)
        if ($this->containsAny($messageLower, ['ajuda', 'como', 'o que', 'onde', 'quando'])) {
            return [
                'message' => 'Posso ajudar você com várias coisas! Escolha uma das opções abaixo:',
                'options' => [
                    'Buscar livros',
                    'Consultar pedidos',
                    'Ver pontos de fidelidade',
                    'Falar com atendente'
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
        $greetings = [
            'oi', 'olá', 'ola', 'oie', 'opa',
            'bom dia', 'boa tarde', 'boa noite', 'bom tarde',
            'ei', 'hey', 'hi', 'hello', 'hola',
            'tchau', 'até logo', 'até mais', 'bye', 'adeus',
            'obrigado', 'obrigada', 'valeu', 'thanks'
        ];

        // Verificar se a mensagem é apenas uma saudação (sem outras intenções)
        $messageWords = explode(' ', trim($message));

        foreach ($greetings as $greeting) {
            if (strpos($message, $greeting) !== false) {
                // Se a mensagem é curta (até 3 palavras) e contém saudação, é uma saudação
                if (count($messageWords) <= 3) {
                    return true;
                }
                // Se a saudação está no início da mensagem
                if (strpos($message, $greeting) === 0) {
                    return true;
                }
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
                $query
                    ->orWhere('title', 'like', "%{$term}%")
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
                'message' => 'Não encontrei livros correspondentes à sua busca. Tente outros termos ou categorias.',
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
        // Palavras a ignorar (expandida e melhorada)
        $ignoreWords = [
            'livro', 'livros', 'buscar', 'procurar', 'encontrar', 'sobre', 'como',
            'quero', 'gostaria', 'pode', 'por', 'favor', 'me', 'ajudar', 'busca',
            'um', 'uma', 'de', 'do', 'da', 'dos', 'das', 'para', 'com', 'em',
            'que', 'qual', 'onde', 'quando', 'porque', 'ver', 'mostrar', 'listar'
        ];

        // Mapeamento de sinônimos para categorias
        $categoryMappings = [
            'ficção' => 'ficção científica',
            'sci-fi' => 'ficção científica',
            'scifi' => 'ficção científica',
            'fantasia' => 'fantasia',
            'romance' => 'romance',
            'romântico' => 'romance',
            'romântica' => 'romance',
            'biografia' => 'biografia',
            'biográfico' => 'biografia',
            'história' => 'história',
            'histórico' => 'história',
            'infantil' => 'infantil',
            'criança' => 'infantil',
            'crianças' => 'infantil',
            'negócios' => 'negócios',
            'business' => 'negócios',
            'empresarial' => 'negócios',
            'autoajuda' => 'autoajuda',
            'auto-ajuda' => 'autoajuda',
            'desenvolvimento' => 'autoajuda'
        ];

        // Dividir a mensagem em palavras
        $words = explode(' ', $message);

        // Filtrar palavras ignoradas e palavras muito curtas
        $terms = array_filter($words, function ($word) use ($ignoreWords) {
            return !in_array($word, $ignoreWords) && strlen($word) > 2;
        });

        // Aplicar mapeamento de sinônimos
        $mappedTerms = array_map(function ($term) use ($categoryMappings) {
            return isset($categoryMappings[$term]) ? $categoryMappings[$term] : $term;
        }, $terms);

        return array_values(array_unique($mappedTerms));  // Reindexar e remover duplicatas
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
