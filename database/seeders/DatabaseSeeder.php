<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Executar os seeders na ordem correta para respeitar as dependências
        $this->call([
            // 1. Usuários do sistema
            AdminUserSeeder::class,
            // 2. Categorias de livros
            BookCategorySeeder::class,
            // 3. Clientes
            CustomerSeeder::class,
            // 4. Livros (depende de categorias)
            BookSeeder::class,
            // 5. Faturas (depende de clientes, livros e usuários)
            InvoiceSeeder::class,
            // 6. Pedidos Especiais (depende de clientes, livros e usuários)
            SpecialOrderSeeder::class,
            // 7. Usuários (depende de usuários)
            UserSeeder::class,
            // 8. Coupons (depende de usuários)
            CouponSeeder::class,
        ]);

        $this->command->info('Banco de dados populado com sucesso!');
        $this->command->info('Usuário admin criado: admin@crm-livraria.com / senha: admin123');
    }
}
