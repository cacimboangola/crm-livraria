<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\InvoiceService;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    protected $invoiceService;
    protected $loyaltyService;

    public function __construct(InvoiceService $invoiceService, LoyaltyService $loyaltyService)
    {
        $this->invoiceService = $invoiceService;
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Processa o checkout
     */
    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,debit_card,bank_transfer,multicaixa,cash',
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.catalog')->with('error', 'Seu carrinho está vazio!');
        }

        // Obter o cliente autenticado
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        // Criar cliente automaticamente se não existir
        if (!$customer) {
            return redirect()->route('customer.catalog')->with('error', 'Cliente não encontrado!');
        }

        try {
            DB::beginTransaction();

            // Criar a fatura
            $invoice = new Invoice();
            $invoice->invoice_number = 'INV-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT);
            $invoice->customer_id = $customer->id;
            $invoice->user_id = $user->id;
            $invoice->invoice_date = now();
            $invoice->due_date = now()->addDays(7);
            $invoice->subtotal = 0;
            $invoice->tax_amount = 0;
            $invoice->discount = 0;
            $invoice->status = 'pending';
            $invoice->payment_method = $request->payment_method;
            $invoice->notes = 'Pedido realizado pelo portal do cliente';
            $invoice->total = 0;  // Será atualizado após adicionar os itens
            $invoice->save();

            $total = 0;

            // Adicionar itens à fatura
            foreach ($cart as $id => $item) {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->book_id = $id;
                $invoiceItem->quantity = $item['quantity'];
                $invoiceItem->unit_price = $item['price'];
                $invoiceItem->description = $item['title'];
                $invoiceItem->tax_rate = 0;  // Sem taxa para Angola
                $invoiceItem->tax_amount = 0;
                $invoiceItem->discount = 0;
                $invoiceItem->subtotal = $item['price'] * $item['quantity'];
                $invoiceItem->total = $item['price'] * $item['quantity'];
                $invoiceItem->save();

                $total += $item['price'] * $item['quantity'];
            }

            // Atualizar o total da fatura
            $invoice->subtotal = $total;
            $invoice->total = $total;
            $invoice->save();

            DB::commit();

            // Limpar o carrinho
            Session::forget('cart');

            return redirect()
                ->route('customer.orders')
                ->with('success', 'Pedido realizado com sucesso! Número da fatura: ' . $invoice->invoice_number);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no checkout: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'cart' => $cart,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()
                ->route('customer.cart')
                ->with('error', 'Erro ao processar o pedido: ' . $e->getMessage());
        }
    }

    /**
     * Marca uma fatura como paga (simulação de pagamento)
     */
    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        // Verificar se a fatura pertence ao cliente autenticado
        if ($invoice->customer_id !== $customer->id) {
            return redirect()
                ->route('customer.orders')
                ->with('error', 'Você não tem permissão para acessar esta fatura.');
        }

        try {
            DB::beginTransaction();

            // Atualizar status da fatura
            $invoice->status = 'paid';
            $invoice->payment_date = now();
            $invoice->save();

            // Adicionar pontos de fidelidade
            $this->loyaltyService->addPointsFromInvoice($invoice);

            DB::commit();

            return redirect()
                ->route('customer.order.details', $invoice->id)
                ->with('success', 'Pagamento confirmado! Pontos de fidelidade adicionados.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('customer.order.details', $invoice->id)
                ->with('error', 'Erro ao processar o pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Cancela um pedido
     */
    public function cancel($id)
    {
        $invoice = Invoice::findOrFail($id);
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        // Verificar se a fatura pertence ao cliente autenticado
        if ($invoice->customer_id !== $customer->id) {
            return redirect()
                ->route('customer.orders')
                ->with('error', 'Você não tem permissão para acessar esta fatura.');
        }

        // Verificar se a fatura pode ser cancelada
        if ($invoice->status !== 'pending') {
            return redirect()
                ->route('customer.order.details', $invoice->id)
                ->with('error', 'Apenas pedidos pendentes podem ser cancelados.');
        }

        $invoice->status = 'cancelled';
        $invoice->save();

        return redirect()
            ->route('customer.order.details', $invoice->id)
            ->with('success', 'Pedido cancelado com sucesso.');
    }
}
