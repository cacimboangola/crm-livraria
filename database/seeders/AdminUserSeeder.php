<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se o usuário já existe
        if (!User::where('email', 'admin@crm-livraria.com')->exists()) {
            // Criar usuário administrador para testes
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@crm-livraria.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '(11) 99999-9999',
                'active' => true,
            ]);
            
            $this->command->info('Usuário administrador criado com sucesso!');
        } else {
            $this->command->info('Usuário administrador já existe, pulando criação.');
        }
        
        $this->command->info('Email: admin@crm-livraria.com');
        $this->command->info('Senha: admin123');
    }
}
