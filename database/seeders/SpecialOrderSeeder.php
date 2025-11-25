<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\SpecialOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class SpecialOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $customers = Customer::take(5)->get();

        if (!$admin || $customers->isEmpty()) {
            $this->command->warn('Necessário ter pelo menos 1 admin e clientes para criar pedidos especiais.');
            return;
        }

        $specialOrders = [
            [
                'customer_id' => $customers[0]->id ?? 1,
                'user_id' => $admin->id,
                'book_title' => 'A Arte da Guerra',
                'book_author' => 'Sun Tzu',
                'book_isbn' => '978-85-359-0277-8',
                'book_publisher' => 'Companhia das Letras',
                'quantity' => 1,
                'estimated_price' => 4500.00,
                'customer_notes' => 'Edição especial com capa dura, se possível.',
                'status' => 'pending',
                'delivery_preference' => 'pickup',
            ],
            [
                'customer_id' => $customers[1]->id ?? 1,
                'user_id' => $admin->id,
                'book_title' => 'O Príncipe',
                'book_author' => 'Nicolau Maquiavel',
                'book_isbn' => '978-85-254-2789-1',
                'book_publisher' => 'Penguin',
                'quantity' => 2,
                'estimated_price' => 3200.00,
                'customer_notes' => 'Para presente de aniversário.',
                'status' => 'ordered',
                'ordered_at' => now()->subDays(5),
                'delivery_preference' => 'delivery',
            ],
            [
                'customer_id' => $customers[2]->id ?? 1,
                'user_id' => $admin->id,
                'book_title' => 'Sapiens: Uma Breve História da Humanidade',
                'book_author' => 'Yuval Noah Harari',
                'book_isbn' => '978-85-254-3214-7',
                'book_publisher' => 'L&PM',
                'quantity' => 1,
                'estimated_price' => 6800.00,
                'supplier_notes' => 'Encomendado na distribuidora ABC. Previsão: 7 dias.',
                'status' => 'received',
                'ordered_at' => now()->subDays(10),
                'received_at' => now()->subDays(2),
                'delivery_preference' => 'pickup',
            ],
            [
                'customer_id' => $customers[3]->id ?? 1,
                'user_id' => $admin->id,
                'book_title' => 'Meditações',
                'book_author' => 'Marco Aurélio',
                'book_isbn' => '978-85-7542-456-3',
                'book_publisher' => 'Edipro',
                'quantity' => 1,
                'estimated_price' => 2900.00,
                'status' => 'notified',
                'ordered_at' => now()->subDays(15),
                'received_at' => now()->subDays(5),
                'notified_at' => now()->subDays(4),
                'delivery_preference' => 'pickup',
            ],
            [
                'customer_id' => $customers[4]->id ?? 1,
                'user_id' => $admin->id,
                'book_title' => 'O Alquimista',
                'book_author' => 'Paulo Coelho',
                'book_isbn' => '978-85-325-2578-9',
                'book_publisher' => 'Rocco',
                'quantity' => 3,
                'estimated_price' => 4200.00,
                'customer_notes' => 'Edição comemorativa de 30 anos.',
                'status' => 'delivered',
                'ordered_at' => now()->subDays(20),
                'received_at' => now()->subDays(10),
                'notified_at' => now()->subDays(9),
                'delivered_at' => now()->subDays(7),
                'delivery_preference' => 'delivery',
            ],
        ];

        foreach ($specialOrders as $order) {
            SpecialOrder::create($order);
        }

        $this->command->info('Pedidos especiais de exemplo criados com sucesso!');
    }
}
