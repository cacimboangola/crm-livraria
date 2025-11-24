<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuários com diferentes papéis
        $this->createUsers();
    }

    /**
     * Criar usuários com diferentes papéis
     */
    private function createUsers(): void
    {
        $users = [
            [
                'name' => 'Gerente',
                'email' => 'gerente@crm-livraria.com',
                'password' => 'gerente123',
                'role' => 'manager',
                'phone' => '(+244) 923-456-789',
                'active' => true,
            ],
            [
                'name' => 'Caixa',
                'email' => 'caixa@crm-livraria.com',
                'password' => 'caixa123',
                'role' => 'cashier',
                'phone' => '(+244) 923-456-790',
                'active' => true,
            ],
            [
                'name' => 'Funcionário',
                'email' => 'funcionario@crm-livraria.com',
                'password' => 'funcionario123',
                'role' => 'employee',
                'phone' => '(+244) 923-456-791',
                'active' => true,
            ],
            [
                'name' => 'Cliente Teste',
                'email' => 'cliente@exemplo.com',
                'password' => 'cliente123',
                'role' => 'customer',
                'phone' => '(+244) 923-456-792',
                'active' => true,
            ],
        ];

        foreach ($users as $userData) {
            // Verificar se o usuário já existe
            if (!User::where('email', $userData['email'])->exists()) {
                // Criar usuário
                User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'role' => $userData['role'],
                    'phone' => $userData['phone'],
                    'active' => $userData['active'],
                ]);
                
                $this->command->info("Usuário {$userData['role']} criado: {$userData['email']}");
            } else {
                $this->command->info("Usuário {$userData['email']} já existe, pulando criação.");
            }
        }
    }
}
