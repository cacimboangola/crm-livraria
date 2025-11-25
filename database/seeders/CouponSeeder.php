<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'BEMVINDO10',
                'name' => 'Desconto de Boas-vindas',
                'description' => 'Cupom de 10% de desconto para novos clientes',
                'type' => 'percentage',
                'value' => 10,
                'min_order_value' => 5000,
                'max_discount' => 2000,
                'usage_limit' => null,
                'usage_limit_per_user' => 1,
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'LIVROS20',
                'name' => 'Super Desconto Livros',
                'description' => '20% de desconto em compras acima de Kz 10.000',
                'type' => 'percentage',
                'value' => 20,
                'min_order_value' => 10000,
                'max_discount' => 5000,
                'usage_limit' => 100,
                'usage_limit_per_user' => 2,
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'FRETE500',
                'name' => 'Desconto Fixo Frete',
                'description' => 'Kz 500 de desconto em qualquer compra',
                'type' => 'fixed',
                'value' => 500,
                'min_order_value' => 2000,
                'max_discount' => null,
                'usage_limit' => 50,
                'usage_limit_per_user' => 1,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'is_active' => true,
            ],
            [
                'code' => 'NATAL2025',
                'name' => 'Promoção de Natal',
                'description' => '15% de desconto para o Natal',
                'type' => 'percentage',
                'value' => 15,
                'min_order_value' => null,
                'max_discount' => 3000,
                'usage_limit' => null,
                'usage_limit_per_user' => null,
                'start_date' => now()->setMonth(12)->setDay(1),
                'end_date' => now()->setMonth(12)->setDay(31),
                'is_active' => true,
            ],
            [
                'code' => 'VIP1000',
                'name' => 'Desconto VIP',
                'description' => 'Kz 1.000 de desconto para clientes VIP',
                'type' => 'fixed',
                'value' => 1000,
                'min_order_value' => 5000,
                'max_discount' => null,
                'usage_limit' => 20,
                'usage_limit_per_user' => 1,
                'start_date' => now(),
                'end_date' => now()->addMonths(2),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
