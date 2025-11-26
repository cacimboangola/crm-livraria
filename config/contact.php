<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Informações de Contato da Livraria
    |--------------------------------------------------------------------------
    |
    | Configurações centralizadas para todas as informações de contato
    | da livraria, incluindo telefone, email, WhatsApp e endereço.
    |
    */

    'phone' => [
        'number' => '+244923456789',
        'display' => '(+244) 923-456-789',
        'formatted' => '244923456789', // Para WhatsApp (sem símbolos)
    ],

    'email' => [
        'general' => 'contato@livraria-crm.com',
        'support' => 'atendimento@livraria-crm.com',
        'sales' => 'vendas@livraria-crm.com',
    ],

    'whatsapp' => [
        'number' => '244923456789', // Sem símbolos para API
        'display' => '(+244) 923-456-789',
        'messages' => [
            'general' => 'Olá! Gostaria de mais informações sobre os livros.',
            'chatbot' => 'Olá! Vim através do chatbot da Livraria CRM e gostaria de mais informações sobre os livros.',
            'support' => 'Olá! Preciso de ajuda com meu pedido.',
            'catalog' => 'Olá! Gostaria de saber sobre os livros disponíveis.',
        ],
    ],

    'address' => [
        'street' => 'Rua dos Livros, 123',
        'city' => 'Luanda',
        'country' => 'Angola',
        'full' => 'Rua dos Livros, 123, Luanda, Angola',
    ],

    'business_hours' => [
        'weekdays' => 'Seg-Sex: 8h-18h',
        'weekend' => 'Sáb: 9h-13h',
        'closed' => 'Dom: Fechado',
        'display' => 'Seg-Sex: 8h-18h',
    ],

    'social_media' => [
        'facebook' => '#',
        'instagram' => '#',
        'twitter' => '#',
        'youtube' => '#',
        'whatsapp' => 'https://wa.me/244923456789',
    ],
];
