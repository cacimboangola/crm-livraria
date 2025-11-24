<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se existem clientes, livros e usuários
        $customers = Customer::all();
        $books = Book::all();
        $users = User::all();
        
        if ($customers->isEmpty()) {
            $this->command->error('Nenhum cliente encontrado. Execute o CustomerSeeder primeiro.');
            return;
        }
        
        if ($books->isEmpty()) {
            $this->command->error('Nenhum livro encontrado. Execute o BookSeeder primeiro.');
            return;
        }
        
        if ($users->isEmpty()) {
            $this->command->error('Nenhum usuário encontrado. Execute o AdminUserSeeder primeiro.');
            return;
        }
        
        // Criar 15 faturas de exemplo
        for ($i = 0; $i < 15; $i++) {
            // Selecionar um cliente aleatório
            $customer = $customers->random();
            // Selecionar um usuário aleatório (ou o primeiro se só tiver um)
            $user = $users->count() > 1 ? $users->random() : $users->first();
            
            // Definir datas
            $invoiceDate = Carbon::now()->subDays(rand(1, 60));
            $dueDate = $invoiceDate->copy()->addDays(15);
            
            // Definir status aleatório
            $statuses = ['paid', 'pending', 'draft', 'cancelled'];
            $statusWeights = [60, 20, 15, 5]; // Probabilidade de cada status
            $status = $this->weightedRandom($statuses, $statusWeights);
            
            // Se for pago, definir data de pagamento
            $paymentDate = $status === 'paid' ? $invoiceDate->copy()->addDays(rand(1, 10)) : null;
            
            // Criar a fatura
            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'user_id' => $user->id,
                'invoice_number' => 'FAT-' . date('Y') . '-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'payment_date' => $paymentDate,
                'status' => $status,
                'subtotal' => 0, // Será calculado depois
                'discount' => rand(0, 15), // Desconto aleatório entre 0 e 15%
                'tax_amount' => rand(0, 10), // Taxa aleatória entre 0 e 10%
                'total' => 0, // Será calculado depois
                'notes' => rand(0, 1) ? 'Observações de teste para a fatura #' . ($i + 1) : null,
            ]);
            
            // Adicionar itens à fatura (entre 1 e 5 itens)
            $itemCount = rand(1, 5);
            $subtotal = 0;
            
            // Garantir que não adicionamos o mesmo livro duas vezes
            $usedBooks = [];
            
            for ($j = 0; $j < $itemCount; $j++) {
                // Selecionar um livro aleatório que ainda não foi usado
                $availableBooks = $books->whereNotIn('id', $usedBooks);
                
                if ($availableBooks->isEmpty()) {
                    break; // Não há mais livros disponíveis
                }
                
                $book = $availableBooks->random();
                $usedBooks[] = $book->id;
                
                // Quantidade aleatória entre 1 e 3
                $quantity = rand(1, 3);
                
                // Criar o item da fatura
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'book_id' => $book->id,
                    'description' => $book->title,
                    'quantity' => $quantity,
                    'unit_price' => $book->price,
                    'tax_rate' => 23.00,
                    'tax_amount' => $quantity * $book->price * 0.23,
                    'discount' => 0,
                    'subtotal' => $quantity * $book->price,
                    'total' => $quantity * $book->price,
                ]);
                
                $subtotal += $quantity * $book->price;
            }
            
            // Atualizar os valores da fatura
            $discountAmount = $subtotal * ($invoice->discount / 100);
            $taxAmount = ($subtotal - $discountAmount) * ($invoice->tax_amount / 100);
            $totalAmount = $subtotal - $discountAmount + $taxAmount;
            
            $invoice->update([
                'subtotal' => $subtotal,
                'total' => $totalAmount,
            ]);
        }
        
        $this->command->info('Faturas criadas com sucesso!');
    }
    
    /**
     * Seleciona um item aleatório com base em pesos.
     *
     * @param array $items
     * @param array $weights
     * @return mixed
     */
    private function weightedRandom(array $items, array $weights)
    {
        $totalWeight = array_sum($weights);
        $randomWeight = mt_rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($items as $index => $item) {
            $currentWeight += $weights[$index];
            if ($randomWeight <= $currentWeight) {
                return $item;
            }
        }
        
        return $items[0]; // Fallback
    }
}
