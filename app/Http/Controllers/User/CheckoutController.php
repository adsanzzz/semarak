<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends \App\Http\Controllers\Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:qris,cod',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong');
        }

        // Hitung total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }

        // Simpan order
        $order = Order::create([
            'buyer_id' => Auth::id(),
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'payment_status' => 'unpaid',
        ]);

        // Kosongkan cart
        session()->forget('cart');

        // Jika QRIS → ke halaman upload bukti
        if ($request->payment_method === 'qris') {
            return redirect()->route('payment.qris', $order->id);
        }

        // Jika COD → langsung sukses
        return redirect()->route('checkout.success');
    }
}