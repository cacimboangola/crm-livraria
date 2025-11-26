<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\SpecialOrder;
use App\Models\User;
use App\Mail\SpecialOrderReady;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SpecialOrderController extends Controller
{
    /**
     * Exibir lista de pedidos especiais.
     */
    public function index(Request $request)
    {
        $query = SpecialOrder::with(['customer', 'user']);

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por busca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('book_title', 'like', "%{$search}%")
                  ->orWhere('book_author', 'like', "%{$search}%")
                  ->orWhere('book_isbn', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $specialOrders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Contadores para dashboard
        $counts = [
            'pending' => SpecialOrder::where('status', 'pending')->count(),
            'ordered' => SpecialOrder::where('status', 'ordered')->count(),
            'received' => SpecialOrder::where('status', 'received')->count(),
            'total_active' => SpecialOrder::active()->count(),
        ];

        return view('special-orders.index', compact('specialOrders', 'counts'));
    }

    /**
     * Mostrar formulário para criar novo pedido especial.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('special-orders.create', compact('customers'));
    }

    /**
     * Armazenar novo pedido especial.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'book_title' => 'required|string|max:255',
            'book_author' => 'nullable|string|max:255',
            'book_isbn' => 'nullable|string|max:20',
            'book_publisher' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'estimated_price' => 'nullable|numeric|min:0',
            'customer_notes' => 'nullable|string',
            'delivery_preference' => 'required|in:pickup,delivery',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = SpecialOrder::STATUS_PENDING;

        $specialOrder = SpecialOrder::create($validated);

        // Notificar administradores sobre novo pedido especial
        $this->notifyAdmins($specialOrder, 'new_order');

        return redirect()
            ->route('special-orders.show', $specialOrder)
            ->with('success', 'Pedido especial criado com sucesso!');
    }

    /**
     * Exibir detalhes de um pedido especial.
     */
    public function show(SpecialOrder $specialOrder)
    {
        $specialOrder->load(['customer', 'user']);
        return view('special-orders.show', compact('specialOrder'));
    }

    /**
     * Mostrar formulário para editar pedido especial.
     */
    public function edit(SpecialOrder $specialOrder)
    {
        if (!$specialOrder->canAdvanceStatus() && $specialOrder->status !== SpecialOrder::STATUS_CANCELLED) {
            return redirect()
                ->route('special-orders.show', $specialOrder)
                ->with('error', 'Este pedido não pode mais ser editado.');
        }

        $customers = Customer::orderBy('name')->get();
        return view('special-orders.edit', compact('specialOrder', 'customers'));
    }

    /**
     * Atualizar pedido especial.
     */
    public function update(Request $request, SpecialOrder $specialOrder)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'book_title' => 'required|string|max:255',
            'book_author' => 'nullable|string|max:255',
            'book_isbn' => 'nullable|string|max:20',
            'book_publisher' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'estimated_price' => 'nullable|numeric|min:0',
            'customer_notes' => 'nullable|string',
            'supplier_notes' => 'nullable|string',
            'delivery_preference' => 'required|in:pickup,delivery',
        ]);

        $specialOrder->update($validated);

        return redirect()
            ->route('special-orders.show', $specialOrder)
            ->with('success', 'Pedido especial atualizado com sucesso!');
    }

    /**
     * Remover pedido especial.
     */
    public function destroy(SpecialOrder $specialOrder)
    {
        if (!$specialOrder->canBeCancelled()) {
            return redirect()
                ->route('special-orders.index')
                ->with('error', 'Este pedido não pode ser removido.');
        }

        $specialOrder->delete();

        return redirect()
            ->route('special-orders.index')
            ->with('success', 'Pedido especial removido com sucesso!');
    }

    /**
     * Avançar status do pedido.
     */
    public function advanceStatus(SpecialOrder $specialOrder)
    {
        if (!$specialOrder->canAdvanceStatus()) {
            return redirect()
                ->route('special-orders.show', $specialOrder)
                ->with('error', 'Este pedido não pode avançar de status.');
        }

        $nextStatus = $specialOrder->next_status;
        $specialOrder->status = $nextStatus;

        // Atualizar timestamp correspondente
        switch ($nextStatus) {
            case SpecialOrder::STATUS_ORDERED:
                $specialOrder->ordered_at = now();
                break;
            case SpecialOrder::STATUS_RECEIVED:
                $specialOrder->received_at = now();
                break;
            case SpecialOrder::STATUS_NOTIFIED:
                $specialOrder->notified_at = now();
                $this->notifyCustomer($specialOrder);
                break;
            case SpecialOrder::STATUS_DELIVERED:
                $specialOrder->delivered_at = now();
                break;
        }

        $specialOrder->save();

        // Notificar o cliente sobre a mudança de status
        $this->notifyCustomerStatusChange($specialOrder, $nextStatus);

        return redirect()
            ->route('special-orders.show', $specialOrder)
            ->with('success', 'Status atualizado para: ' . $specialOrder->status_formatted);
    }

    /**
     * Cancelar pedido especial.
     */
    public function cancel(SpecialOrder $specialOrder)
    {
        if (!$specialOrder->canBeCancelled()) {
            return redirect()
                ->route('special-orders.show', $specialOrder)
                ->with('error', 'Este pedido não pode ser cancelado.');
        }

        $specialOrder->status = SpecialOrder::STATUS_CANCELLED;
        $specialOrder->save();

        return redirect()
            ->route('special-orders.show', $specialOrder)
            ->with('success', 'Pedido especial cancelado.');
    }

    /**
     * Notificar administradores sobre novo pedido ou atualização.
     */
    protected function notifyAdmins(SpecialOrder $specialOrder, string $type): void
    {
        $admins = User::where('role', 'admin')->get();

        $title = match ($type) {
            'new_order' => 'Novo Pedido Especial',
            'status_change' => 'Pedido Especial Atualizado',
            default => 'Pedido Especial',
        };

        $message = match ($type) {
            'new_order' => "Novo pedido especial: {$specialOrder->book_title} para {$specialOrder->customer->name}",
            'status_change' => "Pedido #{$specialOrder->id} atualizado para: {$specialOrder->status_formatted}",
            default => "Pedido especial #{$specialOrder->id}",
        };

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'sender_id' => Auth::id(),
                'type' => 'special_order',
                'title' => $title,
                'message' => $message,
                'link' => route('special-orders.show', $specialOrder),
                'read' => false,
            ]);
        }
    }

    /**
     * Notificar cliente que o livro está disponível.
     */
    protected function notifyCustomer(SpecialOrder $specialOrder): void
    {
        $customer = $specialOrder->customer;

        // Criar notificação no sistema (se cliente tiver usuário)
        $user = User::where('email', $customer->email)->first();
        if ($user) {
            Notification::create([
                'user_id' => $user->id,
                'sender_id' => Auth::id(),
                'type' => 'special_order_ready',
                'title' => 'Seu Livro Chegou!',
                'message' => "O livro \"{$specialOrder->book_title}\" que você encomendou está disponível para " . 
                            ($specialOrder->delivery_preference === 'pickup' ? 'retirada na loja' : 'entrega'),
                'link' => route('customer.orders'),
                'read' => false,
            ]);
        }

        // Enviar email
        try {
            Mail::to($customer->email)->send(new SpecialOrderReady($specialOrder));
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de pedido especial: ' . $e->getMessage(), [
                'special_order_id' => $specialOrder->id,
                'customer_email' => $customer->email,
            ]);
        }
    }

    /**
     * Notificar cliente sobre mudança de status
     */
    protected function notifyCustomerStatusChange(SpecialOrder $specialOrder, string $newStatus): void
    {
        $customer = $specialOrder->customer;
        
        // Criar notificação no sistema (se cliente tiver usuário)
        $user = User::where('email', $customer->email)->first();
        if ($user) {
            $statusMessages = [
                SpecialOrder::STATUS_ORDERED => [
                    'title' => 'Pedido Especial Encomendado! 📦',
                    'message' => "Seu pedido especial \"{$specialOrder->book_title}\" foi encomendado ao fornecedor. Você será notificado quando chegar!"
                ],
                SpecialOrder::STATUS_RECEIVED => [
                    'title' => 'Livro Chegou na Loja! ✅',
                    'message' => "O livro \"{$specialOrder->book_title}\" chegou em nossa loja e está sendo preparado para você."
                ],
                SpecialOrder::STATUS_NOTIFIED => [
                    'title' => 'Seu Livro Está Pronto! 🎉',
                    'message' => "O livro \"{$specialOrder->book_title}\" está pronto para " . 
                                ($specialOrder->delivery_preference === 'pickup' ? 'retirada na loja' : 'entrega') . "!"
                ],
                SpecialOrder::STATUS_DELIVERED => [
                    'title' => 'Pedido Especial Concluído! 🎊',
                    'message' => "Seu pedido especial \"{$specialOrder->book_title}\" foi " . 
                                ($specialOrder->delivery_preference === 'pickup' ? 'retirado' : 'entregue') . " com sucesso!"
                ]
            ];

            if (isset($statusMessages[$newStatus])) {
                Notification::create([
                    'user_id' => $user->id,
                    'sender_id' => Auth::id(),
                    'type' => 'special_order_status',
                    'title' => $statusMessages[$newStatus]['title'],
                    'message' => $statusMessages[$newStatus]['message'],
                    'link' => route('customer.special-orders.show', $specialOrder->id),
                    'read' => false,
                ]);
            }
        }

        // Enviar email de notificação (opcional)
        try {
            if ($newStatus === SpecialOrder::STATUS_NOTIFIED) {
                Mail::to($customer->email)->send(new SpecialOrderReady($specialOrder));
            }
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de notificação de status: ' . $e->getMessage(), [
                'special_order_id' => $specialOrder->id,
                'customer_email' => $customer->email,
                'new_status' => $newStatus,
            ]);
        }
    }
}
