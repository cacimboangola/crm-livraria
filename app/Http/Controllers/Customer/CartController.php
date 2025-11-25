<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Coupon;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Adiciona um livro ao carrinho
     */
    public function add(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $book = Book::findOrFail($request->book_id);
        
        // Verificar estoque
        if ($book->stock < $request->quantity) {
            return back()->with('error', 'Quantidade solicitada não disponível em estoque.');
        }

        // Inicializar o carrinho se não existir
        $cart = Session::get('cart', []);
        
        // Adicionar ou atualizar item no carrinho
        if (isset($cart[$book->id])) {
            $cart[$book->id]['quantity'] += $request->quantity;
        } else {
            $cart[$book->id] = [
                'title' => $book->title,
                'price' => $book->discount > 0 
                    ? $book->price * (1 - $book->discount/100) 
                    : $book->price,
                'original_price' => $book->price,
                'quantity' => $request->quantity,
                'cover' => $book->cover_image,
                'author' => $book->author,
            ];
        }
        
        Session::put('cart', $cart);
        
        return redirect()->route('customer.cart')->with('success', 'Livro adicionado ao carrinho!');
    }
    
    /**
     * Exibe o carrinho de compras
     */
    public function show()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        // Dados do cupom aplicado
        $couponData = Session::get('coupon');
        $discount = 0;
        $coupon = null;
        
        if ($couponData) {
            $coupon = Coupon::find($couponData['id']);
            if ($coupon && $coupon->isValid()) {
                $discount = $coupon->calculateDiscount($total);
            } else {
                // Cupom inválido, remover da sessão
                Session::forget('coupon');
                $couponData = null;
            }
        }
        
        $finalTotal = $total - $discount;
        
        return view('customer.cart', compact('cart', 'total', 'discount', 'finalTotal', 'couponData'));
    }
    
    /**
     * Atualiza o carrinho de compras
     */
    public function update(Request $request)
    {
        // Limpar carrinho
        if ($request->has('clear_cart')) {
            Session::forget('cart');
            return redirect()->route('customer.cart')->with('success', 'Carrinho esvaziado com sucesso!');
        }
        
        // Atualizar quantidade
        if ($request->has('book_id') && $request->has('quantity')) {
            $cart = Session::get('cart', []);
            $bookId = $request->book_id;
            
            if (isset($cart[$bookId])) {
                // Verificar estoque
                $book = Book::findOrFail($bookId);
                if ($book->stock < $request->quantity) {
                    return back()->with('error', 'Quantidade solicitada não disponível em estoque.');
                }
                
                // Atualizar quantidade
                $cart[$bookId]['quantity'] = max(1, $request->quantity);
                Session::put('cart', $cart);
                return redirect()->route('customer.cart')->with('success', 'Carrinho atualizado!');
            }
        }
        
        return redirect()->route('customer.cart');
    }
    
    /**
     * Remove um item do carrinho
     */
    public function remove(Request $request)
    {
        $request->validate([
            'book_id' => 'required',
        ]);
        
        $cart = Session::get('cart', []);
        
        if (isset($cart[$request->book_id])) {
            unset($cart[$request->book_id]);
            Session::put('cart', $cart);
        }
        
        return redirect()->route('customer.cart')->with('success', 'Item removido do carrinho!');
    }

    /**
     * Aplica um cupom de desconto ao carrinho
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim($request->coupon_code));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return back()->with('coupon_error', 'Cupom não encontrado.');
        }

        if (!$coupon->isValid()) {
            return back()->with('coupon_error', 'Este cupom não está mais válido.');
        }

        // Verificar se o cliente pode usar o cupom
        $user = auth()->user();
        if ($user && $user->customer) {
            if (!$coupon->canBeUsedByCustomer($user->customer)) {
                return back()->with('coupon_error', 'Você já atingiu o limite de uso deste cupom.');
            }
        }

        // Calcular o total do carrinho
        $cart = Session::get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Verificar valor mínimo
        if ($coupon->min_order_value && $total < $coupon->min_order_value) {
            return back()->with('coupon_error', 'Valor mínimo do pedido: Kz ' . number_format($coupon->min_order_value, 2, ',', '.'));
        }

        // Salvar cupom na sessão
        Session::put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);

        return back()->with('coupon_success', 'Cupom aplicado com sucesso!');
    }

    /**
     * Remove o cupom aplicado
     */
    public function removeCoupon()
    {
        Session::forget('coupon');
        return back()->with('success', 'Cupom removido.');
    }
}
