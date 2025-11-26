<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SpecialOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpecialOrderController extends Controller
{
    /**
     * Exibir pedidos especiais do cliente
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return redirect()
                ->route('customer.dashboard')
                ->with('error', 'Perfil de cliente não encontrado.');
        }

        $query = SpecialOrder::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc');

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $specialOrders = $query->paginate(10);

        // Contadores para dashboard do cliente
        $counts = [
            'total' => SpecialOrder::where('customer_id', $customer->id)->count(),
            'pending' => SpecialOrder::where('customer_id', $customer->id)->where('status', 'pending')->count(),
            'active' => SpecialOrder::where('customer_id', $customer->id)->active()->count(),
            'delivered' => SpecialOrder::where('customer_id', $customer->id)->where('status', 'delivered')->count(),
        ];

        return view('customer.special-orders.index', compact('specialOrders', 'counts'));
    }

    /**
     * Exibir detalhes de um pedido especial
     */
    public function show($id)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return redirect()
                ->route('customer.dashboard')
                ->with('error', 'Perfil de cliente não encontrado.');
        }

        $specialOrder = SpecialOrder::where('customer_id', $customer->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('customer.special-orders.show', compact('specialOrder'));
    }

    /**
     * Cancelar pedido especial (apenas se permitido)
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return redirect()
                ->route('customer.dashboard')
                ->with('error', 'Perfil de cliente não encontrado.');
        }

        $specialOrder = SpecialOrder::where('customer_id', $customer->id)
            ->where('id', $id)
            ->firstOrFail();

        if (!$specialOrder->canBeCancelled()) {
            return redirect()
                ->route('customer.special-orders.show', $specialOrder)
                ->with('error', 'Este pedido não pode mais ser cancelado.');
        }

        $specialOrder->status = SpecialOrder::STATUS_CANCELLED;
        $specialOrder->save();

        return redirect()
            ->route('customer.special-orders.index')
            ->with('success', 'Pedido especial cancelado com sucesso.');
    }
}
