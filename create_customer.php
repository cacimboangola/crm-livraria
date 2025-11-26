<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Buscar o usuário
$user = \App\Models\User::where('email', 'joao@teste.com')->first();

if ($user) {
    // Verificar se já existe um cliente
    $existingCustomer = \App\Models\Customer::where('email', $user->email)->first();
    
    if (!$existingCustomer) {
        // Criar o cliente
        $customer = \App\Models\Customer::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '923456789',
            'address' => 'Luanda, Angola',
            'city' => 'Luanda',
            'state' => 'LU',
            'postal_code' => '1000',
            'birth_date' => '1990-01-01',
        ]);
        
        echo "Cliente criado com sucesso! ID: " . $customer->id . "\n";
    } else {
        echo "Cliente já existe! ID: " . $existingCustomer->id . "\n";
    }
} else {
    echo "Usuário não encontrado!\n";
}
