<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\LoyaltyService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    protected $bookService;
    protected $loyaltyService;

    public function __construct(BookService $bookService, LoyaltyService $loyaltyService)
    {
        $this->bookService = $bookService;
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Obter todas as faturas com paginação
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Invoice::with(['customer', 'user']);

        // Aplicar filtros
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('invoice_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('invoice_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Obter fatura por ID
     *
     * @param int $id
     * @return Invoice|null
     */
    public function getById(int $id): ?Invoice
    {
        return Invoice::with(['customer', 'user', 'items.book'])
            ->find($id);
    }

    /**
     * Criar nova fatura
     *
     * @param array $data
     * @param array $items
     * @return Invoice
     */
    public function create(array $data, array $items): Invoice
    {
        return DB::transaction(function () use ($data, $items) {
            // Calcular totais
            $subtotal = 0;
            $taxAmount = 0;
            $discount = $data['discount'] ?? 0;

            // Criar a fatura
            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $data['customer_id'],
                'user_id' => $data['user_id'],
                'invoice_date' => $data['invoice_date'] ?? Carbon::now()->format('Y-m-d'),
                'due_date' => $data['due_date'] ?? null,
                'subtotal' => 0,  // Será atualizado após adicionar os itens
                'tax_amount' => 0,  // Será atualizado após adicionar os itens
                'discount' => $discount,
                'total' => 0,  // Será atualizado após adicionar os itens
                'status' => $data['status'] ?? 'draft',
                'payment_method' => $data['payment_method'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Adicionar itens à fatura
            foreach ($items as $item) {
                $book = Book::findOrFail($item['book_id']);

                $unitPrice = $item['unit_price'] ?? $book->price;
                $quantity = $item['quantity'];
                $itemDiscount = $item['discount'] ?? 0;
                $taxRate = $item['tax_rate'] ?? 23.0;  // Taxa de IVA padrão em Portugal

                $itemSubtotal = $unitPrice * $quantity;
                $itemTaxAmount = ($itemSubtotal - $itemDiscount) * ($taxRate / 100);
                $itemTotal = $itemSubtotal - $itemDiscount + $itemTaxAmount;

                // Criar o item da fatura
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'book_id' => $book->id,
                    'description' => $item['description'] ?? $book->title,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $itemTaxAmount,
                    'discount' => $itemDiscount,
                    'subtotal' => $itemSubtotal,
                    'total' => $itemTotal,
                ]);

                // Atualizar estoque do livro
                if ($data['status'] !== 'draft' && $data['status'] !== 'cancelled') {
                    $this->bookService->updateStock($book->id, -$quantity);
                }

                // Acumular totais
                $subtotal += $itemSubtotal;
                $taxAmount += $itemTaxAmount;
            }

            // Calcular total final
            $total = $subtotal - $discount + $taxAmount;

            // Atualizar totais da fatura
            $invoice->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
            ]);

            return $invoice->fresh(['customer', 'user', 'items.book']);
        });
    }

    /**
     * Atualizar fatura existente
     *
     * @param int $id
     * @param array $data
     * @param array|null $items
     * @return Invoice|null
     */
    public function update(int $id, array $data, ?array $items = null): ?Invoice
    {
        $invoice = $this->getById($id);

        if (!$invoice) {
            return null;
        }

        return DB::transaction(function () use ($invoice, $data, $items) {
            // Atualizar dados básicos da fatura
            $invoice->update([
                'customer_id' => $data['customer_id'] ?? $invoice->customer_id,
                'invoice_date' => $data['invoice_date'] ?? $invoice->invoice_date,
                'due_date' => $data['due_date'] ?? $invoice->due_date,
                'discount' => $data['discount'] ?? $invoice->discount,
                'status' => $data['status'] ?? $invoice->status,
                'payment_method' => $data['payment_method'] ?? $invoice->payment_method,
                'notes' => $data['notes'] ?? $invoice->notes,
            ]);

            // Se houver novos itens, atualizar os itens da fatura
            if ($items !== null) {
                // Reverter estoque para itens existentes
                if ($invoice->status !== 'draft' && $invoice->status !== 'cancelled') {
                    foreach ($invoice->items as $item) {
                        $this->bookService->updateStock($item->book_id, $item->quantity);
                    }
                }

                // Remover itens existentes
                $invoice->items()->delete();

                // Recalcular totais
                $subtotal = 0;
                $taxAmount = 0;
                $discount = $invoice->discount;

                // Adicionar novos itens
                foreach ($items as $item) {
                    $book = Book::findOrFail($item['book_id']);

                    $unitPrice = $item['unit_price'] ?? $book->price;
                    $quantity = $item['quantity'];
                    $itemDiscount = $item['discount'] ?? 0;
                    $taxRate = $item['tax_rate'] ?? 23.0;  // Taxa de IVA padrão em Portugal

                    $itemSubtotal = $unitPrice * $quantity;
                    $itemTaxAmount = ($itemSubtotal - $itemDiscount) * ($taxRate / 100);
                    $itemTotal = $itemSubtotal - $itemDiscount + $itemTaxAmount;

                    // Criar o item da fatura
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'book_id' => $book->id,
                        'description' => $item['description'] ?? $book->title,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'tax_rate' => $taxRate,
                        'tax_amount' => $itemTaxAmount,
                        'discount' => $itemDiscount,
                        'subtotal' => $itemSubtotal,
                        'total' => $itemTotal,
                    ]);

                    // Atualizar estoque do livro
                    if ($invoice->status !== 'draft' && $invoice->status !== 'cancelled') {
                        $this->bookService->updateStock($book->id, -$quantity);
                    }

                    // Acumular totais
                    $subtotal += $itemSubtotal;
                    $taxAmount += $itemTaxAmount;
                }

                // Calcular total final
                $total = $subtotal - $discount + $taxAmount;

                // Atualizar totais da fatura
                $invoice->update([
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total' => $total,
                ]);
            }

            return $invoice->fresh(['customer', 'user', 'items.book']);
        });
    }

    /**
     * Excluir fatura
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $invoice = $this->getById($id);

        if ($invoice) {
            // Se a fatura não for rascunho ou cancelada, reverter o estoque
            if ($invoice->status !== 'draft' && $invoice->status !== 'cancelled') {
                foreach ($invoice->items as $item) {
                    $this->bookService->updateStock($item->book_id, $item->quantity);
                }
            }

            return $invoice->delete();
        }

        return false;
    }

    /**
     * Alterar status da fatura
     *
     * @param int $id
     * @param string $status
     * @return Invoice|null
     */
    public function changeStatus(int $id, string $status): ?Invoice
    {
        $invoice = $this->getById($id);

        if ($invoice) {
            $oldStatus = $invoice->status;

            // Atualizar status
            $invoice->status = $status;
            $invoice->save();

            // Se mudar de rascunho/cancelado para outro status, atualizar estoque
            if (($oldStatus === 'draft' || $oldStatus === 'cancelled') &&
                    ($status !== 'draft' && $status !== 'cancelled')) {
                foreach ($invoice->items as $item) {
                    $this->bookService->updateStock($item->book_id, -$item->quantity);
                }
            }

            // Se mudar para cancelado, reverter estoque
            if ($status === 'cancelled' &&
                    ($oldStatus !== 'draft' && $oldStatus !== 'cancelled')) {
                foreach ($invoice->items as $item) {
                    $this->bookService->updateStock($item->book_id, $item->quantity);
                }
            }

            // Se a fatura foi paga, adicionar pontos de fidelidade
            if ($status === 'paid' && $oldStatus !== 'paid') {
                $this->addLoyaltyPoints($invoice);
            }

            // Se a fatura foi cancelada e estava paga, remover pontos de fidelidade
            if ($status === 'cancelled' && $oldStatus === 'paid') {
                $this->removeLoyaltyPoints($invoice);
            }

            return $invoice->fresh();
        }

        return null;
    }

    /**
     * Adicionar pontos de fidelidade para o cliente da fatura
     *
     * @param Invoice $invoice
     * @return void
     */
    protected function addLoyaltyPoints(Invoice $invoice): void
    {
        // Calcular pontos com base no valor total da fatura
        // Regra: 1 ponto para cada 10 unidades monetárias gastas
        $points = floor($invoice->total / 10);

        // Garantir que pelo menos 1 ponto seja adicionado
        if ($points < 1) {
            $points = 1;
        }

        // Adicionar pontos ao cliente
        if ($points > 0) {
            $this->loyaltyService->addPoints(
                $invoice->customer_id,
                $points,
                'Compra - Fatura #' . $invoice->invoice_number,
                $invoice
            );
        }
    }

    /**
     * Remover pontos de fidelidade do cliente da fatura
     *
     * @param Invoice $invoice
     * @return void
     */
    protected function removeLoyaltyPoints(Invoice $invoice): void
    {
        // Buscar transação de pontos relacionada a esta fatura
        $transaction = DB::table('loyalty_transactions')
            ->where('customer_id', $invoice->customer_id)
            ->where('invoice_id', $invoice->id)
            ->where('type', 'earn')
            ->where('points', '>', 0)
            ->first();

        // Se encontrou a transação, reverter os pontos
        if ($transaction) {
            $this->loyaltyService->addPoints(
                $invoice->customer_id,
                -$transaction->points,
                'Estorno - Fatura #' . $invoice->invoice_number . ' cancelada',
                $invoice
            );
        }
    }

    /**
     * Buscar faturas
     *
     * @param string $term
     * @return Collection
     */
    public function search(string $term): Collection
    {
        return Invoice::where('invoice_number', 'like', "%{$term}%")
            ->orWhereHas('customer', function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%");
            })
            ->with(['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obter faturas por cliente
     *
     * @param int $customerId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCustomer(int $customerId, int $perPage = 10): LengthAwarePaginator
    {
        return Invoice::where('customer_id', $customerId)
            ->with(['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Gerar número de fatura
     *
     * @return string
     */
    protected function generateInvoiceNumber(): string
    {
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
        $year = Carbon::now()->format('Y');

        return "FAT-{$year}-" . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}
