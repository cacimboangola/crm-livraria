<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_PT'); // Usando português de Portugal para se aproximar mais de Angola
        
        // Criar 20 clientes de teste
        for ($i = 0; $i < 20; $i++) {
            $name = $faker->name;
            $email = $faker->unique()->safeEmail;
            $password = Str::random(8);
            
            // Criar cliente
            $customer = Customer::create([
                'name' => $name,
                'email' => $email,
                'phone' => '(+244) ' . $faker->numerify('9##-###-###'), // Formato de telefone angolano
                'tax_id' => $faker->numerify('########' . $faker->randomElement(['LA', 'AO']) . '###'), // Formato NIF angolano
                'address' => $faker->streetAddress,
                'city' => $faker->randomElement(['Luanda', 'Benguela', 'Huambo', 'Lubango', 'Malanje', 'Namibe']), // Cidades angolanas
                'state' => $faker->randomElement(['Luanda', 'Benguela', 'Huambo', 'Huíla', 'Malanje', 'Namibe']), // Províncias angolanas
                'postal_code' => $faker->numerify('####'), // Código postal simplificado
                'birth_date' => $faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
                'notes' => $faker->optional(0.7)->paragraph,
                'active' => true,
            ]);
            
            // Criar usuário associado ao cliente
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'customer',
                'phone' => $customer->phone,
                'active' => true,
            ]);
            
            // Registrar senha para fins de teste
            $this->command->info("Cliente criado: {$email} | Senha: {$password}");
        }
        
        // Adicionar alguns clientes específicos para testes (adaptados para Angola)
        $specialCustomers = [
            [
                'name' => 'Maria Domingos',
                'email' => 'maria.domingos@example.com',
                'phone' => '(+244) 923-765-432',
                'tax_id' => '5001234LA056', // Formato NIF angolano
                'address' => 'Rua Comandante Gika, 150',
                'city' => 'Luanda',
                'state' => 'Luanda',
                'postal_code' => '1000',
                'birth_date' => '1985-05-15',
                'notes' => 'Cliente VIP, gosta de livros de ficção científica e romance.',
                'active' => true,
                'password' => 'cliente123',
            ],
            [
                'name' => 'João Baptista',
                'email' => 'joao.baptista@example.com',
                'phone' => '(+244) 912-876-543',
                'tax_id' => '5009876AO078',
                'address' => 'Avenida 4 de Fevereiro, 500',
                'city' => 'Luanda',
                'state' => 'Luanda',
                'postal_code' => '1250',
                'birth_date' => '1978-10-20',
                'notes' => 'Cliente regular, prefere livros de história e biografias.',
                'active' => true,
                'password' => 'cliente456',
            ],
            [
                'name' => 'Ana Francisco',
                'email' => 'ana.francisco@example.com',
                'phone' => '(+244) 931-765-432',
                'tax_id' => '5004567LA089',
                'address' => 'Rua Rainha Ginga, 200',
                'city' => 'Benguela',
                'state' => 'Benguela',
                'postal_code' => '2000',
                'birth_date' => '1990-03-25',
                'notes' => 'Cliente nova, interessada em livros de autoajuda e negócios.',
                'active' => true,
                'password' => 'cliente789',
            ],
        ];
        
        foreach ($specialCustomers as $customerData) {
            $password = $customerData['password'];
            unset($customerData['password']);
            
            // Criar cliente
            $customer = Customer::create($customerData);
            
            // Criar usuário associado
            User::create([
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'password' => Hash::make($password),
                'role' => 'customer',
                'phone' => $customerData['phone'],
                'active' => true,
            ]);
            
            $this->command->info("Cliente especial criado: {$customerData['email']} | Senha: {$password}");
        }
        
        $this->command->info('Clientes criados com sucesso!');
    }
}
